<?php

namespace App\Service\Code\Factories;

use App\Service\EMail\SwiftMessage;
use Swift_Mailer;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Twig\Environment;

class MailFactory
{
    private $mailer;
    private $senderEmail;
    private $twig;

    public function __construct(Swift_Mailer $mailer, ContainerInterface $container, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->senderEmail = $container->getParameter('sender_name');
        $this->twig = $twig;
    }

    /**
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     */
    public function create(string $subject, string $body, $contentType = null, $charset = null): SwiftMessage
    {
        $sender = new SwiftMessage($subject, $body, $contentType, $charset);
        $sender->setFrom($this->senderEmail)
             ->setTwigEngine($this->twig)
             ->setSwiftMailer($this->mailer);

        return $sender;
    }
}
