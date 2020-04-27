<?php
namespace App\Service;

use App\Entity\StudentGroup;
use App\Entity\User;
use App\Entity\Level;
use App\Entity\GroupJoinRequest;
use App\Entity\LiveLesson;

use App\Result\StudentGroupResult;
use App\Result\GroupRequestResult;
use App\Result\LiveLessonResult;

use App\Exception\EduException;
use App\Exception\StudentGroupException;

use App\Processor\StudentGroupProcessor;
use App\Processor\LiveLessonProcessor;
use App\Processor\GroupJoinProcessor;

use App\Service\EntityService;
use App\Service\PictureService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StudentGroupService extends EntityService {
    private $groupProcessor;
    private $joinProcessor;
    private $liveLessonProcessor;

    public function __construct(EntityManagerInterface $em, PictureService $pictureService) {
        $this->groupProcessor = new StudentGroupProcessor($em, $pictureService);
        $this->joinProcessor = new GroupJoinProcessor($em);
        $this->liveLessonProcessor = new LiveLessonProcessor($em);
        parent::__construct($em);
    }

    public function create(User $user, Level $level, array $data) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $lesson = $this->processor->processCreation($user, $level, $data);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function update(User $user, StudentGroup $group, array $data) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $lesson = $this->processor->processUpdate($user, $group, $data);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function delete(User $user, StudentGroup $group) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $lesson = $this->processor->processDeletion($user, $group);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }
}