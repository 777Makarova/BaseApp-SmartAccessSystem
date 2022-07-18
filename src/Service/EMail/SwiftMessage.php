<?php

namespace App\Service\EMail;

use App\Entity\User\User;
use Swift_Message;
use Twig\Error\Error as TwigError;

/**
 * Message with twig support.
 *
 * Class TwigAwareSwiftMessage
 */
class SwiftMessage extends Swift_Message
{
    use TwigAwareTrait;
    use SwiftMailerAwareTrait;

    /**
     * @param string $name
     * @param array  $parameters
     * @param string $contentType
     * @param string $charset
     *
     * @return $this
     *
     * @throws TwigError
     */
    public function setBodyFromTemplate($name, $parameters = [], $contentType = null, $charset = null): SwiftMessage
    {
        $body = $this->twig->render($name, $parameters);
        $this->setBody($body, $contentType, $charset);

        return $this;
    }

    /**
     * @param string $name
     * @param array  $parameters
     *
     * @return $this
     *
     * @throws TwigError
     */
    public function setSubjectFromTemplate($name, $parameters = []): SwiftMessage
    {
        $body = $this->twig->render($name, $parameters);
        $this->setSubject($body);

        return $this;
    }

    /**
     * @param string $twig
     * @param array  $parameters
     * @param string $contentType
     * @param string $charset
     *
     * @return $this
     *
     * @throws TwigError
     */
    public function setBodyFromTwig($twig, $parameters = [], $contentType = null, $charset = null): SwiftMessage
    {
        $body = $this->twig->createTemplate($twig)->render($parameters);
        $this->setBody($body, $contentType, $charset);

        return $this;
    }

    /**
     * @param string $twig
     * @param array  $parameters
     *
     * @return $this
     *
     * @throws TwigError
     */
    public function setSubjectFromTwig($twig, $parameters = []): SwiftMessage
    {
        $body = $this->twig->createTemplate($twig)->render($parameters);
        $this->setSubject($body);

        return $this;
    }

    /**
     * @return $this
     */
    public function setUser(User $user)
    {
        return $this->setTo($user->getEmail());
    }

    public function send(&$failedRecipients = null)
    {
        // bug fix: when Symfony tries to serialize message
        // it get in trouble because
        // twig contains closures somewhere
        // so it gets 'Serialization of 'Closure' is not allowed'
        // and profiler is not working because of it
        // TODO: do it in dev env only
        $this->twig = null;

        return $this->mailer->send($this, $failedRecipients);
    }
}
