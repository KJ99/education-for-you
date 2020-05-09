<?php
namespace App\Result;

use App\Result\Result;

class AdminSiteSettingsResult extends Result {
    
    public function setData($data) : self {
        $this->data = $data;
        return $this;
    }

    public function getData() {
        return $this->data;
    }
}