<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\LevelRepository")
 */
class Level
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
     * @ORM\Column(type="integer")
     */
    private $weight;

    /**
     * @ORM\Column(type="boolean")
     */
    private $hidden;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Subject", inversedBy="levels")
     * @ORM\JoinColumn(nullable=false)
     */
    private $subject;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Unit", mappedBy="level", orphanRemoval=true)
     */
    private $units;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentGroup", mappedBy="level", orphanRemoval=true)
     */
    private $studentGroups;

    public function __construct()
    {
        $this->units = new ArrayCollection();
        $this->studentGroups = new ArrayCollection();
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

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

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

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * @return Collection|Unit[]
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(Unit $unit): self
    {
        if (!$this->units->contains($unit)) {
            $this->units[] = $unit;
            $unit->setLevel($this);
        }

        return $this;
    }

    public function removeUnit(Unit $unit): self
    {
        if ($this->units->contains($unit)) {
            $this->units->removeElement($unit);
            // set the owning side to null (unless already changed)
            if ($unit->getLevel() === $this) {
                $unit->setLevel(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StudentGroup[]
     */
    public function getStudentGroups(): Collection
    {
        return $this->studentGroups;
    }

    public function addStudentGroup(StudentGroup $studentGroup): self
    {
        if (!$this->studentGroups->contains($studentGroup)) {
            $this->studentGroups[] = $studentGroup;
            $studentGroup->setLevel($this);
        }

        return $this;
    }

    public function removeStudentGroup(StudentGroup $studentGroup): self
    {
        if ($this->studentGroups->contains($studentGroup)) {
            $this->studentGroups->removeElement($studentGroup);
            // set the owning side to null (unless already changed)
            if ($studentGroup->getLevel() === $this) {
                $studentGroup->setLevel(null);
            }
        }

        return $this;
    }
}
