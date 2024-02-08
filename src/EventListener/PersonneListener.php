<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use Psr\Log\LoggerInterface;

class PersonneListener 
{
  public function __construct(
    private  LoggerInterface $logger
  ){}

  public  function onPersonneAdd(AddPersonneEvent $event): void 
  {
    $this->logger->debug("Hello, je suis entrain d'écouter l'événement personne.add et la personne ajoutée est: ". $event->getPersonne()->getName());
  }  
}
