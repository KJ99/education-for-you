<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\Level;

class LevelResult extends Result {
    
    public function setData(Level $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?Level {
        return $this->data;
    }
}