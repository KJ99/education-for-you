<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\StudentGroupRepository")
 */
class StudentGroup
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
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="taughtGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $teacher;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="studentGroups")
     */
    private $students;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Level", inversedBy="studentGroups")
     * @ORM\JoinColumn(nullable=false)
     */
    private $level;

    /**
     * @ORM\Column(type="string", length=7)
     */
    private $color;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Picture")
     * @ORM\JoinColumn(nullable=false)
     */
    private $avatar;

    /**
     * @ORM\Column(type="boolean")
     */
    private $autoAccept;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GroupJoinRequest", mappedBy="studentGroup", orphanRemoval=true)
     */
    private $joinRequests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\LiveLesson", mappedBy="studentGroup", orphanRemoval=true)
     */
    private $liveLessons;


    public function __construct()
    {
        $this->students = new ArrayCollection();
        $this->joinRequests = new ArrayCollection();
        $this->liveLessons = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): self
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getStudents(): Collection
    {
        return $this->students;
    }

    public function addStudent(User $student): self
    {
        if (!$this->students->contains($student)) {
            $this->students[] = $student;
        }

        return $this;
    }

    public function removeStudent(User $student): self
    {
        if ($this->students->contains($student)) {
            $this->students->removeElement($student);
        }

        return $this;
    }

    public function getLevel(): ?Level
    {
        return $this->level;
    }

    public function setLevel(?Level $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getColor(): ?string
    {
        return $this->color;
    }

    public function setColor(string $color): self
    {
        $this->color = $color;

        return $this;
    }

    public function getAvatar(): ?Picture
    {
        return $this->avatar;
    }

    public function setAvatar(?Picture $avatar): self
    {
        $this->avatar = $avatar;

        return $this;
    }

    public function getAutoAccept(): ?bool
    {
        return $this->autoAccept;
    }

    public function setAutoAccept(bool $autoAccept): self
    {
        $this->autoAccept = $autoAccept;

        return $this;
    }

    public function getHidden(): ?bool
    {
        return $this->hidden;
    }

    public function setHidden(bool $hidden): self
    {
        $this->hidden = $hidden;

        return $this;
    }

    /**
     * @return Collection|GroupJoinRequest[]
     */
    public function getJoinRequests(): Collection
    {
        return $this->joinRequests;
    }

    public function addJoinRequest(GroupJoinRequest $joinRequest): self
    {
        if (!$this->joinRequests->contains($joinRequest)) {
            $this->joinRequests[] = $joinRequest;
            $joinRequest->setStudentGroup($this);
        }

        return $this;
    }

    public function removeJoinRequest(GroupJoinRequest $joinRequest): self
    {
        if ($this->joinRequests->contains($joinRequest)) {
            $this->joinRequests->removeElement($joinRequest);
            // set the owning side to null (unless already changed)
            if ($joinRequest->getStudentGroup() === $this) {
                $joinRequest->setStudentGroup(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|LiveLesson[]
     */
    public function getLiveLessons(): Collection
    {
        return $this->liveLessons;
    }

    public function addLiveLesson(LiveLesson $liveLesson): self
    {
        if (!$this->liveLessons->contains($liveLesson)) {
            $this->liveLessons[] = $liveLesson;
            $liveLesson->setStudentGroup($this);
        }

        return $this;
    }

    public function removeLiveLesson(LiveLesson $liveLesson): self
    {
        if ($this->liveLessons->contains($liveLesson)) {
            $this->liveLessons->removeElement($liveLesson);
            // set the owning side to null (unless already changed)
            if ($liveLesson->getStudentGroup() === $this) {
                $liveLesson->setStudentGroup(null);
            }
        }

        return $this;
    }
}
