<?php
namespace App\Processor;

use App\Entity\StudentGroup;
use App\Entity\Level;
use App\Entity\User;
use App\Entity\Picture;

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

    public function processCreation(User $user, array $data) : StudentGroup {
        $dataError = $this->getDataValidationError($data);
        if($dataError != null) {
            throw new StudentGroupException($dataError);
        } else if(!$data['level']->getSubject()->getTeachers()->contains($user)) {
            throw new StudentGroupException('access.denied');
        }

        $pictureResult = $this->pictureService->resolvePicture($data['avatar']->getPathName());
        if(!$pictureResult->getSuccess()) {
            throw $pictureResult->getError();
        }
        $avatar = $pictureResult->getData();

        $group = $this->buildEntity($user, $data, $avatar);

        $saved = $this->saveEntity($group);

        if(!$saved) {
            $this->pictureService->deleteFile($avatar->getDirectory() . $avatar->getFileName());
            throw new DatabaseException('save.failed');
        }

        return $group;
    }

    public function processUpdate(User $user, StudentGroup $group, array $data) : StudentGroup {
        $dataError = $this->getDataValidationError($data, $group);
        if($dataError != null) {
            throw new StudentGroupException($dataError);
        } else if($group->getTeacher()->getId() != $user->getId()) {
            throw new StudentGroupException('access.denied');
        }

        $oldPicturePath = $group->getAvatar()->getDirectory() . $group->getAvatar()->getFileName();
        $newPictureResult = $data['avatar'] instanceof UploadedFile 
                    ? $this->pictureService->resolvePicture($data['avatar']->getPathName())
                    : null;
        if($newPictureResult != null && !$newPictureResult->getSuccess()) {
            throw $newPictureResult->getError();
        }
        $newAvatar = $newPictureResult != null ? $newPictureResult->getData() : null;
        if($newAvatar != null) {
            $this->em->remove($group->getAvatar());
        }

        $group = $this->updateEntitty($group, $data, $newAvatar);

        $saved = $this->saveEntity($group);

        if($saved) {
            $this->pictureService->deleteFile($oldPicturePath);
        } else {
            throw new DatabaseException('save.failed');
        }

        return $group;
    }

    public function processDeletion(User $user, StudentGroup $group) : StudentGroup {
        if($group->getTeacher()->getId() != $user->getId() && !$user->isAdmin()) {
            throw new StudentGroupException('access.denied');
        }
    }

    private function getDataValidationError(array $data, ?StudentGroup $current = null) : ?string {
        $error = null;
        $level = null;

        if(array_key_exists('level', $data) && $data['level'] instanceof Level) {
            $level = $data['level'];
        } else if($current != null) {
            $level = $current->getLevel();
        }

        $name = array_key_exists('name', $data) && gettype($data['name']) == 'string' ? $data['name'] : null;
        $groupWithSameName = $level != null && $name != null
            ? $this->em->getRepository(StudentGroup::class)->findOneBy(['level' => $level, 'name' => $name])
            : null;

        if($level == null) {
            $error = 'level.not.set';
        } else if($name == null || strlen(trim($name)) == 0) {
            $error = 'name.not.set';
        } else if($groupWithSameName != null && ($current == null || $current->getName() != $data['name'])) {
            $error = 'name.duplicated';
        } else if($current == null && (!array_key_exists('avatar', $data) || !($data['avatar'] instanceof UploadedFile))) {
            $error = 'avatar.not.set';
        } else if(!array_key_exists('color', $data) || gettype($data['color']) != 'string') {
            $error = 'color.not.set';
        } else if(!array_key_exists('auto_accept', $data) || gettype($data['auto_accept']) != 'boolean') {
            $error = 'auto_accept.not.set';
        } else if(!array_key_exists('hidden', $data) || gettype($data['hidden']) != 'boolean') {
            $error = 'hidden.not.set';
        }

        return $error;
    }

    private function buildEntity(User $user, array $data, Picture $avatar) : StudentGroup {
        $group = new StudentGroup();
        $group
            ->setName(trim($data['name']))
            ->setTeacher($user)
            ->setAvatar($avatar)
            ->setLevel($data['level'])
            ->setColor($data['color'])
            ->setAutoAccept($data['auto_accept'])
            ->setHidden($data['hidden']);
        return $group;
    }

    private function updateEntitty(StudentGroup $group, array $data, ?Picture $newAvatar) : StudentGroup {
        $group
            ->setName(trim($data['name']))
            ->setColor($data['color'])
            ->setAutoAccept($data['auto_accept'])
            ->setHidden($data['hidden']);
        if($newAvatar != null) {
            $group->setAvatar($newAvatar);
        }

        return $group;
    }

    private function saveEntity(StudentGroup $group) : bool {
        $result;
        try {
            $this->em->persist($group->getAvatar());
            $this->em->persist($group);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }
}