<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\StudentGroupService;
use App\Entity\Picture;
use App\Entity\Subject;
use App\Service\FileService;
use App\Entity\File;
use App\Entity\User;
use App\Entity\Unit;
use App\Entity\Lesson;
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
        $user = $this->em->getRepository(User::class)->findOneBy(['id' => 130]);
        $level = $this->em->getRepository(Level::class)->findOneBy(['id' => 1]);

        $data = [

        ];

        $res = $this->service->create($user, $level, $data);
        dump($data);
        return 0;
    }
}