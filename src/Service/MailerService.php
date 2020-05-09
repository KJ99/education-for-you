<?php
namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

class MailerService {
    private $mailer;

    public function __construct(MailerInterface $mailer) {
        $this->mailer = $mailer;
    } 

    public function sendTestMail() {
        $email = (new Email())
            ->from('hello@example.com')
            ->to('ieu63543@eoopy.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $this->mailer->send($email);
    }

    public function sendTestTemplatedEmail() {
        (new TemplatedEmail())
        ->from('fabien@example.com')
        ->to(new Address('ryan@example.com'))
        ->subject('Thanks for signing up!')
    
        // path of the Twig template to render
        ->htmlTemplate('emails/signup.html.twig')
    
        // pass variables (name => value) to the template
        ->context([
            'expiration_date' => new \DateTime('+7 days'),
            'username' => 'foo',
        ]);
    }
}