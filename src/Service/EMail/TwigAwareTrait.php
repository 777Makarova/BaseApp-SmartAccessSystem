<?php

namespace App\Service\EMail;

use Twig\Environment;

trait TwigAwareTrait
{
    /**
     * @var Environment
     */
    protected $twig;

    public function setTwigEngine(Environment $twig)
    {
        $this->twig = $twig;

        return $this;
    }
}
