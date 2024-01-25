<?php


namespace App\Service;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\SecurityBundle\Security;

class Helpers {

  // private $langue;

  public function __construct(private LoggerInterface $logger, Security $security) {
    // $this->langue = $langue;
  }

  public function sayCc(): string {
    // return 'cc';
  }

  public  function getUser(): User {
    return $this->security->getUser();
  }
}
