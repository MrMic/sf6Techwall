<?php

namespace App\EventSubscriber;

use App\Event\AddPersonneEvent;
use App\Service\MailerService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;


class PersonneEventSubscriber implements EventSubscriberInterface 
{

public function __construct(private  MailerService $mailer) {}

    public static function getSubscribedEvents(): array
    {
      return [
        AddPersonneEvent::ADD_PERSONNE_EVENT => ['onAddPersonneEvent', 3000],
      ];
    }

    public function onAddPersonneEvent(AddPersonneEvent $event): void {
      $personne = $event->getPersonne();
      $mailMessage = "Bonjour " . $personne->getName(
      ) . ' ' . $personne->getFirstname() . ' a été ajouté avec succès.' ;

      $this->mailer->sendEmail(content: $mailMessage, subject: 'Mail sent from EventSubscriber');
    }
  
}
