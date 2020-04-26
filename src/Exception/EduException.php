<?php 
namespace App\Exception;

class EduException extends \Exception {
    protected $tag;

    public function __construct(string $tag) {
        $this->tag = $tag;
        parent::__construct($this->resolveMessage($tag));
    }

    protected function resolveMessage(string $tag) : string {
        return 'Unknown error occured';
    }
}