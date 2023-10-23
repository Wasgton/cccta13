<?php

namespace App;

interface MailerInterface
{
    public function send($email);
}
