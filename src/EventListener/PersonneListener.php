<?php

namespace App\EventListener;

use App\Event\AddPersonneEvent;
use App\Event\ListAllPersonnesEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Event\KernelEvent;

class PersonneListener 
{
  public function __construct(
    private  LoggerInterface $logger
  ){}

  public  function onPersonneAdd(AddPersonneEvent $event): void 
  {
    $this->logger->debug("Hello, je suis entrain d'écouter l'événement personne.add et la personne ajoutée est: ". $event->getPersonne()->getName());
  }  

  public function onListAllPersonnes(ListAllPersonnesEvent $event): void
  {
    $this->logger->debug("Le nombre de personne dans la base est : ". $event->getNbPersonne());
  }

  public function onListAllPersonnes2(ListAllPersonnesEvent $event): void
  {
    $this->logger->debug("Le second Listener avec le Nbre : ". $event->getNbPersonne());
  }

  public function logKernelRequest(KernelEvent $event): void
  {
    dd($event->getRequest());
  }
}
