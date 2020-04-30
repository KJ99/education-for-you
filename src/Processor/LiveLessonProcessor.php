<?php
namespace App\Processor;

use App\Entity\StudentGroup;
use App\Entity\Level;
use App\Entity\LiveLesson;
use App\Entity\User;

use App\Result\StudentGroupResult;
use App\Exception\StudentGroupException;
use App\Exception\DatabaseException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;

class LiveLessonProcessor extends Processor {
    public function processCreation(User $user, StudentGroup $group, array $data) : LiveLesson {
        $validationError = $this->getCreateValidationError($user, $group, $data);
        if($validationError != null) {
            throw new StudentGroupException($validationError);
        }
        
        $liveLesson = $this->buildEntity($group, $data);

        $saved = $this->saveEntity($liveLesson);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $liveLesson;
    } 

    public function processUpdate(User $user, LiveLesson $liveLesson, array $data) : LiveLesson {
        $validationError = $this->getCreateValidationError($user, $liveLesson->getStudentGroup(), $data, $liveLesson);
        if($validationError != null) {
            throw new StudentGroupException($validationError);
        }
        
        $liveLesson = $this->updateEntity($liveLesson, $data);

        $saved = $this->saveEntity($liveLesson);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $liveLesson;
    } 

    public function processUrlAddition(User $user, LiveLesson $liveLesson, string $url) {
        if($liveLesson->getStudentGroup()->getTeacher()->getId() != $user->getId()) {
            throw new StudentGroupException('access.denied');
        }
        
        $liveLesson->setMeetupUrl($url);
        $saved = $this->saveEntity($liveLesson);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }
        
        return $liveLesson;
    }

    
    public function processDeletion(User $user, LiveLesson $liveLesson) {
        if($liveLesson->getStudentGroup()->getTeacher()->getId() != $user->getId()) {
            throw new StudentGroupException('access.denied');
        }
        
        $deleted = $this->deleteEntity($liveLesson);

        if(!$deleted) {
            throw new DatabaseException('save.failed');
        }
        
        return $liveLesson;
    }
 
    private function buildEntity(StudentGroup $group, array $data) : LiveLesson {
        $liveLesson = new LiveLesson();
        $liveLesson
            ->setStudentGroup($group)
            ->setTitle($data['title'])
            ->setStart($data['start'])
            ->setMeetupUrl($data['url']);
        return $liveLesson;
    }

    private function updateEntity(LiveLesson $liveLesson, array $data) : LiveLesson {
        $liveLesson
            ->setTitle($data['title'])
            ->setStart($data['start'])
            ->setMeetupUrl($data['url']);
        return $liveLesson;
    }

    private function saveEntity(LiveLesson $liveLesson) : bool {
        $result;
        try {
            $this->em->persist($liveLesson);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    private function deleteEntity(LiveLesson $liveLesson) : bool {
        $result;
        try {
            $this->em->remove($liveLesson);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    private function getCreateValidationError(User $user, StudentGroup $group, array $data, ?LiveLesson $current = null) : ?string {
        $error = null;
        if($group->getTeacher()->getId() != $user->getId()) {
            $error = 'access.denied';
        } else if(!array_key_exists('title', $data) || gettype($data['title']) != 'string' || strlen(trim($data['title'])) == 0) {
            $error = 'title.empty';
        } else if(!array_key_exists('start', $data) || !($data['start'] instanceof \DateTime)) {
            $error = 'start.empty';
        } else if(!array_key_exists('url', $data)) {
            $error = 'url.not.set';
        }
        return $error;
    }
}