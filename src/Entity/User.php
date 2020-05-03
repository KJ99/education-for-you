<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity(fields={"nickname"}, ignoreNull=true)
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\Email
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $nickname;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Picture")
     * @ORM\JoinColumn(nullable=false)
     */
    private $avatar;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserToken", mappedBy="user", orphanRemoval=true)
     */
    private $tokens;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Subject", mappedBy="coordinator")
     */
    private $coordinatedSubjects;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Subject", mappedBy="teachers")
     */
    private $taughtSubjects;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Lesson", mappedBy="author", orphanRemoval=true)
     */
    private $lessons;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="sender", orphanRemoval=true)
     */
    private $sentMessages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Message", mappedBy="reveiver")
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\StudentGroup", mappedBy="teacher")
     */
    private $taughtGroups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\StudentGroup", mappedBy="students")
     */
    private $studentGroups;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\StudentGroup", mappedBy="requests")
     */
    private $groupJoinRequests;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\SystemMessage", mappedBy="receiver")
     */
    private $systemMessages;

    public function __construct()
    {
        $this->tokens = new ArrayCollection();
        $this->coordinatedSubjects = new ArrayCollection();
        $this->taughtSubjects = new ArrayCollection();
        $this->lessons = new ArrayCollection();
        $this->sentMessages = new ArrayCollection();
        $this->messages = new ArrayCollection();
        $this->taughtGroups = new ArrayCollection();
        $this->studentGroups = new ArrayCollection();
        $this->groupJoinRequests = new ArrayCollection();
        $this->systemMessages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(?string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

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

    /**
     * @return Collection|UserToken[]
     */
    public function getTokens(): Collection
    {
        return $this->tokens;
    }

    public function addToken(UserToken $token): self
    {
        if (!$this->tokens->contains($token)) {
            $this->tokens[] = $token;
            $token->setUser($this);
        }

        return $this;
    }

    public function removeToken(UserToken $token): self
    {
        if ($this->tokens->contains($token)) {
            $this->tokens->removeElement($token);
            // set the owning side to null (unless already changed)
            if ($token->getUser() === $this) {
                $token->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subject[]
     */
    public function getCoordinatedSubjects(): Collection
    {
        return $this->coordinatedSubjects;
    }

    public function addCoordinatedSubject(Subject $coordinatedSubject): self
    {
        if (!$this->coordinatedSubjects->contains($coordinatedSubject)) {
            $this->coordinatedSubjects[] = $coordinatedSubject;
            $coordinatedSubject->setCoordinator($this);
        }

        return $this;
    }

    public function removeCoordinatedSubject(Subject $coordinatedSubject): self
    {
        if ($this->coordinatedSubjects->contains($coordinatedSubject)) {
            $this->coordinatedSubjects->removeElement($coordinatedSubject);
            // set the owning side to null (unless already changed)
            if ($coordinatedSubject->getCoordinator() === $this) {
                $coordinatedSubject->setCoordinator(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Subject[]
     */
    public function getTaughtSubjects(): Collection
    {
        return $this->taughtSubjects;
    }

    public function addTaughtSubject(Subject $taughtSubject): self
    {
        if (!$this->taughtSubjects->contains($taughtSubject)) {
            $this->taughtSubjects[] = $taughtSubject;
            $taughtSubject->addTeacher($this);
        }

        return $this;
    }

    public function removeTaughtSubject(Subject $taughtSubject): self
    {
        if ($this->taughtSubjects->contains($taughtSubject)) {
            $this->taughtSubjects->removeElement($taughtSubject);
            $taughtSubject->removeTeacher($this);
        }

        return $this;
    }

    /**
     * @return Collection|Lesson[]
     */
    public function getLessons(): Collection
    {
        return $this->lessons;
    }

    public function addLesson(Lesson $lesson): self
    {
        if (!$this->lessons->contains($lesson)) {
            $this->lessons[] = $lesson;
            $lesson->setAuthor($this);
        }

        return $this;
    }

    public function removeLesson(Lesson $lesson): self
    {
        if ($this->lessons->contains($lesson)) {
            $this->lessons->removeElement($lesson);
            // set the owning side to null (unless already changed)
            if ($lesson->getAuthor() === $this) {
                $lesson->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getSentMessages(): Collection
    {
        return $this->sentMessages;
    }

    public function addSentMessage(Message $sentMessage): self
    {
        if (!$this->sentMessages->contains($sentMessage)) {
            $this->sentMessages[] = $sentMessage;
            $sentMessage->setSender($this);
        }

        return $this;
    }

    public function removeSentMessage(Message $sentMessage): self
    {
        if ($this->sentMessages->contains($sentMessage)) {
            $this->sentMessages->removeElement($sentMessage);
            // set the owning side to null (unless already changed)
            if ($sentMessage->getSender() === $this) {
                $sentMessage->setSender(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setReveiver($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->contains($message)) {
            $this->messages->removeElement($message);
            // set the owning side to null (unless already changed)
            if ($message->getReveiver() === $this) {
                $message->setReveiver(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|StudentGroup[]
     */
    public function getTaughtGroups(): Collection
    {
        return $this->taughtGroups;
    }

    public function addTaughtGroup(StudentGroup $taughtGroup): self
    {
        if (!$this->taughtGroups->contains($taughtGroup)) {
            $this->taughtGroups[] = $taughtGroup;
            $taughtGroup->setTeacher($this);
        }

        return $this;
    }

    public function removeTaughtGroup(StudentGroup $taughtGroup): self
    {
        if ($this->taughtGroups->contains($taughtGroup)) {
            $this->taughtGroups->removeElement($taughtGroup);
            // set the owning side to null (unless already changed)
            if ($taughtGroup->getTeacher() === $this) {
                $taughtGroup->setTeacher(null);
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
            $studentGroup->addStudent($this);
        }

        return $this;
    }

    public function removeStudentGroup(StudentGroup $studentGroup): self
    {
        if ($this->studentGroups->contains($studentGroup)) {
            $this->studentGroups->removeElement($studentGroup);
            $studentGroup->removeStudent($this);
        }

        return $this;
    }

    /**
     * @return Collection|StudentGroup[]
     */
    public function getGroupJoinRequests(): Collection
    {
        return $this->groupJoinRequests;
    }

    public function addGroupJoinRequest(StudentGroup $groupJoinRequest): self
    {
        if (!$this->groupJoinRequests->contains($groupJoinRequest)) {
            $this->groupJoinRequests[] = $groupJoinRequest;
            $groupJoinRequest->addRequest($this);
        }

        return $this;
    }

    public function removeGroupJoinRequest(StudentGroup $groupJoinRequest): self
    {
        if ($this->groupJoinRequests->contains($groupJoinRequest)) {
            $this->groupJoinRequests->removeElement($groupJoinRequest);
            $groupJoinRequest->removeRequest($this);
        }

        return $this;
    }

    private function hasRole(string $role) {
        return in_array($role, $this->getRoles());
    }

    public function isAdmin() {
        return $this->hasRole('ROLE_ADMIN');
    }

    public function isTeacher() {
        return $this->hasRole('ROLE_TEACHER');
    }

    public function isStudent() {
        return $this->hasRole('ROLE_STUDENT');
    }

    public function isCoordinatorOfSubject(Subject $subject) {
        return $this->getCoordinatedSubjects()->contains($subject);
    }

    /**
     * @return Collection|SystemMessage[]
     */
    public function getSystemMessages(): Collection
    {
        return $this->systemMessages;
    }

    public function addSystemMessage(SystemMessage $systemMessage): self
    {
        if (!$this->systemMessages->contains($systemMessage)) {
            $this->systemMessages[] = $systemMessage;
            $systemMessage->setReceiver($this);
        }

        return $this;
    }

    public function removeSystemMessage(SystemMessage $systemMessage): self
    {
        if ($this->systemMessages->contains($systemMessage)) {
            $this->systemMessages->removeElement($systemMessage);
            // set the owning side to null (unless already changed)
            if ($systemMessage->getReceiver() === $this) {
                $systemMessage->setReceiver(null);
            }
        }

        return $this;
    }
}
