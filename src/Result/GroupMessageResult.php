<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\GroupMessage;

class GroupMessageResult extends Result {
    
    public function setData(GroupMessage $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?GroupMessage {
        return $this->data;
    }
}