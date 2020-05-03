<?php
namespace App\Service;

use App\Entity\StudentGroup;
use App\Entity\User;
use App\Entity\Level;
use App\Entity\GroupJoinRequest;
use App\Entity\GroupInviteToken;
use App\Entity\LiveLesson;
use App\Entity\GroupResource;

use App\Result\StudentGroupResult;
use App\Result\GroupRequestResult;
use App\Result\GroupInviteTokenResult;
use App\Result\LiveLessonResult;

use App\Exception\EduException;
use App\Exception\StudentGroupException;

use App\Processor\StudentGroupProcessor;
use App\Processor\LiveLessonProcessor;
use App\Processor\GroupJoinProcessor;

use App\Service\EntityService;
use App\Service\PictureService;
use App\Service\FileService;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StudentGroupService extends EntityService {
    private $groupProcessor;
    private $joinProcessor;
    private $liveLessonProcessor;

    public function __construct(EntityManagerInterface $em, PictureService $pictureService, FileService $fileService) {
        $this->groupProcessor = new StudentGroupProcessor($em, $pictureService, $fileService);
        $this->joinProcessor = new GroupJoinProcessor($em);
        $this->liveLessonProcessor = new LiveLessonProcessor($em);
        parent::__construct($em);
    }

    public function create(User $user, array $data) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $group = $this->groupProcessor->processCreation($user, $data);
            $result->setSuccess(true)->setData($group);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function update(User $user, StudentGroup $group, array $data) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $group = $this->groupProcessor->processUpdate($user, $group, $data);
            $result->setSuccess(true)->setData($group);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function removeStudent(User $user, StudentGroup $group, User $student) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $group = $this->groupProcessor->processStudentRemoval($user, $group, $student);
            $result->setSuccess(true)->setData($group);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function leaveGroup(User $user, StudentGroup $group) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $group = $this->groupProcessor->processGroupLeave($user, $group);
            $result->setSuccess(true)->setData($group);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function addResource(User $user, StudentGroup $group, UploadedFile $file) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $group = $this->groupProcessor->processResourceAddition($user, $group, $file);
            $result->setSuccess(true)->setData($group);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    } 

    public function removeResource(User $user, GroupResource $resource) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $group = $this->groupProcessor->processResourceRemoval($user, $resource);
            $result->setSuccess(true)->setData($group);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    } 

    public function delete(User $user, StudentGroup $group) : StudentGroupResult {
        $result = new StudentGroupResult();
        try {
            $group = $this->groupProcessor->processDeletion($user, $group);
            $result->setSuccess(true)->setData($group);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function join(User $user, StudentGroup $group) : GroupRequestResult {
        $result = new GroupRequestResult();
        try {
            $request = $this->joinProcessor->processJoinAttempt($user, $group);
            $result->setSuccess(true)->setData($request);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function acceptRequest(User $user, GroupJoinRequest $request) : GroupRequestResult {
        $result = new GroupRequestResult();
        try {
            $request = $this->joinProcessor->processJoinRequestAccept($user, $request);
            $result->setSuccess(true)->setData($request);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function declineRequest(User $user, GroupJoinRequest $request) : GroupRequestResult {
        $result = new GroupRequestResult();
        try {
            $request = $this->joinProcessor->processJoinRequestDecline($user, $request);
            $result->setSuccess(true)->setData($request);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function cancelRequest(User $user, GroupJoinRequest $request) : GroupRequestResult {
        $result = new GroupRequestResult();
        try {
            $request = $this->joinProcessor->processRequestCancelation($user, $request);
            $result->setSuccess(true)->setData($request);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function createInviteToken(User $user, StudentGroup $group) : GroupInviteTokenResult {
        $result = new GroupInviteTokenResult();
        try {
            $token = $this->joinProcessor->processInviteTokenCreation($user, $group);
            $result->setSuccess(true)->setData($token);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function joinGroupWithToken(User $user, string $token) : GroupInviteTokenResult {
        $result = new GroupInviteTokenResult();
        try {
            $token = $this->joinProcessor->processInviteTokenUse($user, $token);
            $result->setSuccess(true)->setData($token);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function deleteInviteToken(User $user, GroupInviteToken $token) : GroupInviteTokenResult {
        $result = new GroupInviteTokenResult();
        try {
            $token = $this->joinProcessor->processInviteTokenDeletion($user, $token);
            $result->setSuccess(true)->setData($token);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function createLiveLesson(User $user, StudentGroup $group, array $data) : LiveLessonResult {
        $result = new LiveLessonResult();
        try {
            $lesson = $this->liveLessonProcessor->processCreation($user, $group, $data);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function updateLiveLesson(User $user, LiveLesson $lesson, array $data) : LiveLessonResult {
        $result = new LiveLessonResult();
        try {
            $lesson = $this->liveLessonProcessor->processUpdate($user, $lesson, $data);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function addLiveLessonMeetupUrl(User $user, LiveLesson $lesson, ?string $url) : LiveLessonResult {
        $result = new LiveLessonResult();
        try {
            $lesson = $this->liveLessonProcessor->processUrlAddition($user, $lesson, $url);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function deleteLiveLesson(User $user, LiveLesson $lesson) : LiveLessonResult {
        $result = new LiveLessonResult();
        try {
            $lesson = $this->liveLessonProcessor->processDeletion($user, $lesson);
            $result->setSuccess(true)->setData($lesson);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }
}