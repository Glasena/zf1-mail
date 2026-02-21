<?php

class IndexController extends Zend_Controller_Action
{
    public function indexAction(): void
    {
        $this->view->title = 'ZF1-future App';
        $this->view->message = 'Bem-vindo ao ZF1-future com PHP 8.1 e MySQL!';
    }
}
