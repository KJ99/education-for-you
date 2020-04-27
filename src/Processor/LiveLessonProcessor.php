<?php
namespace App\Processor;

use App\Entity\StudentGroup;
use App\Entity\Level;
use App\Entity\User;

use App\Result\StudentGroupResult;
use App\Exception\StudentGroupException;
use App\Exception\DatabaseException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;

class LiveLessonProcessor extends Processor {

}