<?php

namespace Application\Modules\Mail\Forms;

use Zend_Form;

class Mail extends Zend_Form
{
    public function init()
    {

        $this->addElement('text', 'subject', [
            'label' => 'Subject',
            'required' => true,
        ]);

        $this->addElement('textarea', 'body', [
            'label' => 'Body',
            'required' => true,
        ]);

        $this->addElement('text', 'recipient', [
            'label' => 'Recipient',
            'required' => true,
            'validators' => [
                'EmailAddress',
            ],
        ]);

        $this->addElement('submit', 'submit', [
            'label' => 'Send Mail',
            'type' => 'submit',
        ]);

    }
}