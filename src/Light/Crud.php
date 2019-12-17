<?php

declare(strict_types = 1);

namespace Light;

use Exception;
use MongoDB\BSON\Regex;

/**
 * Class Crud
 * @package Light
 */
abstract class Crud extends Controller
{
    /**
     * @var View
     */
    public $view = null;

    /**
     * @return string
     */
    public function getEntity()
    {
        $controllerClassPars = explode('\\', get_class($this));

        return end($controllerClassPars);
    }

    /**
     * @return string
     */
    public function getModelClassName()
    {
        $controllerClassPars = explode('\\', get_class($this));

        $entity = end($controllerClassPars);

        return implode('\\', [
            Front::getInstance()->getConfig()['light']['loader']['namespace'],
            'Model',
            $entity
        ]);
    }

    /**
     * @return string
     */
    public function getFormClassName()
    {
        $controllerClassPars = explode('\\', get_class($this));

        $controllerClassPars[count($controllerClassPars)-2] = 'Form';

        return implode('\\', $controllerClassPars);
    }

    /**
     * @return string
     */
    public function getButton()
    {
        return $this->button ?? false;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title ?? false;
    }

    /**
     * @return string
     */
    public function getPositioning()
    {
        return $this->positioning ?? false;
    }


    public function copy()
    {
        $this->getView()->setLayoutEnabled(false);

        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $record = $modelClassName::fetchOne([
            'id' => $this->getParam('id')
        ]);

        if (!$record) {
            throw new \Exception('Model was not found');
        }

        $data = [];

        foreach ($record->getMeta()->getProperties() as $property) {

            if ($property->getName() == 'id') {
                continue;
            }

            if (class_exists($property->getType())) {;

                if ($field = $record->{$property->getName()}) {
                    $data[$property->getName()] = $field->id;
                }
            }
            else {
                $data[$property->getName()] = $record->{$property->getName()};
            }
        }

        /** @var Model $newRecord */
        $newRecord = new $modelClassName();
        $newRecord->populate($data);
        $newRecord->save();
    }





    /**
     * @return array
     */
    public function getControls(): array
    {
        $controls = [
            ['type' => 'edit']
        ];

        $modelClassName = $this->getModelClassName();

        /** @var Model $model */
        $model = new $modelClassName();

        if ($model->getMeta()->hasProperty('enabled')) {
            $controls[] = ['type' => 'enabled'];
        }

        return $controls;
    }

    /**
     * @return array
     */
    public function getHeader()
    {
        return $this->header ?? [];
    }

    /**
     * @return array
     */
    public function getFilter()
    {
        return $this->filter ?? [];
    }

    /**
     * @return int
     */
    public function getItemsPerPage()
    {
        return 20;
    }

    /**
     * @return array
     */
    public function getExportHeader()
    {
        return $this->exportHeader ?? [];
    }

    /**
     * @return string
     */
    public function getExportFileName()
    {
        return $this->export ?? 'export';
    }

    /**
     * @return array
     */
    public function getFilterWithValues()
    {
        $filter = $this->getFilter();

        foreach ($filter as $index => $filterItem) {

            $filter[$index]['name'] = $filterItem['name'] ?? $filterItem['type'];

            $filter[$index]['value'] = $this->getRequest()->getGet('filter')[$filter[$index]['name']] ?? null;

            $controllerClassPars = explode('\\', get_class($this));

            $entity = end($controllerClassPars);

            $model = implode('\\', [
                Front::getInstance()->getConfig()['light']['loader']['namespace'],
                'Model',
                $filter[$index]['type']
            ]);

            if (class_exists($model, false)) {

                $filter[$index]['type'] = 'model';
                $cond = [];

                if (!empty($filter[$index]['cond'])) {
                    $cond = array_merge($cond, $filter[$index]['cond']);
                }

                $filter[$index]['model'] = $model::fetchAll($cond);
            }
        }

        return $filter;
    }

    /**
     * @return array
     */
    public function getConditions()
    {
        $conditions = [];

        foreach ($this->getFilterWithValues() as $filter) {

            if (empty($filter['value'])) {
                continue;
            }

            if ($filter['type'] == 'search') {

                if (count($filter['by']) > 1) {
                    foreach ($filter['by'] as $field) {
                        $conditions['$or'][] = [$field => new Regex(htmlspecialchars(quotemeta($filter['value'])), 'i')];
                    }
                }

                else {
                    $conditions[$filter['by'][0]] = new Regex(htmlspecialchars(quotemeta($filter['value'])), 'i');
                }
            }

            else if ($filter['type'] == 'model') {
                $conditions[$filter['by']] = $filter['value'];
            }

            else if ($filter['type'] == 'datetime') {
                $conditions[$filter['by']] = ['$gt' => strtotime($filter['value']['from']), '$lt' => strtotime($filter['value']['to'])];
            }

            else {
                $conditions[$filter['name']] = $filter['value'];
            }
        }

        return $conditions;
    }

