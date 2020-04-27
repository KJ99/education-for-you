<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SubjectRepository")
 */
class Subject
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Picture", cascade={"remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $banner;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="coordinatedSubjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $coordinator;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="taughtSubjects")
     */
    private $teachers;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Level", mappedBy="subject", orphanRemoval=true)
     */
    private $levels;

    public function __construct()
    {
        $this->teachers = new ArrayCollection();
        $this->levels = new ArrayCollection();
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

    public function getBanner(): ?Picture
    {
        return $this->banner;
    }

    public function setBanner(?Picture $banner): self
    {
        $this->banner = $banner;

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

    public function getCoordinator(): ?User
    {
        return $this->coordinator;
    }

    public function setCoordinator(?User $coordinator): self
    {
        $this->coordinator = $coordinator;

        return $this;
    }

    /**
     * @return Collection|User[]
     */
    public function getTeachers(): Collection
    {
        return $this->teachers;
    }

    public function addTeacher(User $teacher): self
    {
        if (!$this->teachers->contains($teacher)) {
            $this->teachers[] = $teacher;
        }

        return $this;
    }

    public function removeTeacher(User $teacher): self
    {
        if ($this->teachers->contains($teacher)) {
            $this->teachers->removeElement($teacher);
        }

        return $this;
    }

    /**
     * @return Collection|Level[]
     */
    public function getLevels(): Collection
    {
        return $this->levels;
    }

    public function addLevel(Level $level): self
    {
        if (!$this->levels->contains($level)) {
            $this->levels[] = $level;
            $level->setSubject($this);
        }

        return $this;
    }

    public function removeLevel(Level $level): self
    {
        if ($this->levels->contains($level)) {
            $this->levels->removeElement($level);
            // set the owning side to null (unless already changed)
            if ($level->getSubject() === $this) {
                $level->setSubject(null);
            }
        }

        return $this;
    }
}
