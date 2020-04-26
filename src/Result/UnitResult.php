<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\Unit;

class UnitResult extends Result {
    
    public function setData(Unit $data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() : ?Unit {
        return $this->data;
    }
}