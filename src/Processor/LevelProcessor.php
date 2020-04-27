<?php
namespace App\Processor;

use App\Entity\Subject;
use App\Entity\Level;
use App\Entity\User;

use App\Result\LevelResult;
use App\Exception\LevelException;
use App\Exception\DatabaseException;
use App\Exception\EduException;

class LevelProcessor extends Processor {
    
    public function processCreation(User $user, Subject $subject, array $data) : Level {
        $validationError = $this->getDataValidationError($user, $subject, $data);
        if($validationError != null) {
            throw new LevelException($validationError);
        }
        $orderNumber = $this->resolveOrderNumber($data['order_number'], $subject);
        $level = $this->buildEntity($subject, $data, $orderNumber);
        $saved = $this->saveEntity($level);
        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $level;
    }

    public function processUpdate(User $user, Level $level, array $data) : Level {
        $validationError = $this->getDataValidationError($user, $level->getSubject(), $data, $level);
        if($validationError != null) {
            throw new LevelException($validationError);
        }
        $orderNumber = $this->resolveOrderNumber($data['order_number'], $level->getSubject(), $level);
        $level = $this->updateEntity($level, $data, $orderNumber);
        $saved = $this->saveEntity($level);
        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $level;
    }

    public function processHide(User $user, Level $level) : Level {
        return $this->processVisibilityChange($user, $level, true);
    }

    public function processShow(User $user, Level $level) : Level {
        return $this->processVisibilityChange($user, $level, false);
    }

    public function processVisibilityChange(User $user, Level $level, bool $hidden) : Level {
        $validationError = $this->getAccessError($user, $level->getSubject());
        if($validationError != null) {
            throw new LevelException($validationError);
        }
        $level->setHidden($hidden);
        $saved = $this->saveEntity($level);
        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $level;
    }

    public function processDeletion(User $user, Level $level) : Level {
        $validationError = $this->getAccessError($user, $level->getSubject());
        if($validationError != null) {
            throw new LevelException($validationError);
        }
        $deleted = $this->deleteEntity($level);
        if(!$deleted) {
            throw new DatabaseException('delete.failed');
        } 
        return $level;
    }

    private function getDataValidationError(User $user, Subject $subject, array $data, ?Level $current = null) : ?string {
        $error = null;
        $accessError = $this->getAccessError($user, $subject);
        $repo = $this->em->getRepository(Level::class);
        $levelWithSameName = isset($data['name']) && gettype($data['name']) == 'string' 
            ? $repo->findOneBy(['subject' => $subject, 'name' => trim($data['name'])])
            : null;
        if($accessError != null) {
            $error = $accessError;
        } else if(!isset($data['name']) || gettype($data['name']) != 'string' || strlen(trim($data['name'])) == 0) {
            $error = 'name.empty';
        } else if($levelWithSameName != null && ($current == null || $current->getId() != $levelWithSameName->getId())) {
            $error = 'name.duplicated';
        } else if(!isset($data['order_number']) || !is_int($data['order_number'])) {
            $error = 'order.number.invalid';
        } else if(!isset($data['publish']) || gettype($data['publish']) != 'boolean') {
            $error = 'publish.invalid';
        }
        return $error;
    }

    private function getAccessError(User $user, Subject $subject) : ?string {
        $error = null;
        if(!$user->isAdmin() && $subject->getCoordinator()->getId() != $user->getId()) {
            $error = 'access.denied';
        }
        return $error;
    }

    private function resolveOrderNumber(int $orderNumber, Subject $subject, ?Level $current = null) : int {
        if($current != null && $current->getWeight() == $orderNumber) {
            return $orderNumber;
        }
        $repo = $this->em->getRepository(Level::class);
        $currentCount = intval($repo->countForSubject($subject));
        if($orderNumber < 1) {
            $orderNumber = 1;
        } else if($orderNumber > $currentCount) {
            $orderNumber = $currentCount + 1;
        }
        $repo->incrementHigherOrderNumbers($orderNumber, $subject);
        return $orderNumber;
    }

    private function buildEntity(Subject $subject, array $data, int $orderNumber) : Level {
        $level = new Level();
        $level 
            ->setName(trim($data['name']))
            ->setWeight($orderNumber)
            ->setHidden(!$data['publish'])
            ->setSubject($subject);
        return $level;
    }

    private function updateEntity(Level $level, array $data, int $orderNumber) : Level {
        $level 
            ->setName(trim($data['name']))
            ->setWeight($orderNumber)
            ->setHidden(!$data['publish']);
        return $level;
    }

    private function saveEntity(Level $level) : bool {
        $result;
        try {
            $this->em->persist($level);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    private function deleteEntity(Level $level) : bool {
        $result;
        try {
            $this->em->remove($level);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

}