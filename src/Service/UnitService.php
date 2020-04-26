<?php
namespace App\Service;

use App\Service\EntityService;
use App\Processor\UnitProcessor;
use App\Entity\Level;
use App\Entity\Unit;
use App\Entity\User;

use App\Result\UnitResult;
use App\Exception\UnitException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;

class UnitService extends EntityService {
    private $processor;

    public function __construct(EntityManagerInterface $em) {
        $this->processor = new UnitProcessor($em);
        parent::__construct($em);
    }

    public function create(User $user, Level $level, array $data) : UnitResult {
        $result = new UnitResult();
        $this->em->beginTransaction();
        try {
            $unit = $this->processor->processCreation($user, $level, $data);
            $result->setSuccess(true)->setData($unit);
            $this->em->commit();
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
            $this->em->rollback();
        }
        return $result;
    }

    public function update(User $user, Unit $unit, array $data) : UnitResult {
        $result = new UnitResult();
        $this->em->beginTransaction();
        try {
            $unit = $this->processor->processUpdate($user, $unit, $data);
            $result->setSuccess(true)->setData($unit);
            $this->em->commit();
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
            $this->em->rollback();
        }
        return $result;
    }

    public function hide(User $user, Unit $unit) : UnitResult {
        $result = new UnitResult();
        try {
            $unit = $this->processor->processHide($user, $unit);
            $result->setSuccess(true)->setData($unit);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function show(User $user, Unit $unit) : UnitResult {
        $result = new UnitResult();
        try {
            $unit = $this->processor->processShow($user, $unit);
            $result->setSuccess(true)->setData($unit);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function delete(User $user, Unit $unit) : UnitResult {
        $result = new UnitResult();
        try {
            $unit = $this->processor->processDeletion($user, $unit);
            $result->setSuccess(true)->setData($unit);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }
}