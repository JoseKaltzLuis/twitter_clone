<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use voku\helper\AntiXSS;

class IndexController extends Action {

	public function index() 
	{
		$this->view->login = isset($_GET['login']) ? $_GET['login'] : '';
		$this->render('index');
	}

	public function inscreverse()
	{
		$this->view->usuario = [
			'nome' => '',
			'email' => '',
			'senha' => '',
		];

		$this->view->erroCadastro = false;
		$this->render('inscreverse');
	}

	public function registrar()
	{
		$antiXss = new AntiXSS();

		$nome = $antiXss->xss_clean($_POST['nome']);
		$email = $antiXss->xss_clean($_POST['email']);

		$senha = password_hash($antiXss->xss_clean($_POST['senha']), PASSWORD_DEFAULT);

		$usuario = Container::getModel('Usuario');

		$usuario->__set('nome', $nome);
		$usuario->__set('email', $email);
		$usuario->__set('senha', $senha);
		
		if($usuario->validarCadastro() && count($usuario->getEmailValido()) == 0) {
			
			$usuario->salvar();
			$this->render('cadastro');
		}else {

			$this->view->usuario = [

				'nome' => $nome,
				'email' => $email,
				'senha' => $senha,

			];
			$this->view->erroCadastro = true;
			$this->render('inscreverse');
		}
	}

}
