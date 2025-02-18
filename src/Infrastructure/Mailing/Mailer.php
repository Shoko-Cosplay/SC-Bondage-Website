<?php

namespace App\Infrastructure\Mailing;

use App\Infrastructure\Queue\EnqueueMethod;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Transport\Smtp\SmtpTransport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Crypto\DkimSigner;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Message;
use Twig\Environment;

class Mailer
{
    public function __construct(
        private readonly Environment $twig,
        private readonly EnqueueMethod $enqueue,
        private MailerInterface $mailer,
        private readonly ?string $dkimKey = null,
    ) {
    }

    public function createEmail(string $template, array $data = []): Email
    {
        $this->twig->addGlobal('format', 'html');
        $html = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.html.twig']));
        $this->twig->addGlobal('format', 'text');
        $text = $this->twig->render($template, array_merge($data, ['layout' => 'mails/base.text.twig']));

        return (new Email())
            ->from(new Address('noreply@shoko-cosplay.fr', 'Shoko Cosplay'))
            ->html($html)
            ->text($text);
    }

    public function send(Email $email,?\DateTime $dateTime = null): void
    {
        $this->enqueue->enqueue(self::class, 'sendNow', [$email],$dateTime);
    }

    public function sendNow(Email $email): void
    {

        if($_ENV['APP_ENV'] == "dev"){
            $this->mailer->send($email);
        } else {
            $transport = new Transport\Smtp\EsmtpTransport(
                host: 'mail.infomaniak.com',
                port: '465',
                tls: true,
                authenticators: [new Transport\Smtp\Auth\LoginAuthenticator()]
            );
            $transport->setUsername($_ENV['SMTP_USERNAME']);
            $transport->setPassword($_ENV['SMTP_PASSWORD']);
            $this->mailer = new \Symfony\Component\Mailer\Mailer($transport);
            $this->mailer->send($email);
        }
    }
}
