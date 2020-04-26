<?php
namespace App\Result;

use App\Result\Result;
use App\Exception\EduException;
use App\Entity\UserToken;

class UserTokenResult extends Result {
    
    public function setData(UserToken $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?UserToken {
        return $this->data;
    }
}