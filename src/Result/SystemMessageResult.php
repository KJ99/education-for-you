<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\SystemMessage;

class SystemMessageResult extends Result {
    
    public function setData(SystemMessage $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?SystemMessage {
        return $this->data;
    }
}