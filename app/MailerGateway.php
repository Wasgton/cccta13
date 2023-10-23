<?php

namespace App;

class MailerGateway implements MailerInterface
{

    public function send($email)
    {
        return true;
    }

}
