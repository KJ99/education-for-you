<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\Subject;

class SubjectResult extends Result {
    
    public function setData(Subject $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?Subject {
        return $this->data;
    }
}