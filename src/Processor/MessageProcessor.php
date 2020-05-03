<?php
namespace App\Processor;

use App\Entity\StudentGroup;
use App\Entity\User;
use App\Entity\File;
use App\Entity\Message;
use App\Entity\GroupMessage;
use App\Entity\SystemMessage;

use App\Service\FileService;
use App\Exception\MessageException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MessageProcessor extends Processor {
        
    private $pictureService;
    private $fileService;

    public function __construct(EntityManagerInterface $em, FileService $fileService) {
        $this->fileService = $fileService;
        parent::__construct($em);
    }
}