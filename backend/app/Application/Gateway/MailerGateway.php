<?php

namespace App\Application\Gateway;

use App\Infra\Gateway\MailerInterface;

class MailerGateway implements MailerInterface
{

    public function send($email)
    {
        return true;
    }

}
