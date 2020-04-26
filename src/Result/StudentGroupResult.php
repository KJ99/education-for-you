<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\StudentGroup;

class StudentGroupResult extends Result {
    
    public function setData(StudentGroup $group) : self {
        $this->data = $group;
        return $this;
    }

    public function getData() : ?StudentGroup {
        return $this->data;
    }
}