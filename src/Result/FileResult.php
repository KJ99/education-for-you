<?php
namespace App\Result;

use App\Result\Result;
use App\Entity\File;

class FileResult extends Result {
    
    public function setData(File $file) : self {
        $this->data = $file;
        return $this;
    }

    public function getData() : ?File {
        return $this->data;
    }
}