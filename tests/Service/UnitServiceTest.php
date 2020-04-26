<?php
namespace App\Test\Service;

use App\Service\UnitService;
use App\Result\UnitResult;
use App\Entity\Level;
use App\Entity\User;
use App\Entity\Unit;
use App\Exception\EduException;
use App\Exception\UnitException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class UnitServiceTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        //UnitService
        $this->service = $container->get('App\Service\UnitService');
        $this->em = $container->get('doctrine')->getManager();
    }
    
    private function getStaticData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $levelRepo = $this->em->getRepository(Level::class);
        $unitRepo = $this->em->getRepository(Unit::class);
        $admin = $userRepo->findOneBy(['id' => 130]);
        $teachers = [
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
        ];
        $units =  [
            $unitRepo->findOneBy(['id' => 21]),
            $unitRepo->findOneBy(['id' => 22]),
            $unitRepo->findOneBy(['id' => 23]),
            $unitRepo->findOneBy(['id' => 24]),
            $unitRepo->findOneBy(['id' => 25]),
            $unitRepo->findOneBy(['id' => 26]),
            $unitRepo->findOneBy(['id' => 27]),
            $unitRepo->findOneBy(['id' => 28]),
            $unitRepo->findOneBy(['id' => 29]),
        ];
        $levels = [
            $levelRepo->findOneBy(['id' => 56]),
            $levelRepo->findOneBy(['id' => 58]),
            $levelRepo->findOneBy(['id' => 59]),
        ];
        /*
        coordinators:
        [0-1] - [admin]
        [2-4] - [0]
        */
        return ['admin' => $admin, 'teachers' => $teachers, 'units' => $units, 'levels' => $levels];
    }

    private function getCreateData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][0],
                'data' => [
                    'name' => 'Hello, World',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][0],
                'data' => [
                    'name' => 'Sport',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][0],
                'data' => [
                    'name' => 'Traveling',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Hello, World',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Sport',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['admin'],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Traveling',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],

            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][2],
                'data' => [
                    'name' => 'Hello, World',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][2],
                'data' => [
                    'name' => 'Sport',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][2],
                'data' => [
                    'name' => 'Traveling',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            //bad ========================================================================================
            //access denied
            [
                'user' => $dbData['teachers'][1],
                'level' => $dbData['levels'][0],
                'data' => [
                    'name' => 'XDD',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            //name
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => null,
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 85,
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => '    ',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Sport',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => '  Sport  ',
                    'order_number' => 1,
                    'publish' => true,
                ]
            ],
            //order number
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Hubabuba',
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Hubabuba2',
                    'order_number' => null,
                    'publish' => true,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Hubabuba3',
                    'order_number' => 'nanana',
                    'publish' => true,
                ]
            ],
            //publish
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Hubabuba4',
                    'order_number' => 1,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
                'data' => [
                    'name' => 'Hubabuba5',
                    'order_number' => 1,
                    'publish' => null,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'level' => $dbData['levels'][1],
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
                'unit' => $dbData['units'][0],
                'original_unit_data' => [
                    'name' => $dbData['units'][0]->getName(),
                    'order_number' => $dbData['units'][0]->getWeight(),
                    'publish' => !$dbData['units'][0]->getHidden()
                ],
                'data' => [
                    'name' => 'Semi-Advanced',
                    'order_number' => $dbData['units'][0]->getWeight(),
                    'publish' => false,
                ]
            ],
            //coordinator
            [
                'user' => $dbData['teachers'][0],
                'unit' => $dbData['units'][2],
                'original_unit_data' => [
                    'name' => $dbData['units'][2]->getName(),
                    'order_number' => $dbData['units'][2]->getWeight(),
                    'publish' => !$dbData['units'][2]->getHidden()
                ],
                'data' => [
                    'name' => $dbData['units'][2]->getName(),
                    'order_number' => -1,
                    'publish' => $dbData['units'][2]->getHidden(),
                ]
            ],
            //bad================================================================
            [
                'user' => $dbData['teachers'][0],
                'unit' => $dbData['units'][4],
                'original_unit_data' => [
                    'name' => $dbData['units'][4]->getName(),
                    'order_number' => $dbData['units'][4]->getWeight(),
                    'publish' => !$dbData['units'][4]->getHidden()
                ],
                'data' => [
                    'name' =>  $dbData['units'][3]->getName(),
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
                'unit' => $dbData['units'][0],
            ],
            //bad
            [
                'user' => $dbData['teachers'][0],
                'unit' => $dbData['units'][0],
            ],
        ];
    }

    private function getShowData() : array {
        $dbData = $this->getStaticData();
        return [
            [
                'user' => $dbData['admin'],
                'unit' => $dbData['units'][1],
            ],
            //bad
            [
                'user' => $dbData['teachers'][0],
                'unit' => $dbData['units'][0],
            ],
        ];
    }

    private function getDeleteData() : array {
        $dbData = $this->getStaticData();
        return [
            [
                'user' => $dbData['admin'],
                'unit' => $dbData['units'][1],
                'unit_id' => $dbData['units'][1]->getId()
            ],
            //bad
            [
                'user' => $dbData['teachers'][0],
                'unit' => $dbData['units'][0],
                'unit_id' => $dbData['units'][0]->getId()
            ],
        ];
    }

    // public function testCreate() {
    //     $goodCount = 9;
    //     $this->init();
    //     $data = $this->getCreateData();
    //     $results = [];
    //     foreach($data as $row) {
    //         $results[] = $this->service->create($row['user'], $row['level'], $row['data']);
    //     }

    //     for($i = 0; $i < $goodCount; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof UnitResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Unit);
    //         //Addditional asserts depends on case
    //         $unitDb = $this->em->getRepository(Unit::class)->findOneBy(['id' => $result->getData()->getId()]);
    //         $this->assertTrue($unitDb instanceof Unit);

    //     }

    //     for($i = $goodCount; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof UnitResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //         //Addditional asserts depends on case
    //     }
    // }

    // public function testUpdate() {
    //     $goodCount = 1;
    //     $this->init();
    //     $data = $this->getUpdateData();
    //     $results = [];
    //     foreach($data as $row) {
    //         $results[] = $this->service->update($row['user'], $row['unit'], $row['data']);
    //     }

    //     for($i = 0; $i < $goodCount; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof UnitResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Unit);
    //         //Addditional asserts depends on case
    //         $unitData = $data[$i]['data'];
    //         $unitDb = $this->em->getRepository(Unit::class)->findOneBy(['id' => $data[$i]['unit']->getId()]);
    //         $this->assertTrue($unitDb instanceof Unit);
    //         $this->assertSame($unitDb->getName(), $unitData['name']);
    //         $this->assertSame($unitDb->getHidden(), !$unitData['publish']);
    //     }

    //     for($i = $goodCount; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof UnitResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //         //Addditional asserts depends on case
    //         $unitData = $data[$i]['original_unit_data'];
    //         $unitDb = $this->em->getRepository(Unit::class)->findOneBy(['id' => $data[$i]['unit']->getId()]);
    //         $this->assertSame($unitDb->getName(), $unitData['name']);
    //         $this->assertSame($unitDb->getWeight(), $unitData['order_number']);
    //         $this->assertSame($unitDb->getHidden(), !$unitData['publish']);
    //     }
    // }

    public function testHide() {
        $goodCount = 1;
        $this->init();
        $data = $this->getHideData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->hide($row['user'], $row['unit']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UnitResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Unit);
            //Addditional asserts depends on case
            $this->assertTrue($result->getData()->getHidden());
        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UnitResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
        }
    }

    public function testShow() {
        $goodCount = 1;
        $this->init();
        $data = $this->getShowData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->show($row['user'], $row['unit']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UnitResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Unit);
            //Addditional asserts depends on case
            $this->assertFalse($result->getData()->getHidden());

        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UnitResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
        }
    }

    public function testDelete() {
        $goodCount = 1;
        $this->init();
        $data = $this->getDeleteData();
        $results = [];
        foreach($data as $row) {
            $results[] = $this->service->delete($row['user'], $row['unit']);
        }

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UnitResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Unit);
            //Addditional asserts depends on case
            $unitDb = $this->em->getRepository(Unit::class)->findOneBy(['id' => $data[$i]['unit_id']]);
            $this->assertNull($unitDb);
        }

        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UnitResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
            //Addditional asserts depends on case
            $unitDb = $this->em->getRepository(Unit::class)->findOneBy(['id' => $data[$i]['unit_id']]);
            $this->assertTrue($unitDb instanceof Unit);
        
        }
    }
}
