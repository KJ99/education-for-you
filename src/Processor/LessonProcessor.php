<?php
namespace App\Processor;

use App\Entity\Unit;
use App\Entity\Lesson;
use App\Entity\User;
use App\Entity\File;
use App\Entity\LessonAttachment;
use App\Entity\LessonVideo;

use App\Service\FileService;

use App\Result\LessonResult;
use App\Exception\LessonException;
use App\Exception\DatabaseException;
use App\Exception\EduException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class LessonProcessor extends Processor {
    
    private $fileService;

    public function __construct(EntityManagerInterface $em, FileService $fileService) {
        $this->fileService = $fileService;
        parent::__construct($em);
    }


    public function processCreation(User $user, Unit $unit, array $data) : Lesson {
        $validationError = $this->getDataValidationError($user, $unit, $data);
        if($validationError != null) {
            throw new LessonException($validationError);
        }
        
        $attachments = $this->resolveAttachmentsFiles($data['attachments']);
        $localVideo = null;
        if($data['video'] instanceof UploadedFile) {
            $result = $this->fileService->resolveFile($data['video']->getPathName(), $data['video']->getClientOriginalName());
            if(!$result->getSuccess()) {
                throw $result->getError();
            }
            $localVideo = $this->buildVideoEntity($result->getData());
        }
        
        $orderNumber = $this->resolveOrderNumber($data['order_number'], $unit);
        $lesson = $this->buildEntity($unit, $data, $user, $attachments, $localVideo, $orderNumber);
        
        $saved = $this->saveEntity($lesson);

        if(!$saved) {
            foreach($attachments as $attachment) {
                $this->fileService->deleteFile($attachment->getDirectory() . $attachment->getFileName());
            }
            if($localVideo != null) {
                $file = $localVideo->getFile();
                $this->fileService->deleteFile($file->getDirectory() . $file->getFileName());
            }
            throw new DatabaseException('save.failed');
        }

        return $lesson;
    }

    public function processUpdate(User $user, Lesson $lesson, array $data) : Lesson {
        $validationError = $this->getDataValidationError($user, $lesson->getUnit(), $data, $lesson);
        if($validationError != null) {
            throw new LessonException($validationError);
        } else if($user->getId() != $lesson->getAuthor()->getId()) {
            throw new LessonException('access.denied');
        }
        $oldLessonVideo = $lesson->getVideo(); 
        $oldVideoFilePath = $lesson->getVideo() != null 
            ? $lesson->getVideo()->getFile()->getDirectory() . $lesson->getVideo()->getFile()->getFileName() 
            : null;
        $newLessonVideo = null;
        if($data['video'] instanceof UploadedFile) {
            $result = $this->fileService->resolveFile($data['video']->getPathName(), $data['video']->getClientOriginalName());
            if(!$result->getSuccess()) {
                throw $result->getError();
            }
            $oldVideoDeleted = $this->deleteVideoEntity($oldLessonVideo);
            $newLessonVideo = $this->buildVideoEntity($result->getData());
            $lesson->setVideo($newLessonVideo);
            $this->em->persist($newLessonVideo->getFile());
            $this->em->persist($newLessonVideo);
        }

        $videoReplaceSuccess = $newLessonVideo == null || $oldVideoDeleted;

        if(!$videoReplaceSuccess) {
            throw new LessonException('video.replace.failed');
        }

        $orderNumber = $this->resolveOrderNumber($data['order_number'], $lesson->getUnit(), $lesson);
        $lesson = $this->updateEntity($lesson, $data, $newLessonVideo, $orderNumber);
        
        $saved;

        try {
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }

        if(!$saved) {
            if($newLessonVideo != null) {
                $file = $newLessonVideo->getFile();
                $this->fileService->deleteFile($file->getDirectory() . $file->getFileName());
            }
            throw new DatabaseException('save.failed');
        } else if($newLessonVideo != null && $oldVideoFilePath != null) {
            $this->fileService->deleteFile($oldVideoFilePath);
        }

        return $lesson;
    }

    public function processAttachmentAddition(User $user, Lesson $lesson, UploadedFile $attachment) : Lesson {
        if($user->getId() != $lesson->getAuthor()->getId()) {
            throw new LessonException('access.denied');
        }
        $fileResult = $this->fileService->resolveFile($attachment->getPathName(), $attachment->getClientOriginalName());
        if(!$fileResult->getSuccess()) {
            throw new LessonException('new.attachment.failed');
        }
        $file = $fileResult->getData();
        $attachment = new LessonAttachment();
        $attachment
            ->setLesson($lesson)
            ->setFile($file);
        
        $this->em->persist($file);
        $this->em->persist($attachment);

        $saved;
        try {
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            dump($e);exit;
            $saved = false;
        }
        if(!$saved) {
            $this->fileService->deleteFile($file->getDirectory() . $file->getFileName());
            throw new DatabaseException('save.failed');
        }
        return $lesson;
    }

    public function processAttachmentRemoval(User $user, LessonAttachment $attachment) : Lesson {
        $lesson = $attachment->getLesson();
        if($user->getId() != $lesson->getAuthor()->getId()) {
            throw new LessonException('access.denied');
        }
        $filePath = $attachment->getFile()->getDirectory() . $attachment->getFile()->getFileName();
        $this->em->remove($attachment->getFile());
        $this->em->remove($attachment);
        $saved;
        try {
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }

        if($saved) {
            $this->fileService->deleteFile($filePath);
        } else {
            throw new DatabaseException('save.failed');
        }

        return $lesson;
    }

    public function processHide(User $user, Lesson $lesson) : Lesson {
        return $this->processVisibilityChange($user, $lesson, true);
    }

    public function processShow(User $user, Lesson $lesson) : Lesson {
        return $this->processVisibilityChange($user, $lesson, false);
    }

    public function processVisibilityChange(User $user, Lesson $lesson, bool $hidden) : Lesson {
        $userId = $user->getId();
        $authorId = $lesson->getAuthor()->getId();
        $coordinatorId = $lesson->getSubject()->getCoordinator->getId();
        if($userId != $authorId && $userId != $coordinatorId && !$user->isAdmin()) {
            throw new LessonException('access.denied');
        }
        $lesson->setHidden($hidden);
        
        $saved;
        try {
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }

        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $lesson;
    }

    public function processDeletion(User $user, Lesson $lesson) : Lesson {
        if($user->getId() != $lesson->getAuthor()->getId() && !$user->isAdmin()) {
            throw new LessonException('access.denied');
        }
        $filePaths = [];
        foreach($lesson->getAttachments() as $attachment) {
            $file = $attachment->getFile();
            $filePaths[] = $file->getDirectory() . $file->getFileName();
        }
        if($lesson->getVideo() != null) {
            $file = $lesson->getVideo()->getFile();
            $filePaths[] = $file->getDirectory() . $file->getFileName();
        }

        $deleted = $this->deleteEntity($lesson);
        if($deleted) {
            foreach($filePaths as $path) {
                $this->fileService->deleteFile($path);
            }
        } else {
            throw new DatabaseException('delete.failed');
        }
        return $lesson;
    }
    

    private function getDataValidationError(User $user, Unit $unit, array $data, ?Lesson $current = null) : ?string {
        $error = null;
        $accessError = $this->getAccessError($user, $unit);
        $repo = $this->em->getRepository(Lesson::class);
        $lessonWithSameTitle = array_key_exists('title', $data)
            ? $repo->findOneBy(['title' => trim($data['title']), 'unit' => $unit])
            : null;
        if($accessError != null) {
            $error = $accessError;
        } else if(!array_key_exists('title', $data) || gettype($data['title']) != 'string' || strlen(trim($data['title'])) == 0) {
            $error = 'title.empty';
        } else if($lessonWithSameTitle != null && ($current == null || $current->getId() != $lessonWithSameTitle->getId())) {
            $error = 'title.duplicated';
        } else if(!array_key_exists('text', $data) || ($data['text'] != null && gettype($data['text']) != 'string')) {
            $error = 'text.not.set';
        } else if(!array_key_exists('publish', $data) || gettype($data['publish']) != 'boolean') {
            $error = 'publish.not.set';
        } else if(!array_key_exists('video', $data)) {
            $error = 'video.not.set';
        } else if(!array_key_exists('yt_id', $data)) {
            $error = 'remote.video.not.set';
        } else if(($current == null || $current->getVideo() == null) && !($data['video'] instanceof UploadedFile) && gettype($data['yt_id']) != 'string') {
            $error = 'video.empty';
        } else if(!array_key_exists('order_number', $data) || !is_int($data['order_number'])) {
            $error = 'weight.not.set';
        } else if($current == null && (!array_key_exists('attachments', $data) || !is_array($data['attachments']))) {
            $error = 'attachments.not.set';
        } 
        return $error;
    }

    private function getAccessError(User $user, Unit $unit) : ?string {
        $error = null;
        if(!$unit->getLevel()->getSubject()->getTeachers()->contains($user)) {
            $error = 'access.denied';
        }
        return $error;
    }

    private function resolveOrderNumber(int $orderNumber, Unit $unit, ?Lesson $current = null) : int {
        if($current != null && $current->getWeight() == $orderNumber) {
            return $orderNumber;
        }
        $repo = $this->em->getRepository(Lesson::class);
        $currentCount = intval($repo->countForUnit($unit));
        if($orderNumber < 1) {
            $orderNumber = 1;
        } else if($orderNumber > $currentCount) {
            $orderNumber = $currentCount + 1;
        }
        $repo->incrementHigherOrderNumbers($orderNumber, $unit);
        return $orderNumber;
    }

    private function buildEntity(
        Unit $unit,
        array $data, 
        User $author, 
        array $attachmentsFiles,
        ?LessonVideo $localVideo,
        int $orderNumber
    ) : Lesson {
        $lesson = new Lesson();
        $lesson
            ->setTitle(trim($data['title']))
            ->setAuthor($author)
            ->setUnit($unit)
            ->setText($data['text'])
            ->setHidden(!$data['publish'])
            ->setCreated(new \DateTime())
            ->setRemoteVideo($data['yt_id'])
            ->setWeight($orderNumber)
            ->setVideo($localVideo);
        $attachments = $this->buildAttachmentsEntities($attachmentsFiles);
        foreach($attachments as $attachment) {
            $lesson->addAttachment($attachment);
        }
        return $lesson;
    }

    private function buildVideoEntity(File $file) : LessonVideo {
        $video = new LessonVideo();
        $video->setFile($file);
        return $video;
    }
    
    private function deleteVideoEntity(?LessonVideo $video) : bool {
        $result = true;
        if($video != null) {
            $this->em->remove($video->getFile());
            $this->em->remove($video);
            try {
                $this->em->flush();
            } catch(\Exception $e) {
                dump($e);exit;
                $result = false;
            }
        }
        return $result;
    }

    private function buildAttachmentsEntities(array $files) : array {
        $attachments = [];
        foreach($files as $file) {
            if($file instanceof File) {
                $attachments[] = (new LessonAttachment())->setFile($file);
            }
        }
        return $attachments;
    }

    private function updateEntity(Lesson $lesson, array $data, ?LessonVideo $lessonVideo, int $orderNumber) : Lesson {
        $lesson
            ->setTitle(trim($data['title']))
            ->setText($data['text'])
            ->setHidden(!$data['publish'])
            ->setRemoteVideo($data['yt_id'])
            ->setWeight($orderNumber);

        return $lesson;
    }

    private function saveEntity(Lesson $lesson, bool $saveVideo = true, bool $saveAttachments = true) : bool {
        $result;

        foreach($lesson->getAttachments() as $attachment) {
            $this->em->persist($attachment->getFile());
            $this->em->persist($attachment);
        }
        if($lesson->getVideo() != null) {
            $this->em->persist($lesson->getVideo()->getFile());
            $this->em->persist($lesson->getVideo());
        } 
        $this->em->persist($lesson);

        try {
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            dump($e);exit;
            $result = false;
        }
        return $result;
    }

    private function persistAttachmentsList(array $attachments) {
        foreach($attachments as $attachment) {
            if($attachment instanceof LessonAttachment) {
                $this->em->persist($attachment->getFile());
                $this->em->persist($attachment);
            }
        }
    }


    private function persistLessonVideo(LessonVideo $video) {
        $this->em->persist($video->getFile());
        $this->em->persist($video);
    }

    private function deleteEntity(Lesson $lesson) : bool {
        $result;
        foreach($lesson->getAttachments() as $attachment) {
            $this->em->remove($attachment->getFile());
            $this->em->remove($attachment);
        }
        if($lesson->getVideo() != null) {
            $this->em->remove($lesson->getVideo()->getFile());
            $this->em->remove($lesson->getVideo());
        }
        $this->em->remove($lesson);
        try {
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    private function resolveAttachmentsFiles(array $files) : array {
        $attachments = [];
        foreach($files as $file) {
            if($file instanceof UploadedFile) {
                $result = $this->fileService->resolveFile($file->getPathName(), $file->getClientOriginalName());
                if(!$result->getSuccess()) {
                    throw $result->getError();
                }
                $attachments[] = $result->getData();
            }
        }
        return $attachments;
    }

}