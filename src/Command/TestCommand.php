<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\LessonService;
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

    public function __construct(LessonService $service, EntityManagerInterface $em) {
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
        return 0;
    }
}