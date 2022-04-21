<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use voku\helper\AntiXSS;

class AuthController extends Action
{
    public function autenticar()
    {
        $antiXss = new AntiXSS();
    
        $email = $antiXss->xss_clean($_POST['email']);
		$senha = $antiXss->xss_clean($_POST['senha']);

        $usuario = Container::getModel('Usuario');
        $usuario->__set('email', $email);
        $usuario->__set('senha', $senha);

        $usuario->autenticar();
        
        if($usuario->__get('id') != '' && $usuario->__get('nome')) {
            
            $_SESSION['id'] = $usuario->__get('id');
            $_SESSION['nome'] = $usuario->__get('nome');
            
            header('Location: /timeline');

        } else {
            if(!isset($_SESSION['login_tentativas'])) {
                $_SESSION['login_tentativas'] = 0;
            }
            $_SESSION['login_tentativas']++;

            header('Location: /?login=erro');
        }

    }

    public function sair()
    {
        unset($_SESSION['id']);
        unset($_SESSION['nome']);
        header('Location: /');
    }
}