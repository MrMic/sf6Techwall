<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailerService
{
  private $replyTo;

  public function __construct(private MailerInterface $mailer, $replyTo) {
    $this->replyTo = $replyTo;
  }



// ═══════════════════════════════ SEND EMAIL ═════════════════════════════
  public function sendEmail(
    $to = 'mic.a.elle.chlon@gmail.com', 
    $content = '<p>See Twig integration for better HTML integration!</p>', 
    $subject='Hello Email from SYFONY 6') : void
  {
    // NOTE: OK
    // dd($this->replyTo);

    $email = (new Email())
      ->from('mic.a.elle.chlon@gmail.com')
      ->to($to)
      ->replyTo($this->replyTo)
      ->subject($subject)
      // ->text('Sending emails is fun again!')
      ->html($content);


     $this->mailer->send($email);
    
  }
}
