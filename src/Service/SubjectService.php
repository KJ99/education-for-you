<?php
namespace App\Service;

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
        parent::__construct($em);
    }



    public function create(User $user, array $data) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processSubjectData($user, $data);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function update(User $user, Subject $current, array $data) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processUpdateData($user, $data, $current);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    
    public function addTeachers(User $user, Subject $subject, array $teachers) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processTeachersAddition($user, $subject, $teachers);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    } 

    public function removeTeacher(User $user, Subject $subject, User $teacher) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processTeacherRemoval($user, $subject, $teacher);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function deleteSubject(User $user, Subject $subject) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processSubjectDelete($user, $subject);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function hideSubject(User $user, Subject $subject) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processSubjectHide($user, $subject);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }    
    
    public function showSubject(User $user, Subject $subject) : SubjectResult {
        $result = new SubjectResult();
        try {
            $subject = $this->processSubjectShow($user, $subject);
            $result->setSuccess(true)->setData($subject);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    private function processUpdateData(User $user, array $data, Subject $subject) : Subject {
        $validationError = $this->getDataValidationError($user, $data, $subject);
        if($validationError != null) {
            throw new SubjectException($validationError);
        }
        $bannerResult = $data['banner'] instanceof UploadedFile 
            ? $this->pictureService->resolvePicture($data['banner']->getPathName())
            : null;
        if($bannerResult != null && !$bannerResult->getSuccess()) {
            throw $bannerResult->getError();
        }
        $banner = $bannerResult != null ? $bannerResult->getData() : null;
        $oldBanner = $subject->getBanner();
        
        $subject = $this->updateEntity($subject, $data, $banner);

        $saved = $this->saveEntity($subject);

        if(!$saved) {
            $this->pictureService->deleteFile($banner->getDirectory() . $banner->getFileName());
            throw new DatabaseException('save.failed');
        } else if($banner != null) {
            $this->pictureService->deletePicture($oldBanner);
        }

        return $subject;
    }

    private function processSubjectData(User $user, array $data) : Subject {
        $validationError = $this->getDataValidationError($user, $data);
        if($validationError != null) {
            throw new SubjectException($validationError);
        }

        $bannerResult = $this->pictureService->resolvePicture($data['banner']->getPathName());
        if(!$bannerResult->getSuccess()) {
            throw $bannerResult->getError();
        }
        $banner = $bannerResult->getData();

        $subject = $this->buildEntity($data, $banner);

        $saved = $this->saveEntity($subject);

        if(!$saved) {
            $this->pictureService->deleteFile($banner->getDirectory() . $banner->getFileName());
            throw new DatabaseException('save.failed');
        }

        return $subject;
    }

    private function getDataValidationError(User $user, array $data, ?Subject $current = null) : ?string {
        $error = null;
        $providedName = isset($data['name']) ? $data['name'] : null;
        $subjectWithSameName = $this->em->getRepository(Subject::class)->findOneBy(['name' => trim($providedName)]);

        if(!$user->isAdmin()) {
            $error = 'access.denied';
        } else if(!isset($data['name']) || gettype($data['name']) != 'string' || strlen(trim($data['name'])) == 0) {
            $error = 'name.empty';
        } else if($subjectWithSameName != null && ($current == null || $current->getId() != $subjectWithSameName->getId())) {
            $error = 'name.duplicate';
        } else if(!isset($data['coordinator']) || !($data['coordinator'] instanceof User)) {
            $error = 'coordinator.not.set';
        } else if($current == null && !isset($data['banner'])) {
            $error = 'banner.not.set';
        } else if($current == null && ($data['banner'] == null || !($data['banner'] instanceof UploadedFile))) {
            $error = 'banner.not.found';
        } else if($current != null && !$current->getTeachers()->contains($data['coordinator'])) {
            $error = 'coordinator.not.teacher';
        } else if(!isset($data['publish'])) {
            $error = 'publish.not.set';
        } else if(gettype($data['publish']) != 'boolean') {
            $error = 'hidden.bad.type';
        }

        return $error;
    }

    private function updateEntity(Subject $subject, array $data, ?Picture $banner) : Subject {
        $subject
            ->setName(trim($data['name']))
            ->setHidden(!$data['publish'])
            ->setCoordinator($data['coordinator']);
        
        if($banner != null) {
            $subject->setBanner($banner);
        }
        
        return $subject;
    }

    private function buildEntity(array $data, Picture $banner) : Subject {
        $subject = new Subject();

        $subject
            ->setName(trim($data['name']))
            ->setHidden(!$data['publish'])
            ->setBanner($banner)
            ->addTeacher($data['coordinator'])
            ->setCoordinator($data['coordinator']);
        
        if(isset($data['teachers']) && is_array($data['teachers'])) {
            foreach($data['teachers'] as $teacher) {
                if($teacher instanceof User) {
                    $subject->addTeacher($teacher);
                }
            }
        }

        return $subject;
    }

    private function saveEntity(Subject $subject) : bool {
        $result;
        try {
            $this->em->persist($subject->getBanner());
            $this->em->persist($subject);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    private function deleteEntity(Subject $subject) : bool {
        $result;
        try {
            $this->em->remove($subject);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    private function processSubjectDelete(User $user, Subject $subject) : Subject {
        if(!$user->isAdmin()) {
            throw new SubjectException('access.denied');
        }
        $filePath = $subject->getBanner()->getDirectory() . $subject->getBanner()->getFileName();
        $deleted = $this->deleteEntity($subject);
        if(!$deleted) {
            throw new DatabaseException('delete.failed');
        } else {
            $this->pictureService->deleteFile($filePath);
        }
        return $subject;
    }

    private function processTeachersAddition(User $user, Subject $subject, array $teachers) : Subject {
        $validationError = $this->getTeacherAdditionError($user, $subject, $teachers);
        if($validationError != null) {
            throw new SubjectException($validationError);
        }
        
        foreach($teachers as $teacher) {
            $subject->addTeacher($teacher);
        }
        $saved = $this->saveEntity($subject);
        if(!$saved) {
            throw new DatabaseException('teacher.add.failed');
        }

        return $subject;
    }

    private function getTeacherAdditionError(User $user, Subject $subject, array $teachers) : ?string {
        $error = null;
        if(!$user->isAdmin() && $subject->getCoordinator()->getId() != $user->getId()) {
            $error = 'access.denied';
        } else { 
            foreach($teachers as $teacher) {
                if(!($teacher instanceof User)) {
                    $error = 'invalid.teacher.type';
                } else if(!$teacher->isTeacher()) {
                    $error = 'user.not.teacher';
                }
            }
        }
        return $error;
    }

    
    private function processTeacherRemoval(User $user, Subject $subject, User $teacher) : Subject {
        if(!$user->isAdmin() && $subject->getCoordinator()->getId() != $user->getId()) {
            throw new SubjectException('access.denied');
        } else if($subject->getCoordinator()->getId() == $teacher->getId()) {
            throw new SubjectException('remove.coordinator');
        } 

        $subject->removeTeacher($teacher);
        $saved = $this->saveEntity($subject);
        if(!$saved) {
            throw new DatabaseException('teacher.remove.failed');
        }

        return $subject;
    }

    private function processSubjectHide(User $user, Subject $subject) : Subject {
        return $this->processSubjectVisibilityChange($user, $subject, true);
    }

    private function processSubjectShow(User $user, Subject $subject) : Subject {
        return $this->processSubjectVisibilityChange($user, $subject, false);
    }

    private function processSubjectVisibilityChange(User $user, Subject $subject, bool $hidden) : Subject {
        if(!$user->isAdmin() && $subject->getCoordinator()->getId() != $user->getId()) {
            throw new SubjectException('access.denied');
        }
        $subject->setHidden($hidden);
        $saved = $this->saveEntity($subject);
        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $subject;
    }

}