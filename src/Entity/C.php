<?php

namespace App\Entity;

use App\Repository\CRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CRepository::class)]
class C
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }
}
