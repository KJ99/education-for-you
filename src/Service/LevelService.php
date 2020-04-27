<?php
namespace App\Service;

use App\Service\EntityService;
use App\Processor\LevelProcessor;
use App\Entity\Subject;
use App\Entity\Level;
use App\Entity\User;

use App\Result\LevelResult;
use App\Exception\LevelException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;

class LevelService extends EntityService {
    private $processor;

    public function __construct(EntityManagerInterface $em) {
        $this->processor = new LevelProcessor($em);
        parent::__construct($em);
    }

    public function create(User $user, Subject $subject, array $data) : LevelResult {
        $result = new LevelResult();
        $this->em->beginTransaction();
        try {
            $level = $this->processor->processCreation($user, $subject, $data);
            $result->setSuccess(true)->setData($level);
            $this->em->commit();
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
            $this->em->rollback();
        }
        return $result;
    }

    public function update(User $user, Level $level, array $data) : LevelResult {
        $result = new LevelResult();
        $this->em->beginTransaction();
        try {
            $level = $this->processor->processUpdate($user, $level, $data);
            $result->setSuccess(true)->setData($level);
            $this->em->commit();
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
            $this->em->rollback();
        }
        return $result;
    }

    public function hide(User $user, Level $level) : LevelResult {
        $result = new LevelResult();
        try {
            $level = $this->processor->processHide($user, $level);
            $result->setSuccess(true)->setData($level);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function show(User $user, Level $level) : LevelResult {
        $result = new LevelResult();
        try {
            $level = $this->processor->processShow($user, $level);
            $result->setSuccess(true)->setData($level);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function delete(User $user, Level $level) : LevelResult {
        $result = new LevelResult();
        try {
            $level = $this->processor->processDeletion($user, $level);
            $result->setSuccess(true)->setData($level);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }
}