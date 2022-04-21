<?php

namespace App\Controllers;


use MF\Controller\Action;
use MF\Model\Container;
use voku\helper\AntiXSS;

class AppController extends Action
{
    public function validaAutenticacao()
    {
        if(!isset($_SESSION['id']) || $_SESSION['id'] == '' || !isset($_SESSION['nome']) || $_SESSION['nome'] == '') {
            header('Location: /?login=erro');
        }
    }

    public function timeline()
    {
        $this->validaAutenticacao();

        $tweet = Container::getModel('Tweet');

        $tweet->__set('id_usuario', $_SESSION['id']);
        $tweets = $tweet->getTweets();
        
        $this->view->tweets = $tweets;

        $usuario = Container::getModel('Usuario');
        $usuario->__set('id', $_SESSION['id']);

        $this->view->info_usuario = $usuario->getInfoUsuario();
        $this->view->total_tweets = $usuario->getTotalTweets();
        $this->view->total_seguindo = $usuario->getTotalSeguindo();
        $this->view->total_seguidores = $usuario->getTotalSeguidores();

        $this->render('timeline');
      
    }

    public function tweet()
    {
        $this->validaAutenticacao();

        $antiXss = new AntiXSS();
       
        $tweet = Container::getModel('Tweet');

        $tweetEnviado = $antiXss->xss_clean($_POST['tweet']);

        $tweet->__set('tweet', $tweetEnviado);
        $tweet->__set('id_usuario', $_SESSION['id']);

        $tweet->salvar();

        header("Location: /timeline");
        
    }

    public function quemSeguir()
    {
        $this->validaAutenticacao();

        $pesquisarPor = isset($_GET['pesquisarPor']) ? $_GET['pesquisarPor'] : '';

        $usuarios = [];

        if($pesquisarPor != '') {

            $usuario = Container::getModel('Usuario');
            $usuario->__set('nome', $pesquisarPor);
            $usuario->__set('id', $_SESSION['id']);
            $usuarios = $usuario->getAll();
        }

        $this->view->usuarios = $usuarios;

        $this->render('quemSeguir');
    }

    public function acao()
    {
        $this->validaAutenticacao();

        $acao = isset($_GET['acao']) ? $_GET['acao'] : '';
        $id_usuario_seguindo = isset($_GET['id_usuario']) ? $_GET['id_usuario'] : '';
        
        $usuario = Container::getModel('UsuariosSeguidores');
        $usuario->__set('id_usuario', $_SESSION['id']);

        if($acao == 'seguir') {

            $usuario->seguirUsuario($id_usuario_seguindo);
        } else if($acao == 'deixar_de_seguir') {

            $usuario->deixarSeguirUsuario($id_usuario_seguindo);
        }

        header('Location: /quem_seguir');
    }

    public function exclui_tweet()
    {
        $this->validaAutenticacao();

        $id = isset($_GET['id']) ? $_GET['id'] : '';

        if($_GET['id_usuario'] == $_SESSION['id']) {
            $tweet = Container::getModel('Tweet');

            $tweet->__set('id', $id);

            $tweet->apagaTweet();
        }
        
        header('Location: /timeline');

    }

}