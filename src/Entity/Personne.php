<?php

namespace App\Entity;

use App\Repository\PersonneRepository;
use App\Traits\TimeStampTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\HasLifecycleCallbacks]
#[ORM\Entity(repositoryClass: PersonneRepository::class)]
class Personne
{
    use TimeStampTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 30)]
    #[Assert\NotBlank(message: 'Veuillez entrer votre prenom')]
    #[Assert\Length(min: 2, minMessage: 'Veuillez entrer au moins 2 caractères')]
    private ?string $firstname = null;

    #[ORM\Column(length: 50)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $age = null;

    #[ORM\OneToOne(inversedBy: 'personne', cascade: ['persist', 'remove'])]
    private ?Profile $profile = null;

    #[ORM\ManyToMany(targetEntity: Hobby::class, inversedBy: 'personnes')]
    private Collection $hobbies;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?Job $job = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'personnes')]
    private ?User $createdBy = null;

    // #[ORM\Column(nullable: true)]
    // private ?\DateTime $createdAt = null;
    //
    // #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    // private ?\DateTimeInterface $updatedAt = null;

    // ______________________________________________________________________
    public function __construct()
    {
        $this->hobbies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): static
    {
        $this->age = $age;

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): static
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * @return Collection<int, Hobby>
     */
    public function getHobbies(): Collection
    {
        return $this->hobbies;
    }

    public function addHobby(Hobby $hobby): static
    {
        if (!$this->hobbies->contains($hobby)) {
            $this->hobbies->add($hobby);
        }

        return $this;
    }

    public function removeHobby(Hobby $hobby): static
    {
        $this->hobbies->removeElement($hobby);

        return $this;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function setJob(?Job $job): static
    {
        $this->job = $job;

        return $this;
    }

    // public function getCreatedAt(): ?\DateTimeImmutable
    // {
    //     return $this->createdAt;
    // }
    //
    // public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    // {
    //     $this->createdAt = $createdAt;
    //
    //     return $this;
    // }
    //
    // public function getUpdatedAt(): ?\DateTimeInterface
    // {
    //     return $this->updatedAt;
    // }
    //
    // public function setUpdatedAt(?\DateTimeInterface $updatedAt): static
    // {
    //     $this->updatedAt = $updatedAt;
    //
    //     return $this;
    // }
    //
    // #[ORM\PrePersist]
    // public function onPrePersist(): void
    // {
    //     $this->createdAt = new \DateTime();
    //     $this->updatedAt = new \DateTime();
    // }
    // 
    // #[ORM\PreUpdate]
    // public function onPreUpdate() : void
    // {
    //     $this->updatedAt = new \DateTime();
    // }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): static
    {
        $this->image = $image;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): static
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}
