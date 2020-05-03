<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\StudentGroupService;
use App\Entity\Picture;
use App\Entity\StudentGroup;
use App\Entity\GroupJoinRequest;
use App\Entity\GroupInviteToken;
use App\Entity\Subject;
use App\Entity\Message;
use App\Entity\GroupMessage;
use App\Entity\File;
use App\Entity\Level;
use App\Entity\User;
use App\Entity\Unit;
use App\Entity\Lesson;
use App\Entity\GroupResource;
use App\Entity\LiveLesson;
use App\Entity\LessonAttachment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use App\Exception\EduException;
use App\Exception\LessonException;


class TestCommand extends Command
{

    private $service;
    private $em;

    public function __construct(StudentGroupService $service, EntityManagerInterface $em) {
        $this->service = $service;
        $this->em = $em;
        parent::__construct();
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:test-command';

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        $teacher = $this->em->getRepository(User::class)->findOneBy(['id' => 130]);

        $users = [
            $this->em->getRepository(User::class)->findOneBy(['id' => 137]),
            $this->em->getRepository(User::class)->findOneBy(['id' => 138]),
            $this->em->getRepository(User::class)->findOneBy(['id' => 139]),
            $this->em->getRepository(User::class)->findOneBy(['id' => 140])
        ];
        $groups = [
            $this->em->getRepository(StudentGroup::class)->findOneBy(['id' => 1]),
            $this->em->getRepository(StudentGroup::class)->findOneBy(['id' => 2]),
            $this->em->getRepository(StudentGroup::class)->findOneBy(['id' => 3]),
        ];

        $this->service->createLiveLesson($teacher, $groups[0], [
            'title' => 'Hello, World xd',
            'start' => new \DateTime('+7 days'),
            'url' => null
        ]);
        return 0;
    }
}