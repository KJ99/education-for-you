<?php
namespace App\Test\Service;

use App\Service\SubjectService;
use App\Result\SubjectResult;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Picture;
use App\Exception\EduException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class SubjectServiceTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        $this->service = $container->get('App\Service\SubjectService');
        $this->em = $container->get('doctrine')->getManager();
    }
    
    private function getCreateData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $admin = $userRepo->findOneBy(['id' => 130]);
        $teachers = [
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
        ];
        return [
            ['user' => $admin, 'data' => [
                'name' => 'Polish', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'German', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'English', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => false, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'Biology', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => false, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'History', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => null
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'Statistics', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'Maths', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [78, 82]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'Informatics', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => 789
            ]],
            //=====================================================================================================
            ['user' => $admin, 'data' => [
                'name' => null, 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => '   ', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'Statistics', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => ' Statistics  ', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],

            ['user' => $admin, 'data' => [
                'name' => 'Geography', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => 'xswf', 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'Physics', 
                'banner' => null, 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'UTK', 
                'banner' => new UploadedFile(
                    '/tmp/files_list',
                    'files_list',
                    'text/plain',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'UTK', 
                'banner' => 89, 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'ASSO', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => null, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'Linux', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => 90, 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $admin, 'data' => [
                'name' => 'WiA', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'publish' => true, 
                'teachers' => [$teachers[0], $teachers[1]]
            ]],
            ['user' => $teachers[0], 'data' => [
                'name' => 'Russian', 
                'banner' => new UploadedFile(
                    '/tmp/cieszyn.PNG',
                    'cieszyn.PNG',
                    'image/png',
                    null
                ), 
                'coordinator' => $admin, 
                'publish' => true, 
                'teachers' => []
            ]],
            
        ];
    }

    private function getTeacherAddData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $subjectRepo = $this->em->getRepository(Subject::class);
        $admin = $userRepo->findOneBy(['id' => 130]);
        $teachers = [
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
            $userRepo->findOneBy(['id' => 136]),
        ];
        $subjects = [
            $subjectRepo->findOneBy(['id' => 62]),
            $subjectRepo->findOneBy(['id' => 63]),
            $subjectRepo->findOneBy(['id' => 70]),
            $subjectRepo->findOneBy(['id' => 71]),
            $subjectRepo->findOneBy(['id' => 72]),
            $subjectRepo->findOneBy(['id' => 73]),
            $subjectRepo->findOneBy(['id' => 74]),
            $subjectRepo->findOneBy(['id' => 75]),
            $subjectRepo->findOneBy(['id' => 76]),
            $subjectRepo->findOneBy(['id' => 77]),
        ];
        return [
            //good
            [
                'user' => $admin,
                'teachers' => [$teachers[4], $teachers[3]],
                'subject' => $subjects[0],
            ],
            [
                'user' => $teachers[0],
                'teachers' => [$teachers[4]],
                'subject' => $subjects[1],
            ],
            //bad
            [
                'user' => $teachers[1],
                'teachers' => [$teachers[3]],
                'subject' => $subjects[1],
            ],
            [
                'user' => $teachers[0],
                'teachers' => [$teachers[2], $teachers[5]],
                'subject' => $subjects[1],
            ],
        ];
    }

    private function getTeacherRemoveData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $subjectRepo = $this->em->getRepository(Subject::class);
        $admin = $userRepo->findOneBy(['id' => 130]);
        $teachers = [
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
        ];
        $subjects = [
            $subjectRepo->findOneBy(['id' => 62]),
            $subjectRepo->findOneBy(['id' => 63]),
            $subjectRepo->findOneBy(['id' => 70]),
            $subjectRepo->findOneBy(['id' => 71]),
            $subjectRepo->findOneBy(['id' => 72]),
            $subjectRepo->findOneBy(['id' => 73]),
            $subjectRepo->findOneBy(['id' => 74]),
            $subjectRepo->findOneBy(['id' => 75]),
            $subjectRepo->findOneBy(['id' => 76]),
            $subjectRepo->findOneBy(['id' => 77]),
        ];

        //subject - 47 (1)
        // coordinator - 131 (0)
        // teachers: 130 ($admin), 131 (0), 132 (1), 135 (4)
        return [
            //good
            [
                'user' => $admin,
                'subject' => $subjects[1],
                'teacher' => $teachers[4]
            ],
            [
                'user' => $teachers[0],
                'subject' => $subjects[1],
                'teacher' => $teachers[1]
            ],
            //bad
            [
                'user' => $teachers[1],
                'subject' => $subjects[2],
                'teacher' => $teachers[0]
            ],
            [
                'user' => $admin,
                'subject' => $subjects[1],
                'teacher' => $teachers[0]
            ],
            
        ];
    }

    private function getDeleteData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $subjectRepo = $this->em->getRepository(Subject::class);
        $admin = $userRepo->findOneBy(['id' => 130]);
        $teachers = [
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
        ];
        $subjects = [
            $subjectRepo->findOneBy(['id' => 62]),
            $subjectRepo->findOneBy(['id' => 63]),
            $subjectRepo->findOneBy(['id' => 70]),
            $subjectRepo->findOneBy(['id' => 71]),
            $subjectRepo->findOneBy(['id' => 72]),
            $subjectRepo->findOneBy(['id' => 73]),
            $subjectRepo->findOneBy(['id' => 74]),
            $subjectRepo->findOneBy(['id' => 75]),
            $subjectRepo->findOneBy(['id' => 76]),
            $subjectRepo->findOneBy(['id' => 77]),
        ];
        return [
            //good
            [
                'user' => $admin,
                'subject' => $subjects[7],
                'subject_id' => $subjects[7]->getId(),
                'banner_id' => $subjects[7]->getBanner()->getId()
            ],
            [
                'user' => $admin,
                'subject' => $subjects[8],
                'subject_id' => $subjects[8]->getId(),
                'banner_id' => $subjects[8]->getBanner()->getId()
            ],
            [
                'user' => $admin,
                'subject' => $subjects[9],
                'subject_id' => $subjects[9]->getId(),
                'banner_id' => $subjects[9]->getBanner()->getId()
            ],
            //bad
            [
                'user' => $teachers[1],
                'subject' => $subjects[0],
                'subject_id' => $subjects[0]->getId(),
                'banner_id' => $subjects[0]->getBanner()->getId()
            ],
            [
                'user' => $teachers[2],
                'subject' => $subjects[1],
                'subject_id' => $subjects[1]->getId(),
                'banner_id' => $subjects[1]->getBanner()->getId()
            ],
        ];
    }

    private function getHideShowData() {
        $userRepo = $this->em->getRepository(User::class);
        $subjectRepo = $this->em->getRepository(Subject::class);
        $admin = $userRepo->findOneBy(['id' => 130]);
        $teachers = [
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
        ];
        $subjects = [
            $subjectRepo->findOneBy(['id' => 62]),
            $subjectRepo->findOneBy(['id' => 63]),
            $subjectRepo->findOneBy(['id' => 73]),
            $subjectRepo->findOneBy(['id' => 74])
        ];
        return [
            //good
            [
                'user' => $admin,
                'subject' => $subjects[2]
            ],
            [
                'user' => $teachers[0],
                'subject' => $subjects[3]
            ],
            //bad
            [
                'user' => $teachers[1],
                'subject' => $subjects[0]
            ],
            [
                'user' => $teachers[2],
                'subject' => $subjects[1]
            ],
        ];
    }

    // public function testCreate() {
    //     $this->init();
    //     $data = $this->getCreateData();
    //     $results = [];
    //     foreach($data as $subject) {
    //         $results[] = $this->service->create($subject['user'], $subject['data']);
    //     }

    //     for($i = 0; $i < 8; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Subject);
    //         $subject = $result->getData();
    //         $dbSubject = $this->em->getRepository(Subject::class)->findOneBy(['name' => $subject->getName()]);
    //         $this->assertTrue($dbSubject->getId() == $subject->getId());
    //     }  

    //     for($i = 8; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertNull($result->getData());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //     }

    // }

    // public function testTeacherAdd() {
    //     $this->init();
    //     $data = $this->getTeacherAddData();
    //     $results = [];
    //     foreach ($data as $row) {
    //         $results[] = $this->service->addTeachers($row['user'], $row['subject'], $row['teachers']);
    //     }
    //     for($i = 0; $i < 2; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Subject);
    //         $subjectDb = $this->em->getRepository(Subject::class)->findOneBy(['id' => $result->getData()->getId()]);
    //         $this->assertTrue($subjectDb instanceof Subject);
    //         $teachersToAdd = $data[$i]['teachers'];
    //         foreach($teachersToAdd as $teacher) {
    //             $this->assertTrue($subjectDb->getTeachers()->contains($teacher));
    //         }
    //     }

    //     for($i = 2; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //     }
    // }

    // public function testTeacherRemove() {
    //     $this->init();
    //     $data = $this->getTeacherRemoveData();
    //     $results = [];
    //     foreach ($data as $row) {
    //         $results[] = $this->service->removeTeacher($row['user'], $row['subject'], $row['teacher']);
    //     }
    //     for($i = 0; $i < 2; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Subject);
    //         $subjectDb = $this->em->getRepository(Subject::class)->findOneBy(['id' => $result->getData()->getId()]);
    //         $this->assertTrue($subjectDb instanceof Subject);
    //         $teacher = $data[$i]['teacher'];
    //         $this->assertFalse($subjectDb->getTeachers()->contains($teacher));
    //     }

    //     for($i = 2; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //     }
    // }

    // public function testDelete() {
    //     $this->init();
    //     $data = $this->getDeleteData();
    //     $results = [];
    //     foreach ($data as $row) {
    //         $results[] = $this->service->deleteSubject($row['user'], $row['subject']);
    //     }
    //     for($i = 0; $i < 3; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Subject);
    //         $subjectDb = $this->em->getRepository(Subject::class)->findOneBy(['id' => $data[$i]['subject_id']]);
    //         $pictureDb = $this->em->getRepository(Picture::class)->findOneBy(['id' => $data[$i]['banner_id']]);
    //         $this->assertNull($subjectDb);
    //         $this->assertNull($pictureDb);
    //     }

    //     for($i = 3; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //     }
    // }
    
    // public function testHide() {
    //     $this->init();
    //     $data = $this->getHideShowData();
    //     $results = [];
    //     foreach($data as $row) {
    //         $results[] = $this->service->hideSubject($row['user'], $row['subject']);
    //     }
    //     for($i = 0; $i < 2; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Subject);
    //         $subjectDb = $this->em->getRepository(Subject::class)->findOneBy(['id' => $data[$i]['subject']->getId()]);
    //         $this->assertTrue($subjectDb instanceof Subject);
    //         $this->assertTrue($subjectDb->getHidden());
    //     }

    //     for($i = 2; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //     }
    // }

    // public function testShow() {
    //     $this->init();
    //     $data = $this->getHideShowData();
    //     $results = [];
    //     foreach($data as $row) {
    //         $results[] = $this->service->showSubject($row['user'], $row['subject']);
    //     }
    //     for($i = 0; $i < 2; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Subject);
    //         $subjectDb = $this->em->getRepository(Subject::class)->findOneBy(['id' => $data[$i]['subject']->getId()]);
    //         $this->assertTrue($subjectDb instanceof Subject);
    //         $this->assertFalse($subjectDb->getHidden());
    //     }

    //     for($i = 2; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof SubjectResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //     }
    // }
}
