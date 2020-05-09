<?php
namespace App\Service;

use App\Processor\SubjectProcessor;
use App\Service\EntityService;
use App\Service\PictureService;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Picture;

use App\Result\SubjectResult;
use App\Result\PictureResult;
use App\Exception\SubjectException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class SubjectService extends EntityService {
    private $pictureService;

    public function __construct(EntityManagerInterface $em, PictureService $pictureService) {
        $this->pictureService = $pictureService;
        $this->processor = new SubjectProcessor($em, $pictureService);
        parent::__construct($em);
    }

    public function create(User $user, array $data) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processor->processCreation($user, $data);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function update(User $user, Subject $current, array $data) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processor->processUpdate($user, $data, $current);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    
    public function addTeachers(User $user, Subject $subject, array $teachers) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processor->processTeachersAddition($user, $subject, $teachers);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    } 

    public function removeTeacher(User $user, Subject $subject, User $teacher) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processor->processTeacherRemoval($user, $subject, $teacher);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function deleteSubject(User $user, Subject $subject) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processor->processSubjectDelete($user, $subject);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function hideSubject(User $user, Subject $subject) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processor->processSubjectHide($user, $subject);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }    
    
    public function showSubject(User $user, Subject $subject) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processor->processSubjectShow($user, $subject);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }


}