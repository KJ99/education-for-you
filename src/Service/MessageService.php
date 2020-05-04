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

    public function sendDirectMessage(User $user, User $receiver, array $data) : MessageResult {
        $result = new MessageResult();
        try {
            $message = $this->processor->processDirectMessageCreation($user, $receiver, $data);
            $result->setSuccess(true)->setData($message);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function sendGroupMessage(User $user, StudentGroup $group, array $data) : GroupMessageResult{
        $result = new GroupMessageResult();
        try {
            $message = $this->processor->processGroupMessageCreation($user, $group, $data);
            $result->setSuccess(true)->setData($message);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function sendSystemMessage(User $receiver, array $data) : SystemMessageResult {
        $result = new SystemMessageResult();
        try {
            $message = $this->processor->processSystenMessageCreation($receiver, $data);
            $result->setSuccess(true)->setData($message);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }
}