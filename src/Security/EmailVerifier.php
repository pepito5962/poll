<?php

namespace App\Security;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class EmailVerifier
{
    private MailerInterface $mailer;

    private string $senderEmail;

    private string $senderName;

    public function __construct(MailerInterface $mailer, string $senderEmail, string $senderName){
        $this->mailer = $mailer;
        $this->senderEmail = $senderEmail;
        $this->senderName = $senderName;
    }

    /**
     * @param array<mixed> $arguments
     * @return void
     */
    public function send(array $arguments): void
    {
        [
            'recipient_email' => $recipientsEmail,
            'subject' => $subject,
            'html_template' => $htmlTemplate,
            'context' => $context
        ] = $arguments;

        $email = new TemplatedEmail();
        $email->from(new Address($this->senderEmail, $this->senderName))
            ->to($recipientsEmail)
            ->subject($subject)
            ->htmlTemplate($htmlTemplate)
            ->context($context);

        try{
            $this->mailer->send($email);
        }catch(TransportExceptionInterface $mailerException){
            throw $mailerException;
        }
    }
}
