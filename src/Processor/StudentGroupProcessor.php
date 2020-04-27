<?php
namespace App\Processor;

use App\Entity\StudentGroup;
use App\Entity\Level;
use App\Entity\User;

use App\Result\StudentGroupResult;
use App\Service\PictureService;
use App\Exception\StudentGroupException;
use App\Exception\DatabaseException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class StudentGroupProcessor extends Processor {
        
    private $pictureService;

    public function __construct(EntityManagerInterface $em, PictureService $pictureService) {
        $this->pictureService = $pictureService;
        parent::__construct($em);
    }

    public function processCreate(User $user, Level $level, array $data) : StudentGroup {
        $dataError = $this->getDataValidationError($data);
        if($dataError != null) {
            throw new StudentGroupException($dataError);
        } else if(!$level->getSubject()->getTeachers()->contains($user)) {
            throw new StudentGroupException('access.denied');
        }
    }

    public function processUpdate(User $user, StudentGroup $group, array $data) : StudentGroup {
        $dataError = $this->getDataValidationError($data);
        if($dataError != null) {
            throw new StudentGroupException($dataError);
        } else if($group->getTeacher()->getId() != $user->getId()) {
            throw new StudentGroupException('access.denied');
        }

    }

    public function processDeletion(User $user, StudentGroup $group) : StudentGroup {
        if($group->getTeacher()->getId() != $user->getId() && !$user->isAdmin()) {
            throw new StudentGroupException('access.denied');
        }
    }

    private function getDataValidationError(array $data) : ?string {
        $error = null;

        return $error;
    }
}