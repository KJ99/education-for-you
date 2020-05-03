<?php
namespace App\Service;

use App\Entity\StudentGroup;
use App\Entity\User;
use App\Entity\Message;
use App\Entity\GroupMessage;
use App\Entity\SystemMessage;

use App\Result\MessageResult;
use App\Result\GroupMessageResult;
use App\Result\SystemMessageResult;


use App\Exception\EduException;
use App\Exception\MessageException;
use App\Processor\MessageProcessor;

use App\Service\EntityService;
use App\Service\FileService;

use Doctrine\ORM\EntityManagerInterface;

class MessageService extends EntityService {
    private $processor;

    public function __construct(EntityManagerInterface $em, FileService $fileService) {
        $this->processor = new MessageProcessor($em, $fileService);
        parent::__construct($em);
    }

    
}