<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\LiveLesson;

class LiveLessonResult extends Result {
    
    public function setData(LiveLesson $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?LiveLesson {
        return $this->data;
    }
}