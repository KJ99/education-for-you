<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\Picture;

class PictureResult extends Result {
    
    public function setData(?Picture $picture) : self {
        $this->data = $picture;
        return $this;
    }

    public function getData() : ?Picture {
        return $this->data;
    }
}