<?php


namespace App\Service;

use Psr\Log\LoggerInterface;

class Helpers {

  // private $langue;

  public function __construct(private LoggerInterface $logger) {
    // $this->langue = $langue;
  }

  public function sayCc(): string {
    return 'cc';
  }
}
