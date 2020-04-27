<?php
namespace App\Processor;

use App\Entity\Level;
use App\Entity\Unit;
use App\Entity\User;

use App\Result\UnitResult;
use App\Exception\UnitException;
use App\Exception\DatabaseException;
use App\Exception\EduException;

class UnitProcessor extends Processor {
    
    public function processCreation(User $user, Level $level, array $data) : Unit {
        $validationError = $this->getDataValidationError($user, $level, $data);
        if($validationError != null) {
            throw new UnitException($validationError);
        }
        $orderNumber = $this->resolveOrderNumber($data['order_number'], $level);
        $unit = $this->buildEntity($level, $data, $orderNumber);
        $saved = $this->saveEntity($unit);
        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $unit;
    }

    public function processUpdate(User $user, Unit $unit, array $data) : Unit {
        $validationError = $this->getDataValidationError($user, $unit->getLevel(), $data, $unit);
        if($validationError != null) {
            throw new UnitException($validationError);
        }
        $orderNumber = $this->resolveOrderNumber($data['order_number'], $unit->getLevel(), $unit);
        $unit = $this->updateEntity($unit, $data, $orderNumber);
        $saved = $this->saveEntity($unit);
        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $unit;
    }

    public function processHide(User $user, Unit $unit) : Unit {
        return $this->processVisibilityChange($user, $unit, true);
    }

    public function processShow(User $user, Unit $unit) : Unit {
        return $this->processVisibilityChange($user, $unit, false);
    }

    public function processVisibilityChange(User $user, Unit $unit, bool $hidden) : Unit {
        $validationError = $this->getAccessError($user, $unit->getLevel());
        if($validationError != null) {
            throw new UnitException($validationError);
        }
        $unit->setHidden($hidden);
        $saved = $this->saveEntity($unit);
        if(!$saved) {
            throw new DatabaseException('save.failed');
        } 
        return $unit;
    }

    public function processDeletion(User $user, Unit $unit) : Unit {
        $validationError = $this->getAccessError($user, $unit->getLevel());
        if($validationError != null) {
            throw new UnitException($validationError);
        }
        $deleted = $this->deleteEntity($unit);
        if(!$deleted) {
            throw new DatabaseException('delete.failed');
        } 
        return $unit;
    }

    private function getDataValidationError(User $user, Level $level, array $data, ?Unit $current = null) : ?string {
        $error = null;
        $accessError = $this->getAccessError($user, $level);
        $repo = $this->em->getRepository(Unit::class);
        $unitWithSameName = isset($data['name']) && gettype($data['name']) == 'string' 
            ? $repo->findOneBy(['level' => $level, 'name' => trim($data['name'])])
            : null;
        if($accessError != null) {
            $error = $accessError;
        } else if(!isset($data['name']) || gettype($data['name']) != 'string' || strlen(trim($data['name'])) == 0) {
            $error = 'name.empty';
        } else if($unitWithSameName != null && ($current == null || $current->getId() != $unitWithSameName->getId())) {
            $error = 'name.duplicated';
        } else if(!isset($data['order_number']) || !is_int($data['order_number'])) {
            $error = 'order.number.invalid';
        } else if(!isset($data['publish']) || gettype($data['publish']) != 'boolean') {
            $error = 'publish.invalid';
        }
        return $error;
    }

    private function getAccessError(User $user, Level $level) : ?string {
        $error = null;
        if(!$user->isAdmin() && $level->getSubject()->getCoordinator()->getId() != $user->getId()) {
            $error = 'access.denied';
        }
        return $error;
    }

    private function resolveOrderNumber(int $orderNumber, Level $level, ?Unit $current = null) : int {
        if($current != null && $current->getWeight() == $orderNumber) {
            return $orderNumber;
        }
        $repo = $this->em->getRepository(Unit::class);
        $currentCount = intval($repo->countForLevel($level));
        if($orderNumber < 1) {
            $orderNumber = 1;
        } else if($orderNumber > $currentCount) {
            $orderNumber = $currentCount + 1;
        }
        $repo->incrementHigherOrderNumbers($orderNumber, $level);
        return $orderNumber;
    }

    private function buildEntity(Level $level, array $data, int $orderNumber) : Unit {
        $unit = new Unit();
        $unit 
            ->setName(trim($data['name']))
            ->setWeight($orderNumber)
            ->setHidden(!$data['publish'])
            ->setLevel($level);
        return $unit;
    }

    private function updateEntity(Unit $unit, array $data, int $orderNumber) : Unit {
        $unit 
            ->setName(trim($data['name']))
            ->setWeight($orderNumber)
            ->setHidden(!$data['publish']);
        return $unit;
    }

    private function saveEntity(Unit $unit) : bool {
        $result;
        try {
            $this->em->persist($unit);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    private function deleteEntity(Unit $unit) : bool {
        $result;
        try {
            $this->em->remove($unit);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

}