<?php
namespace App\Service;

use App\Service\EntityService;
use App\Service\FileService;
use App\Processor\LessonProcessor;
use App\Entity\Unit;
use App\Entity\Lesson;
use App\Entity\LessonAttachment;
use App\Entity\User;

use App\Result\LessonResult;
use App\Exception\LessonException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class LessonService extends EntityService {
    private $processor;

    public function __construct(EntityManagerInterface $em, FileService $fileService) {
        $this->processor = new LessonProcessor($em, $fileService);
        parent::__construct($em);
    }

    public function create(User $user, Unit $unit, array $data) : LessonResult {
        $result = new LessonResult();
        $this->em->beginTransaction();
        try {
            $lesson = $this->processor->processCreation($user, $unit, $data);
            $result->setSuccess(true)->setData($lesson);
            $this->em->commit();
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
            $this->em->rollback();
        }
        return $result;
    }

    public function update(User $user, Lesson $lesson, array $data) : LessonResult {
        $result = new LessonResult();
        $this->em->beginTransaction();
        try {
            $lesson = $this->processor->processUpdate($user, $lesson, $data);
            $result->setSuccess(true)->setData($lesson);
            $this->em->commit();
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
            $this->em->rollback();
        }
        return $result;
    }

    public function addAttachment(User $user, Lesson $lesson, UploadedFile $file) : LessonResult {
        $result = new LessonResult();
        try {
            $lesson = $this->processor->processAttachmentAddition($user, $lesson, $file);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function removeAttachment(User $user, LessonAttachment $attachment) : LessonResult {
        $result = new LessonResult();
        try {
            $lesson = $this->processor->processAttachmentRemoval($user, $attachment);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function hide(User $user, Lesson $lesson) : LessonResult {
        $result = new LessonResult();
        try {
            $lesson = $this->processor->processHide($user, $lesson);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function show(User $user, Lesson $lesson) : LessonResult {
        $result = new LessonResult();
        try {
            $lesson = $this->processor->processShow($user, $lesson);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function delete(User $user, Lesson $lesson) : LessonResult {
        $result = new LessonResult();
        try {
            $lesson = $this->processor->processDeletion($user, $lesson);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }
}