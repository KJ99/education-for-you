<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\GroupJoinRequest;

class GroupRequestResult extends Result {
    
    public function setData(GroupJoinRequest $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?GroupJoinRequest {
        return $this->data;
    }
}