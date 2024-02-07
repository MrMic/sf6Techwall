<?php

namespace App\Event;

use App\Entity\Personne;
use Symfony\Contracts\EventDispatcher\Event;

class AddPersonneEvent extends Event
{
  const ADD_PERSONNE_EVENT= 'personne.add';

  public  function __construct(public Personne $personne) {}

  /**
   * Return the personne object
   *
   * @return Personne
   */
  public function getPersonne(): Personne
  {
    return $this->personne;
  }

} 
