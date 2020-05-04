<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\Message;

class MessageResult extends Result {
    
    public function setData(Message $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?Message {
        return $this->data;
    }
}