<?php
namespace App\Test\Service;

use App\Service\LessonService;
use App\Result\LessonResult;
use App\Entity\Unit;
use App\Entity\User;
use App\Entity\Lesson;
use App\Entity\LessonAttachment;
use App\Exception\EduException;
use App\Exception\LessonException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class LessonServiceTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        //LessonService
        $this->service = $container->get('App\Service\LessonService');
        $this->em = $container->get('doctrine')->getManager();
    }
    
    private function getStaticData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $unitRepo = $this->em->getRepository(Unit::class);
        $lessonRepo = $this->em->getRepository(Lesson::class);
        $attachmentRepo = $this->em->getRepository(LessonAttachment::class);
        $users = [
            $userRepo->findOneBy(['id' => 130]),
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
            $userRepo->findOneBy(['id' => 136]),
        ];


        /*
        Unit teachers:

        [0] - {[0], [1], [2], [4], [5]}
        [1] - {}
        [2] - {[0], [1], [2], [4], [5]}
        [3] - {[0], [1]}
        [4] - {[0], [1]}
        [5] - {[0], [1]}
        [6] - {[0], [1]}
        [7] - {[0], [1]}
        [8] - {[0], [1]}

        */
        $units = [
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

        /*
        Lesson Unit
        all - [0]
        */

        $lessons = [
            $lessonRepo->findOneBy(['id' => 11]),
            $lessonRepo->findOneBy(['id' => 12]),
            $lessonRepo->findOneBy(['id' => 13]),
            $lessonRepo->findOneBy(['id' => 14]),
            $lessonRepo->findOneBy(['id' => 15]),
            $lessonRepo->findOneBy(['id' => 16])
        ];

        /* 
        attachment lessons

        */

        $attachments = [
            $attachmentRepo->findOneBy(['id' => 20]),
            $attachmentRepo->findOneBy(['id' => 21]),
            $attachmentRepo->findOneBy(['id' => 22]),
            $attachmentRepo->findOneBy(['id' => 23]),
            $attachmentRepo->findOneBy(['id' => 24]),
            $attachmentRepo->findOneBy(['id' => 25]),
            $attachmentRepo->findOneBy(['id' => 26]),
            $attachmentRepo->findOneBy(['id' => 27]),
            $attachmentRepo->findOneBy(['id' => 28]),
            $attachmentRepo->findOneBy(['id' => 29]),
            $attachmentRepo->findOneBy(['id' => 30]),
            $attachmentRepo->findOneBy(['id' => 31]),
        ];

        return [
            'users' => $users,
            'units' => $units,
            'lessons' => $lessons,
            'attachments' => $attachments
        ];
    }

    /*
                'attachments' => [
                    new UploadedFile(
                        '/tmp/groups.PNG',
                        'groups.PNG',
                        'image/png',
                        null
                    ),
                    new UploadedFile(
                        '/tmp/lesson2.PNG',
                        'lesson2.PNG',
                        'image/png',
                        null
                    ),
                ],
    */

