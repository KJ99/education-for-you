<?php
namespace App\Test\Service;

use App\Service\LevelService;
use App\Result\LevelResult;
use App\Entity\Subject;
use App\Entity\User;
use App\Entity\Level;
use App\Exception\EduException;
use App\Exception\LevelException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class LevelServiceTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        //LevelService
        $this->service = $container->get('App\Service\LevelService');
        $this->em = $container->get('doctrine')->getManager();
    }
    
    private function getStaticData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $subjectRepo = $this->em->getRepository(Subject::class);
        $levelRepo = $this->em->getRepository(Level::class);
        $admin = $userRepo->findOneBy(['id' => 130]);
        $teachers = [
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
        ];
        $levels =  [
            $levelRepo->findOneBy(['id' => 56]),
            $levelRepo->findOneBy(['id' => 57]),
            $levelRepo->findOneBy(['id' => 58]),
            $levelRepo->findOneBy(['id' => 59]),
            $levelRepo->findOneBy(['id' => 60]),
        ];
        $subjects = [
            $subjectRepo->findOneBy(['id' => 62]),
            $subjectRepo->findOneBy(['id' => 63]),
            $subjectRepo->findOneBy(['id' => 73]),
            $subjectRepo->findOneBy(['id' => 74]),
        ];
        /*
        coordinators:
        [0] - [admin]
        [1] - [0] 
        [2] - [0]
        [3] - [0]
        */
        return ['admin' => $admin, 'teachers' => $teachers, 'levels' => $levels, 'subjects' => $subjects];
    }

    private function getCreateData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            [
                'user' => $dbData['admin'],
                'subject' => $dbData['subjects'][0],
                'data' => [
                    'name' => 'Foundation',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['admin'],
                'subject' => $dbData['subjects'][0],
                'data' => [
                    'name' => 'Advanced',
                    'order_number' => 2,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['admin'],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Foundation',
                    'order_number' => 8,
                    'publish' => false,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => '  Beginner      ',
                    'order_number' => 16,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Get started',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            //bad ========================================================================================
            //access denied
            [
                'user' => $dbData['teachers'][1],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'XDD',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            //name
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => null,
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 85,
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => '    ',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Beginner',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => '  Beginner  ',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            //order number
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Hubabuba',
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Hubabuba2',
                    'order_number' => null,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Hubabuba3',
                    'order_number' => 'nanana',
                    'publish' => true,
                ]
            ],
            //publish
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Hubabuba4',
                    'order_number' => 1,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Hubabuba5',
                    'order_number' => 1,
                    'publish' => null,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'subject' => $dbData['subjects'][1],
                'data' => [
                    'name' => 'Hubabuba6',
                    'order_number' => 1,
                    'publish' => 'trrweg',
                ]
            ]
        ];
    }

    private function getUpdateData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            //admin
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][0],
                'original_level_data' => [
                    'name' => $dbData['levels'][0]->getName(),
                    'order_number' => $dbData['levels'][0]->getWeight(),
                    'publish' => !$dbData['levels'][0]->getHidden()
                ],
                'data' => [
                    'name' => 'Semi-Advanced',
                    'order_number' => $dbData['levels'][0]->getWeight(),
                    'publish' => false,
                ]
            ],
            //coordinator
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][2],
                'original_level_data' => [
                    'name' => $dbData['levels'][2]->getName(),
                    'order_number' => $dbData['levels'][2]->getWeight(),
                    'publish' => !$dbData['levels'][2]->getHidden()
                ],
                'data' => [
                    'name' => $dbData['levels'][2]->getName(),
                    'order_number' => -1,
                    'publish' => $dbData['levels'][2]->getHidden(),
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][3],
                'original_level_data' => [
                    'name' => $dbData['levels'][3]->getName(),
                    'order_number' => $dbData['levels'][3]->getWeight(),
                    'publish' => !$dbData['levels'][3]->getHidden()
                ],
                'data' => [
                    'name' => $dbData['levels'][3]->getName(),
                    'order_number' => -1,
                    'publish' => $dbData['levels'][3]->getHidden(),
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][4],
                'original_level_data' => [
                    'name' => $dbData['levels'][4]->getName(),
                    'order_number' => $dbData['levels'][4]->getWeight(),
                    'publish' => !$dbData['levels'][4]->getHidden()
                ],
                'data' => [
                    'name' =>  $dbData['levels'][4]->getName(),
                    'order_number' => -1,
                    'publish' => true,
                ]
            ],
            //bad================================================================
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][4],
                'original_level_data' => [
                    'name' => $dbData['levels'][4]->getName(),
                    'order_number' => $dbData['levels'][4]->getWeight(),
                    'publish' => !$dbData['levels'][4]->getHidden()
                ],
                'data' => [
                    'name' =>  $dbData['levels'][3]->getName(),
                    'order_number' => -1,
                    'publish' => true,
                ]
            ],
        ];
    }

    private function getHideData() : array {
        $dbData = $this->getStaticData();
        return [
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][0],
            ],
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][3],
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][2],
            ],
            //bad
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][0],
            ],
        ];
    }

    private function getShowData() : array {
        $dbData = $this->getStaticData();
        return [
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][1],
            ],
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][3],
            ],
            //bad
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][0],
            ],
        ];
    }

    private function getDeleteData() : array {
        $dbData = $this->getStaticData();
        return [
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][1],
                'level_id' => $dbData['levels'][1]->getId()
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][4],
                'level_id' => $dbData['levels'][4]->getId()
            ],
            //bad
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][0],
                'level_id' => $dbData['levels'][0]->getId()
            ],
        ];
    }

    public function testCreate() {
        $goodCount = 5;
        $this->init();
        $data = $this->getCreateData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->create($row['user'], $row['subject'], $row['data']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Level);
            //Addditional asserts depends on case
            $levelDb = $this->em->getRepository(Level::class)->findOneBy(['id' => $result->getData()->getId()]);
            $this->assertTrue($levelDb instanceof Level);

        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
        }
    }

    public function testUpdate() {
        $goodCount = 4;
        $this->init();
        $data = $this->getUpdateData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->update($row['user'], $row['level'], $row['data']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Level);
            //Addditional asserts depends on case
            $levelData = $data[$i]['data'];
            $levelDb = $this->em->getRepository(Level::class)->findOneBy(['id' => $data[$i]['level']->getId()]);
            $this->assertTrue($levelDb instanceof Level);
            $this->assertSame($levelDb->getName(), $levelData['name']);
            $this->assertSame($levelDb->getHidden(), !$levelData['publish']);
        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
            $levelData = $data[$i]['original_level_data'];
            $levelDb = $this->em->getRepository(Level::class)->findOneBy(['id' => $data[$i]['level']->getId()]);
            $this->assertSame($levelDb->getName(), $levelData['name']);
            $this->assertSame($levelDb->getWeight(), $levelData['order_number']);
            $this->assertSame($levelDb->getHidden(), !$levelData['publish']);
        }
    }

    public function testHide() {
        $goodCount = 3;
        $this->init();
        $data = $this->getHideData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->hide($row['user'], $row['level']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Level);
            //Addditional asserts depends on case
            $this->assertTrue($result->getData()->getHidden());
        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
        }
    }

    public function testShow() {
        $goodCount = 2;
        $this->init();
        $data = $this->getShowData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->show($row['user'], $row['level']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Level);
            //Addditional asserts depends on case
            $this->assertFalse($result->getData()->getHidden());

        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
        }
    }

    public function testDelete() {
        $goodCount = 2;
        $this->init();
        $data = $this->getDeleteData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->delete($row['user'], $row['level']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Level);
            //Addditional asserts depends on case
            $levelDb = $this->em->getRepository(Level::class)->findOneBy(['id' => $data[$i]['level_id']]);
            $this->assertNull($levelDb);
        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LevelResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
            $levelDb = $this->em->getRepository(Level::class)->findOneBy(['id' => $data[$i]['level_id']]);
            $this->assertTrue($levelDb instanceof Level);
        
        }
    }
}
