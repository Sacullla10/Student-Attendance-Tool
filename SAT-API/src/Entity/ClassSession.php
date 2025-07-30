<?php

namespace App\Entity;

use App\Repository\ClassSessionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClassSessionRepository::class)]
class ClassSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $session_date = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSessionDate(): ?\DateTime
    {
        return $this->session_date;
    }

    public function setSessionDate(\DateTime $session_date): static
    {
        $this->session_date = $session_date;

        return $this;
    }
}
