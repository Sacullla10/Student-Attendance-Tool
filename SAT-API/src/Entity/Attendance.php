<?php

namespace App\Entity;

use App\Repository\AttendanceRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AttendanceRepository::class)]
class Attendance
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'attendances')]
    #[ORM\JoinColumn(name: 'student_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private ?Student $student = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?ClassSession $class_session = null;

    #[ORM\Column]
    private ?bool $is_present = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStudent(): ?Student
    {
        return $this->student;
    }

    public function setStudent(?Student $student): static
    {
        $this->student = $student;

        return $this;
    }

    public function getClassSession(): ?ClassSession
    {
        return $this->class_session;
    }

    public function setClassSession(?ClassSession $class_session): static
    {
        $this->class_session = $class_session;

        return $this;
    }

    public function isPresent(): ?bool
    {
        return $this->is_present;
    }

    public function setIsPresent(bool $is_present): static
    {
        $this->is_present = $is_present;

        return $this;
    }
}
