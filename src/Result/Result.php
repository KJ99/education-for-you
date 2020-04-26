<?php
namespace App\Result;
use App\Exception\EduException;

class Result {
    protected $success;
    protected $data;
    protected $error;

    public function setError(EduException $error) {
        $this->error = $error;
        return $this;
    }

    public function setSuccess(bool $success) : self {
        $this->success = $success;
        return $this;
    }

    public function getSuccess() : bool {
        return $this->success;
    }

    public function getError() : ?EduException {
        return $this->error;
    }
}