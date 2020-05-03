<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\GroupInviteToken;

class GroupInviteTokenResult extends Result {
    
    public function setData(GroupInviteToken $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?GroupInviteToken {
        return $this->data;
    }
}