<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GroupMessageAttachmentRepository")
 */
class GroupMessageAttachment
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\GroupMessage", inversedBy="attachments")
     * @ORM\JoinColumn(nullable=false)
     */
    private $message;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\File")
     * @ORM\JoinColumn(nullable=false)
     */
    private $file;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMessage(): ?GroupMessage
    {
        return $this->message;
    }

    public function setMessage(?GroupMessage $message): self
    {
        $this->message = $message;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): self
    {
        $this->file = $file;

        return $this;
    }
}
