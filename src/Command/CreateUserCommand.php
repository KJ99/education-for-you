<?php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\UserService;
use App\Entity\Picture;
use App\Service\FileService;
use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;


class CreateUserCommand extends Command
{

    private $service;
    private $em;

    public function __construct(UserService $service, EntityManagerInterface $em) {
        $this->service = $service;
        $this->em = $em;
        parent::__construct();
    }

    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:create-user';

    protected function configure()
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $res = $this->service->createUserFromConsole($input, $output, $this->getHelper('question'));
        dump($res);
        return 0;
    }
}