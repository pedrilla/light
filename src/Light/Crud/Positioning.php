<?php

/**
 * Trait Default_Trait_Positioning
 */
trait Default_Trait_Positioning
{
    public $positioning = true;

    public function setPositionAction()
    {
        $this->_disableLayout();
        $this->_disableAutoRendering();

        /** @var MongoStar\Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        $model = new $modelClassName();

        if (!$model->getMeta()->hasProperty('position')) {
            throw new Exception('Model doesn\'t have position property');
        }

        foreach ($this->getParam('items') as $index => $itemId) {
            $model = $modelClassName::fetchOne(['id' => $itemId]);
            $model->position = $index;
            $model->save();
        }
    }

    public function positionAction()
    {
        /** @var MongoStar\Model $modelClassName */
        $modelClassName = $this->getModelClassName();

        /** @var MongoStar\Model $model */
        $model = new $modelClassName();

        if (!$model->getMeta()->hasProperty('position')) {
            throw new Exception('Model doesn\'t have position property');
        }


        if ($model->getMeta()->hasProperty('language')) {

            if ($this->getParam('language')) {
                $language = App_Model_Language::fetchOne(['id' => $this->getParam('language')]);
            }
            else {
                $language = App_Model_Language::fetchOne();
            }

            $this->view->selectedLanguage = $language;

            $this->view->rows = $modelClassName::fetchAll([
                'language' => $language->id
            ]);
        }
        else {
            $this->view->rows = $modelClassName::fetchAll([]);
        }


        $this->view->type = 'list';

        if ($model->getMeta()->hasProperty('image')) {
            $this->view->type = 'image';
        }
        else if ($model->getMeta()->hasProperty('images')) {
            $this->view->type = 'images';
        }


        $this->_helper->viewRenderer('common/position', null, true);
    }
}