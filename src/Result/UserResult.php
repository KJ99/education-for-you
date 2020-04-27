<?php
namespace App\Result;

use App\Result\Result;
use App\Exception\EduException;
use App\Entity\User;

class UserResult extends Result {
    
    public function setData(User $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?User {
        return $this->data;
    }
}