    /**
     * @return array
     */
    public function getSorting()
    {
        $defaultSort = [];

        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $model = new $modelClassName();

        if ($model->getMeta()->hasProperty('position')) {
            $defaultSort = [
                'position' => 1
            ];
        }

        return array_merge($this->sort ?? $defaultSort, array_filter($this->getRequest()->getGet('sort', $defaultSort)));
    }

    /**
     * @return Zend_Paginator
     * @throws Zend_Paginator_Exception
     */
    public function getPaginator()
    {
        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        return $modelClassName::fetchAll(
            $this->getConditions(),
            $this->getSorting(),
            $this->getItemsPerPage(),
            ($this->getRequest()->getGet('page', 1) - 1) * $this->getItemsPerPage()
        );
    }

    public function index()
    {
        $this->view->setVars([

            'title' => $this->getTitle(),
            'button' => $this->getButton(),
            'positioning' => $this->getPositioning(),
            'positioningWithoutLanguage' => $this->positioningWithoutLanguage ?? false,
            'positioningCustom' => $this->positioningCustom ?? false,
            'export' => $this->export ?? false,

            'language' => $this->getRequest()->getGet('filter')['language'] ?? false,
            'filter' => $this->getFilterWithValues(),
            'header' => $this->getHeader(),
            'controls' => $this->getControls(),
            'paginator' => $this->getPaginator(),
            'controller' => $this->getRouter()->getController(),
            'page' => $this->getRequest()->getGet('page', 1)
        ]);

        if ($this->getRequest()->getGet('modal')) {
            return $this->view->render('table/ajax');
        }
        else {
            return $this->view->render('table/index');
        }
    }

    public function export()
    {
        $this->getView()->setLayoutEnabled(false);

        $response = [];

        $response[] = implode(',', array_keys($this->getExportHeader()));

        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $table = $modelClassName::fetchAll(
            $this->getConditions(),
            $this->getSorting()
        );

        foreach ($table as $row) {

            $cols = [];

            foreach ($this->getExportHeader() as $name => $struct) {

                $cols[] = $this->exportType($row->{$struct['field']}, $struct['type']);
            }

            $response[] = implode(',', $cols);
        }

        $fileName = $this->getExportFileName() . '_' . date('c') . '.csv';
        $this->getResponse()->setHeader('Content-Disposition', 'attachment;filename=' . $fileName);

        echo implode(";\n", $response);
    }

    public function manage()
    {
        $this->getView()->setAutoRender(false);

        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $formClassName = $this->getFormClassName();

        $model = $modelClassName::fetchObject([
            'id' => $this->getRequest()->getGet('id')
        ]);

        /** @var Form $form */
        $form = new $formClassName([
            'data' => $model
        ]);

        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getRequest()->getPostAll())) {

                $formData = $form->getValues();

                if (!(bool)$model->id && $model->getMeta()->hasProperty('language')) {

                    $languageModelClassName = implode('\\', [
                        Front::getInstance()->getConfig()['light']['loader']['namespace'],
                        'Model',
                        'Language'
                    ]);

                    foreach ($languageModelClassName::fetchAll() as $language) {

                        /** @var Model $languageRelatedModel */
                        $languageRelatedModel = new $modelClassName();

                        $languageRelatedModel->populate($formData);
                        $languageRelatedModel->language = $language;

                        if ($formData['language'] != $language->id && $model->getMeta()->hasProperty('language')) {
                            $languageRelatedModel->enabled = false;
                        }

                        $languageRelatedModel->save();

                        $this->didSave($languageRelatedModel);
                    }
                }
                else {

                    $model->populate($formData);
                    $model->save();

                    $this->didSave($model);
                }

                die('ok:'.$this->getRequest()->getPost('return-url'));
            }
        }

        $form->setReturnUrl(
            $this->getRouter()->assemble([
                'controller' => $this->getRouter()->getController(),
                'action' => 'index'
            ])
        );

        return $this->view->render('form/default', [
            'title' => $this->getTitle(),
            'form' => $form,
        ]);
    }

    /**
     * @param $value
     * @param $type
     * @return false|null|string
     */
    public function exportType($value, $type)
    {
        switch ($type) {

            case 'text':
                return $value;

            case 'date':
                return date('Y/m/d H:i:s', $value);
        }

        return null;
    }

    public function setEnabled()
    {
        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $record = $modelClassName::fetchOne([
            'id' => $this->getRequest()->getGet('id')
        ]);

        $record->enabled = $this->getRequest()->getGet('enabled');
        $record->save();
    }

    public function init()
    {
        parent::init();

        if ($this->getRequest()->isAjax()) {
            $this->getView()->setLayoutEnabled(false);
        }

        $this->getView()->setAutoRender(false);

        $this->view = new View();
        $this->view->setPath(__DIR__ . '/Crud');
    }

    public function didSave() {}
}