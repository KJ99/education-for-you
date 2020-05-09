<?php
namespace App\Processor;

use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Picture;


use App\Service\PictureService;

use App\Result\SubjectResult;
use App\Exception\SubjectException;
use App\Exception\DatabaseException;
use App\Exception\EduException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class SubjectProcessor extends Processor {
    
    private $pictureService;

    public function __construct(EntityManagerInterface $em, PictureService $pictureService) {
        $this->pictureService = $pictureService;
        parent::__construct($em);
    }
    
    public function processCreation(User $user, array $data) : Subject {
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

    public function processUpdate(User $user, array $data, Subject $subject) : Subject {
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

    public function processTeachersAddition(User $user, Subject $subject, array $teachers) : Subject {
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

    public function processTeacherRemoval(User $user, Subject $subject, User $teacher) : Subject {
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

    public function processSubjectHide(User $user, Subject $subject) : Subject {
        return $this->processSubjectVisibilityChange($user, $subject, true);
    }

    public function processSubjectShow(User $user, Subject $subject) : Subject {
        return $this->processSubjectVisibilityChange($user, $subject, false);
    }

    private function processSubjectDelete(User $user, Subject $subject) : Subject {
        // if(!$user->isAdmin()) {
        //     throw new SubjectException('access.denied');
        // }
        // $filePath = $subject->getBanner()->getDirectory() . $subject->getBanner()->getFileName();
        // $deleted = $this->deleteEntity($subject);
        // if(!$deleted) {
        //     throw new DatabaseException('delete.failed');
        // } else {
        //     $this->pictureService->deleteFile($filePath);
        // }
        throw new EduException('unknown');
        return $subject;
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
            $error = 'publish.bad.type';
        }

        return $error;
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
}