<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LiveLessonRepository")
 */
class LiveLesson
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $meetupUrl;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StudentGroup", inversedBy="liveLessons")
     * @ORM\JoinColumn(nullable=false)
     */
    private $studentGroup;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getMeetupUrl(): ?string
    {
        return $this->meetupUrl;
    }

    public function setMeetupUrl(?string $meetupUrl): self
    {
        $this->meetupUrl = $meetupUrl;

        return $this;
    }

    public function getStudentGroup(): ?StudentGroup
    {
        return $this->studentGroup;
    }

    public function setStudentGroup(?StudentGroup $studentGroup): self
    {
        $this->studentGroup = $studentGroup;

        return $this;
    }
}
