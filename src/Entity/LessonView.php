<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LessonViewRepository")
 */
class LessonView
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\lesson")
     * @ORM\JoinColumn(nullable=false)
     */
    private $lesson;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     */
    private $user;

    /**
     * @ORM\Column(type="datetime")
     */
    private $viewDate;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLesson(): ?lesson
    {
        return $this->lesson;
    }

    public function setLesson(?lesson $lesson): self
    {
        $this->lesson = $lesson;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getViewDate(): ?\DateTimeInterface
    {
        return $this->viewDate;
    }

    public function setViewDate(\DateTimeInterface $viewDate): self
    {
        $this->viewDate = $viewDate;

        return $this;
    }
}
