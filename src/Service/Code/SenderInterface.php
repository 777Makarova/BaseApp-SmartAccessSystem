<?php

namespace App\Service\Code;

use App\Entity\Code\Code;

interface SenderInterface
{
    public function getName(): string;

    public function sendCode(Code $code);
}
