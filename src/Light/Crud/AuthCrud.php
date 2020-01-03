<?php

declare(strict_types = 1);

namespace Light\Crud;

use Light\Auth;
use Light\Controller;
use Light\Crud;
use Light\Front;

/**
 * Class AuthCrud
 * @package Light\Crud
 */
abstract class AuthCrud extends Controller
{
    public function init()
    {
        parent::init();

        if (!Auth::getInstance()->hasIdentity()) {
            $this->redirect(
                $this->getRouter()->assemble([
                    'controller' => Front::getInstance()->getConfig()['light']['admin']['auth']['route']
                ])
            );
        }

        if ($this->getRequest()->isAjax()) {
            $this->getView()->setLayoutEnabled(false);
        }
    }
}