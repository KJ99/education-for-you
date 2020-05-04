<?php
namespace App\Processor;

use App\Entity\StudentGroup;
use App\Entity\User;
use App\Entity\File;
use App\Entity\Message;
use App\Entity\GroupMessage;
use App\Entity\SystemMessage;
use App\Entity\MessageAttachment;
use App\Entity\GroupMessageAttachment;
use App\Entity\SystemMessageAttachment;

use App\Service\FileService;
use App\Exception\MessageException;
use App\Exception\DatabaseException;
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

    public function processDirectMessageCreation(User $user, User $receiver, array $data) : Message {
        $validationError = $this->getStandardMessageValidationError($data);
        if($validationError != null) {
            throw new MessageException($validationError);
        }
        $attachmentFiles = $this->resolveAttachmentFiles($data['attachments']);
        $attachments = $this->resolveDirectMessageAttamchmentsEntities($attachmentFiles);
        $message = $this->buildDirectMessage($user, $receiver, $data, $attachments);
        $saved = $this->saveMessage($message);
        if(!$saved) {
            foreach($attachmentFiles as $file) {
                $this->fileService->deleteFile($file->getDirectory() . $file->getFileName());
            }
            throw new DatabaseException('save.failed');
        }
        return $message;
    }

    public function processGroupMessageCreation(User $user, StudentGroup $group, array $data) : GroupMessage {
        $validationError = $this->getGroupMessageValidationError($user, $group, $data);
        if($validationError != null) {
            throw new MessageException($validationError);
        }
        $attachmentFiles = $this->resolveAttachmentFiles($data['attachments']);
        $attachments = $this->resolveGroupMessageAttamchmentsEntities($attachmentFiles);
        $message = $this->buildGroupMessage($group, $data, $attachments);
        $saved = $this->saveMessage($message);
        if(!$saved) {
            foreach($attachmentFiles as $file) {
                $this->fileService->deleteFile($file->getDirectory() . $file->getFileName());
            }
            throw new DatabaseException('save.failed');
        }
        return $message;
    }

    public function processSystenMessageCreation(User $receiver, array $data) : SystemMessage {
        $validationError = $this->getStandardMessageValidationError($data);
        if($validationError != null) {
            throw new MessageException($validationError);
        }
        $attachmentFiles = $this->resolveAttachmentFiles($data['attachments']);
        $attachments = $this->resolveSystemMessageAttamchmentsEntities($attachmentFiles);
        $message = $this->buildSystemMessage($receiver, $data, $attachments);
        $saved = $this->saveMessage($message);
        if(!$saved) {
            foreach($attachmentFiles as $file) {
                $this->fileService->deleteFile($file->getDirectory() . $file->getFileName());
            }
            throw new DatabaseException('save.failed');
        }
        return $message;

    }

    private function resolveAttachmentFiles(array $files) : array {
        $attachments = [];
        foreach($files as $file) {
            if($file instanceof UploadedFile) {
                $result = $this->fileService->resolveFile($file->getPathName(), $file->getClientOriginalName());
                if(!$result->getSuccess()) {
                    foreach($attachments as $attachment) {
                        $this->fileService->deleteFile($attachment->getDirectory() . $attachment->getFileName());
                    }
                    throw $result->getError();
                }
                $attachments[] = $result->getData();
            }
        }
        return $attachments;
    }

    private function resolveDirectMessageAttamchmentsEntities(array $files) : array {
        $attachments = [];
        foreach($files as $file) {
            if($file instanceof File) {
                $attachment = new MessageAttachment();
                $attachment
                    ->setFile($file);
                $attachments[] = $attachment;
            }
        }
        return $attachments;
    }

    private function resolveGroupMessageAttamchmentsEntities(array $files) : array {
        $attachments = [];
        foreach($files as $file) {
            if($file instanceof File) {
                $attachment = new GroupMessageAttachment();
                $attachment
                    ->setFile($file);
                $attachments[] = $attachment;
            }
        }
        return $attachments;
    }

    private function resolveSystemMessageAttamchmentsEntities(array $files) : array {
        $attachments = [];
        foreach($files as $file) {
            if($file instanceof File) {
                $attachment = new SystemMessageAttachment();
                $attachment
                    ->setFile($file);
                $attachments[] = $attachment;
            }
        }
        return $attachments;
    }

    private function buildDirectMessage(User $sender, User $receiver, array $data, array $attachments) : Message {
        $message = new Message();
        $message
            ->setSender($sender)
            ->setReceiver($receiver)
            ->setSubject(trim($data['subject']))
            ->setContent($data['content'])
            ->setSentDate(new \DateTime());

        foreach($attachments as $attachment) {
            if($attachment instanceof MessageAttachment) {
                $message->addAttachment($attachment);
            }
        }

        return $message;
    }

    private function buildGroupMessage(StudentGroup $group, array $data, array $attachments) : GroupMessage {
        $message = new GroupMessage();
        $message
            ->setStudentGroup($group)
            ->setSubject(trim($data['subject']))
            ->setContent($data['content'])
            ->setSentDate(new \DateTime());

        foreach($attachments as $attachment) {
            if($attachment instanceof GroupMessageAttachment) {
                $message->addAttachment($attachment);
            }
        }

        return $message;
    }

    private function buildSystemMessage(User $receiver, array $data, array $attachments) : SystemMessage {
        $message = new SystemMessage();
        $message
            ->setReceiver($receiver)
            ->setSubject(trim($data['subject']))
            ->setContent($data['content'])
            ->setSentDate(new \DateTime());

        foreach($attachments as $attachment) {
            if($attachment instanceof SystemMessageAttachment) {
                $message->addAttachment($attachment);
            }
        }

        return $message;
    }

    private function saveMessage($message) : bool {
        $result;
        try {
            foreach($message->getAttachments() as $attachment) {
                $this->em->persist($attachment->getFile());
                $this->em->persist($attachment);
            }
            $this->em->persist($message);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }


    private function getStandardMessageValidationError(array $data) : ?string {
        $error = null;
        if(!array_key_exists('subject', $data) || gettype($data['subject']) != 'string' || strlen(trim($data['subject'])) == 0) {
            $error = 'subject.empty';    
        } else if(!array_key_exists('content', $data) || gettype($data['content']) != 'string' || strlen(trim($data['content'])) == 0) {
            $error = 'content.empty';
        } else if(!array_key_exists('attachments', $data) || !is_array($data['attachments'])) {
            $error = 'content.empty';
        }
        return $error;
    }

    private function getGroupMessageValidationError(User $user, StudentGroup $group, array $data) : ?string {
        $error = null;
        if($group->getTeacher()->getId() != $user->getId()) {
            $error = 'access.denied';
        } else {
            $error = $this->getStandardMessageValidationError($data);
        }
        return $error;
    }

}