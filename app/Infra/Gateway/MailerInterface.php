<?php

namespace App\Infra\Gateway;

interface MailerInterface
{
    public function send($email);
}
