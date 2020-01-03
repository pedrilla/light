<?php

declare(strict_types=1);

namespace Light\Crud;

use Light\Auth;
use Light\Controller;
use Light\Filter\Trim;
use Light\Front;

/**
 * Class Login
 * @package Light\Crud
 */
class Login extends Controller
{
    /**
     * @return false|string
     *
     * @throws \Light\Exception\DomainMustBeProvided
     * @throws \Light\Exception\RouterVarMustBeProvided
     */
    public function index()
    {
        if ($this->getRequest()->isPost()) {

            $login = (new Trim())->filter($this->getRequest()->getPost('login'));
            $password = (new Trim())->filter($this->getRequest()->getPost('password'));

            $config = Front::getInstance()->getConfig()['light']['admin']['auth'];

            if ($config['login'] == $login && $config['password'] == $password) {
                Auth::getInstance()->set($config);
                $this->redirect($this->getRouter()->assemble(['controller' => 'index']));
            }

            $this->getView()->assign('error', true);
        }

        $this->getView()->setPath(__DIR__);
        return $this->getView()->render('login');
    }

    /**
     * @throws \Light\Exception\DomainMustBeProvided
     * @throws \Light\Exception\RouterVarMustBeProvided
     */
    public function logout()
    {
        Auth::getInstance()->remove();
        $this->redirect($this->getRouter()->assemble(['action' => 'index']));
    }
}