<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;

class EntityService {
    protected $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }
}