/*
                'video' => new UploadedFile(
                    '/tmp/sample.mp4',
                    'sample.mp4',
                    'video/mp4',
                    null
                ),
*/

    private function getCreateData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => '  ' . md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => null,
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => null,
                    'attachments' => []
                ],
            ],
            //bad========================================================================================
            //access
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][4],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            //title
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][0],
                'data' => [
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => '',
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => '    ',
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => 'Example lesson',
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => '   Example lesson',
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => null,
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => 75,
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            //text
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][3],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'publish' => false,
                    'text' => 8907,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            //publish
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => null,
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => 'wefg',
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            //order_number
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 'fewg',
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => null,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            //video
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'yt_id' => 'XDD',
                    'attachments' => []
                ],
            ],
            //yt_id
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'attachments' => []
                ],
            ],
            //video & yt_id
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => null,
                    'yt_id' => null,
                    'attachments' => []
                ],
            ],
            //attachments
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                    'attachments' => null
                ],
            ],
            [
                'unit' => $dbData['units'][2],
                'user' => $dbData['users'][0],
                'data' => [
                    'title' => md5(uniqid() . random_bytes(32)),
                    'text' => '',
                    'publish' => false,
                    'order_number' => 100,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => 'XDD',
                ],
            ],
        ];
    }

    private function getUpdateData() : array {
        $dbData = $this->getStaticData();
        /*
        no yt_id - {[0] [3]}
        no video - {[2], [5]}
        */
        return [
            //good
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][0],
                'data' => [
                    'title' => $dbData['lessons'][0]->getTitle(),
                    'text' => $dbData['lessons'][0]->getText(),
                    'publish' => !$dbData['lessons'][0]->getHidden(),
                    'order_number' => $dbData['lessons'][0]->getWeight(),
                    'video' => null,
                    'yt_id' => null,
                ],
            ],
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][3],
                'data' => [
                    'title' => $dbData['lessons'][3]->getTitle(),
                    'text' => $dbData['lessons'][3]->getText(),
                    'publish' => !$dbData['lessons'][3]->getHidden(),
                    'order_number' => $dbData['lessons'][3]->getWeight(),
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => null,
                ],
            ],
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][2],
                'data' => [
                    'title' => $dbData['lessons'][2]->getTitle(),
                    'text' => $dbData['lessons'][2]->getText(),
                    'publish' => !$dbData['lessons'][2]->getHidden(),
                    'order_number' => $dbData['lessons'][2]->getWeight(),
                    'video' => null,
                    'yt_id' => $dbData['lessons'][2]->getRemoteVideo(),
                ],
            ],
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][3],
                'data' => [
                    'title' => $dbData['lessons'][3]->getTitle(),
                    'text' => $dbData['lessons'][3]->getText(),
                    'publish' => !$dbData['lessons'][3]->getHidden(),
                    'order_number' => 90,
                    'video' => null,
                    'yt_id' => $dbData['lessons'][3]->getRemoteVideo(),
                ],
            ],
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][5],
                'data' => [
                    'title' => $dbData['lessons'][5]->getTitle(),
                    'text' => $dbData['lessons'][5]->getText(),
                    'publish' => !$dbData['lessons'][5]->getHidden(),
                    'order_number' => 90,
                    'video' => new UploadedFile(
                        '/tmp/sample.mp4',
                        'sample.mp4',
                        'video/mp4',
                        null
                    ),
                    'yt_id' => null,
                ],
            ],
            //bad======================================================================
            //access
            [
                'user' => $dbData['users'][1],
                'lesson' => $dbData['lessons'][3],
                'data' => [
                    'title' => $dbData['lessons'][3]->getTitle(),
                    'text' => $dbData['lessons'][3]->getText(),
                    'publish' => !$dbData['lessons'][3]->getHidden(),
                    'order_number' => 90,
                    'video' => null,
                    'yt_id' => $dbData['lessons'][3]->getRemoteVideo(),
                ],
            ],
            //name
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][3],
                'data' => [
                    'title' => $dbData['lessons'][2]->getTitle(),
                    'text' => $dbData['lessons'][3]->getText(),
                    'publish' => !$dbData['lessons'][3]->getHidden(),
                    'order_number' => 90,
                    'video' => null,
                    'yt_id' => $dbData['lessons'][3]->getRemoteVideo(),
                ],
            ],
            //video & yt_id
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][2],
                'data' => [
                    'title' => $dbData['lessons'][2]->getTitle(),
                    'text' => $dbData['lessons'][2]->getText(),
                    'publish' => !$dbData['lessons'][2]->getHidden(),
                    'order_number' => 90,
                    'video' => null,
                    'yt_id' => null,
                ],
            ],
        ];
    }

    private function getAttachmentAddData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            [
                'user' => $dbData['users'][0],
                'lesson' => $dbData['lessons'][0],
                'data' => new UploadedFile(
                    '/tmp/lesson2.PNG',
                    'lesson2.PNG',
                    'image/png',
                    null
                )
            ]
            //bad=======================================================
            //access
            [
                'user' => $dbData['users'][1],
                'lesson' => $dbData['lessons'][0],
                'data' => new UploadedFile(
                    '/tmp/lesson2.PNG',
                    'lesson2.PNG',
                    'image/png',
                    null
                )
            ]
        ];
    }

    private function getAttachmentRemoveData() : array {
        $dbData = $this->getStaticData();
        return [
            [
                $dbData = $this->getStaticData();
                return [
                    //good
                    [
                        'user' => $dbData['users'][0],
                        'lesson' => $dbData['lessons'][0],
                        'attachments' => $dbData['attachments'][0]
                    ]
                    //bad=======================================================
                    //access
                    [
                        'user' => $dbData['users'][1],
                        'lesson' => $dbData['lessons'][0],
                        'data' => $dbData['attachments'][0]
                    ]
                ];
            ]
        ];
    }

    private function getHideData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            [
                
            ]
            //bad=======================================
            //access
        ];
    }

    private function getShowData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            [

            ]
            //bad=======================================
            //access
        ];
    }

    private function getDeleteData() : array {
        $dbData = $this->getStaticData();
        return [
            //good
            [

            ]
            //bad=======================================
            //access

        ];
    }

    // public function testCreate() {
    //     $goodCount = 4;
    //     $this->init();
    //     $data = $this->getCreateData();
    //     $results = [];
    //     foreach($data as $row) {
    //         $results[] = $this->service->create($row['user'], $row['unit'], $row['data']);
    //     }

    //     for($i = 0; $i < $goodCount; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof LessonResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Lesson);
            
    //         $lessonDb = $this->em->getRepository(Lesson::class)->findOneBy(['id' => $result->getData()->getId()]);
    //         $this->assertTrue($lessonDb instanceof Lesson);
    //     }
    //     for($i = $goodCount; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof LessonResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //     }
    // }

    // public function testUpdate() {
    //     $goodCount = 5;
    //     $this->init();
    //     $data = $this->getUpdateData();
    //     $results = [];
    //     foreach($data as $row) {
    //         $results[] = $this->service->update($row['user'], $row['lesson'], $row['data']);
    //     }

    //     for($i = 0; $i < $goodCount; $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof LessonResult);
    //         $this->assertTrue($result->getSuccess());
    //         $this->assertNull($result->getError());
    //         $this->assertTrue($result->getData() instanceof Lesson);
    //     }
    //     for($i = $goodCount; $i < count($results); $i++) {
    //         $result = $results[$i];
    //         $this->assertTrue($result instanceof LessonResult);
    //         $this->assertFalse($result->getSuccess());
    //         $this->assertTrue($result->getError() instanceof EduException);
    //         $this->assertNull($result->getData());
    //     }
    // }

    public function testAttachmentAdd() {
        $goodCount = 0;
        $this->init();
        $data = $this->getAttachmentAddData();
        $results = [];

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Lesson);
        }
        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
        }

    }

    public function testAttachmentRemove() {
        $goodCount = 0;
        $this->init();
        $data = $this->getAttachmentRemoveData();
        $results = [];

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Lesson);
        }
        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
        }

    }

    public function testHide() {
        $goodCount = 0;
        $this->init();
        $data = $this->getHideData();
        $results = [];

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Lesson);
        }
        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
        }
    }

    public function testShow() {
        $goodCount = 0;
        $this->init();
        $data = $this->getShowData();
        $results = [];

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Lesson);
        }
        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
        }

    }

    public function testDelete() {
        $goodCount = 0;
        $this->init();
        $data = $this->getDeleteData();
        $results = [];

        for($i = 0; $i < $goodCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof Lesson);
        }
        for($i = $goodCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof LessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
        }

    }
}
