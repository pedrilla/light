<?php

/**
 * Trait Admin_Trait_Manage
 */
trait Default_Trait_ManageSingle
{
    public function didSave()
    {

    }

    public function indexAction()
    {
        /** @var MongoStar\Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $formClassName = $this->getFormClassName();

        $model = $modelClassName::fetchObject();

        /** @var Default_Form_Abstract $form */
        $form = new $formClassName($model);

        if ($this->getRequest()->isPost()) {

            if ($form->isValid($this->getAllParams())) {

                $formData = $form->getValues();

                if (!(bool)$model->id && $model->getMeta()->hasProperty('language')) {

                    foreach (App_Model_Language::fetchAll() as $language) {

                        /** @var MongoStar\Model $languageRelatedModel */
                        $languageRelatedModel = new $modelClassName();

                        $languageRelatedModel->populate($formData);
                        $languageRelatedModel->language = $language;

                        $languageRelatedModel->save();

                        $this->didSave($languageRelatedModel);
                    }
                }
                else {

                    $model->populate($formData);
                    $model->save();

                    $this->didSave($model);
                }

                die('ok:'.$this->getParam('return-url'));
            }
        }

        $form->setReturnUrl(App_Helper_Url::assemble(['controller' => $this->getParam('controller')]));

        $this->view->form = $form;
        $this->view->title = $this->getTitle();

        $this->view->hideCancelButton = true;

        $this->_helper->viewRenderer('common/form/default', null, true);
    }
}
