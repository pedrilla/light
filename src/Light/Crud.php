<?php

declare(strict_types = 1);

namespace Light;

use Exception;
use Light\Crud\AuthCrud;
use Light\Model\ModelInterface;
use MongoDB\BSON\Regex;

/**
 * Class Crud
 * @package Light
 */
abstract class Crud extends AuthCrud
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
     * @param mixed|null $model
     * @return Form|null
     */
    public function getForm($model = null)
    {
        /** @var Form $formClassName */
        $formClassName = $this->getFormClassName();

        if (class_exists($formClassName)) {
            return new $formClassName([
                'data' => $model
            ]);
        }

        return null;
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
     * @return bool
     */
    public function getPositioning()
    {
        return $this->positioning ?? false;
    }

    /**
     * @return false|string
     * @throws Exception
     */
    public function position()
    {
        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        /** @var Model $model */
        $model = new $modelClassName();

        if (!$model->getMeta()->hasProperty('position')) {
            throw new Exception('Model doesn\'t have position property');
        }

        $this->getView()->setVars([
            'rows' => $model::fetchAll($this->getConditions(), $this->getSorting()),
            'header' => $this->getPositioning()
        ]);

        $this->getView()->setScript('table/position');
    }

    /**
     * @return array
     * @throws Exception
     */
    public function setPosition()
    {
        $this->getView()->setLayoutEnabled(false);

        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        /** @var Model $model */
        $model = new $modelClassName();

        if (!$model->getMeta()->hasProperty('position')) {
            throw new Exception('Model doesn\'t have position property');
        }

        foreach ($this->getRequest()->getParam('items', []) as $index => $id) {

            $model = $modelClassName::fetchOne([
                'id' => $id
            ]);

            $model->position = $index;
            $model->save();
        }

        return [];
    }

    /**
     * @throws Exception
     */
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
        $controls = $this->controls ?? [
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
        return 10;
    }

    /**
     * @return array
     */
    public function getExportHeader()
    {
        return $this->exportHeader ?? $this->header ?? [];
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

                $conditions[$filter['by'] ?? 'id'] = $filter['value'];
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
     * @return Paginator
     */
    public function getPaginator()
    {
        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $paginator = new Paginator(
            new $modelClassName(),
            $this->getConditions(),
            $this->getSorting()
        );

        $paginator->setPage(
            intval($this->getRequest()->getGet('page', 1))
        );

        $paginator->setItemsPerPage(
            $this->getItemsPerPage()
        );

        return $paginator;
    }

    /**
     *
     */
    public function index()
    {
        $this->getView()->setVars([

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
        ]);

        $this->getView()->setScript('table/index');
    }

    /**
     *
     */
    public function select()
    {
        $this->getView()->setVars([

            'title' => $this->getTitle(),
            'language' => $this->getRequest()->getGet('filter')['language'] ?? false,
            'filter' => $this->getFilterWithValues(),
            'header' => $this->getHeader(),
            'isSelectControl' => true,
            'paginator' => $this->getPaginator(),
            'elementName' => $this->getParam('elementName'),
            'controller' => $this->getRouter()->getController(),
            'fields' => json_decode(base64_decode($this->getParam('fields')), true),
            'fieldsRaw' => $this->getParam('fields')
        ]);

        $this->getView()->setScript('table/modal');
    }

    /**
     * @return string
     */
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

                $cols[] = $this->exportType($row->{$name}, $struct['type'] ?? 'text');
            }

            $response[] = implode(',', $cols);
        }

        $fileName = $this->getExportFileName() . '_' . date('c') . '.csv';
        $this->getResponse()->setHeader('Content-Disposition', 'attachment;filename=' . $fileName);

        return implode(";\n", $response) . ';';
    }

    /**
     * @throws Exception\DomainMustBeProvided
     * @throws Exception\RouterVarMustBeProvided
     * @throws Exception\ValidatorClassWasNotFound
     */
    public function manage()
    {
        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $model = $modelClassName::fetchObject([
            'id' => $this->getRequest()->getParam('id')
        ]);

        /** @var Form $form */
        $form = $this->getForm($model);

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

                        if ($formData['language']->id != $language->id
                            && $model->getMeta()->hasProperty('language')
                            && $model->getMeta()->hasProperty('enabled'))
                        {
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

        $this->getView()->setVars([
            'title' => $this->getTitle(),
            'form' => $form,
        ]);

        $this->getView()->setScript('form/default');
    }

    /**
     * @param mixed  $value
     * @param string $type
     * @return false|string|null
     */
    public function exportType($value, $type)
    {
        switch ($type) {

            case 'text':
                return $value;

            case 'bool':
                return (bool)$value ? 'Да' : 'Нет';

            case 'date':
                return date('Y/m/d H:i:s', $value);
        }

        return null;
    }

    /**
     * @return bool
     */
    public function setEnabled()
    {
        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $record = $modelClassName::fetchOne([
            'id' => $this->getRequest()->getGet('id')
        ]);

        $record->enabled = $this->getRequest()->getGet('enabled');
        $record->save();

        return true;
    }

    /**
     *
     */
    public function init()
    {
        parent::init();

        $this->getView()->setLayoutEnabled(
            !$this->getRequest()->isAjax()
        );

        $this->getView()->setLayoutTemplate('index');
        $this->getView()->setAutoRender(true);

        $this->getView()->setPath(__DIR__ . '/Crud');
    }

    /**
     * @param Model $model
     */
    public function didSave($model) {}
}
