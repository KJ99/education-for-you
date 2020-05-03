<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupInviteTokenRepository")
 */
class GroupInviteToken
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\StudentGroup", inversedBy="inviteTokens")
     * @ORM\JoinColumn(nullable=false)
     */
    private $studentGroup;

    /**
     * @ORM\Column(type="datetime")
     */
    private $expires;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $token;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getExpires(): ?\DateTimeInterface
    {
        return $this->expires;
    }

    public function setExpires(\DateTimeInterface $expires): self
    {
        $this->expires = $expires;

        return $this;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }
}
