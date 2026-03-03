<?php

namespace Application\Modules\Mail\Controllers;

use Application\Modules\Mail\DTOs\SendMailDTO;
use Application\Modules\Mail\Forms\Mail as MailForm;
use Application\Modules\Mail\Services\MailService;
use Zend_Controller_Action;
use Zend_Registry;

class MailController extends Zend_Controller_Action
{

    protected MailService $mailService;

    public function init()
    {
        $em = Zend_Registry::get('doctrine.em');
        $this->mailService = new MailService($em);
    }

    public function indexAction()
    {

        $form = new MailForm();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            $sendMailDTO = new SendMailDTO(
                $formData
            );
            try {
                $this->mailService->sendMail($sendMailDTO);
                $this->view->message = 'Mail sent successfully!';
            } catch (\Throwable $th) {
                $this->view->message = 'Failed to send mail: ' . $th->getMessage();
            }
        }
    }
}
