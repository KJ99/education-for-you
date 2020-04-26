<?php
namespace App\Processor;

use Doctrine\ORM\EntityManagerInterface;

class Processor {
    protected $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
}