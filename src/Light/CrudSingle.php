<?php

declare(strict_types = 1);

namespace Light;

use Exception;
use MongoDB\BSON\Regex;

/**
 * Class CrudSingle
 * @package Light
 */
abstract class CrudSingle extends Crud
{
    public function index()
    {
        /** @var Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $formClassName = $this->getFormClassName();

        $model = $modelClassName::fetchObject();

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
}
