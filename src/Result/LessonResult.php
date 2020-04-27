<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\Lesson;

class LessonResult extends Result {
    
    public function setData(Lesson $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?Lesson {
        return $this->data;
    }
}