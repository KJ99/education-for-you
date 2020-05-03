<?php
namespace App\Test\Service;

use App\Service\StudentGroupService;
use App\Result\StudentGroupResult;
use App\Result\GroupRequestResult;
use App\Result\LiveLessonResult;
use App\Result\GroupInviteTokenResult;
use App\Entity\User;
use App\Entity\Level;
use App\Entity\GroupResource;
use App\Entity\GroupInviteToken;
use App\Entity\LiveLesson;
use App\Entity\GroupJoinRequest;
use App\Entity\StudentGroup;
use App\Exception\EduException;
use App\Exception\StudentGroupException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class StudentGroupServiceTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        $this->service = $container->get('App\Service\StudentGroupService');
        $this->em = $container->get('doctrine')->getManager();
    }
    
    private function getStaticData() : array {
        $userRepo = $this->em->getRepository(User::class);
        $levelRepo = $this->em->getRepository(Level::class);
        $groupRepo = $this->em->getRepository(StudentGroup::class);
        $requestRepo = $this->em->getRepository(GroupJoinRequest::class);
        $liveLessonRepo = $this->em->getRepository(LiveLesson::class);
        $resourceRepo = $this->em->getRepository(GroupResource::class);
        $tokenRepo = $this->em->getRepository(GroupInviteToken::class);

        $teachers = [
            $userRepo->findOneBy(['id' => 130]),
            $userRepo->findOneBy(['id' => 131]),
            $userRepo->findOneBy(['id' => 132]),
            $userRepo->findOneBy(['id' => 133]),
            $userRepo->findOneBy(['id' => 134]),
            $userRepo->findOneBy(['id' => 135]),
        ];

        $students = [
            $userRepo->findOneBy(['id' => 136]),
            $userRepo->findOneBy(['id' => 137]),
            $userRepo->findOneBy(['id' => 138]),
            $userRepo->findOneBy(['id' => 139]),
            $userRepo->findOneBy(['id' => 140]),
        ];

        $levels = [
            $levelRepo->findOneBy(['id' => 56]),
            $levelRepo->findOneBy(['id' => 58]),
            $levelRepo->findOneBy(['id' => 59]),
        ];

        $groups = [
            $groupRepo->findOneBy(['id' => 1]),
            $groupRepo->findOneBy(['id' => 2]),
            $groupRepo->findOneBy(['id' => 3]),
        ];

        $requests = [
            $requestRepo->findOneBy(['id' => 9]),
            $requestRepo->findOneBy(['id' => 13]),
            $requestRepo->findOneBy(['id' => 15]),
            $requestRepo->findOneBy(['id' => 17]),
        ];

        $liveLessons = [
            $liveLessonRepo->findOneBy(['id' => 2]),
            $liveLessonRepo->findOneBy(['id' => 3]),
        ];

        $resources = [
            $resourceRepo->findOneBy(['id' => 3]),
        ];

        $tokens = [
            $tokenRepo->findOneBy(['id' => 1]),
            $tokenRepo->findOneBy(['id' => 2]),
        ];

        return [
            'teachers' => $teachers,
            'students' => $students,
            'groups' => $groups,
            'requests' => $requests,
            'liveLessons' => $liveLessons,
            'resources' => $resources,
            'levels' => $levels,
            'tokens' => $tokens
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

    private function getCreateData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                [
                    'user' => $dbData['teachers'][0],
                    'data' => [
                        'level' => $dbData['levels'][1],
                        'name' => 'Żuczki 13fewf' .  md5(random_bytes(19)),
                        'avatar' => new UploadedFile(
                            '/tmp/lesson2.PNG',
                            'lesson2.PNG',
                            'image/png',
                            null
                        ),
                        'color' => '#000',
                        'auto_accept' => false,
                        'hidden' => false,
                    ]
                ],
            ],
            'bad' => [
                [
                    'user' => $dbData['teachers'][3],
                    'data' => [
                        'level' => $dbData['levels'][0],
                        'name' => 'Żuczki',
                        'avatar' => new UploadedFile(
                            '/tmp/lesson2.PNG',
                            'lesson2.PNG',
                            'image/png',
                            null
                        ),
                        'color' => '#000',
                        'auto_accept' => false,
                        'hidden' => false,
                    ]
                ],
                [
                    'user' => $dbData['teachers'][3],
                    'data' => [
                        'level' => $dbData['levels'][0],
                        'name' => 'Żuczki',
                        'avatar' => new UploadedFile(
                            '/tmp/lesson2.PNG',
                            'lesson2.PNG',
                            'image/png',
                            null
                        ),
                        'color' => '#000',
                        'auto_accept' => false,
                        'hidden' => false,
                    ]
                ],
            ]
        ];
    }

    private function getUpdateData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'original_data' => clone $dbData['groups'][0],
                'data' => [
                    'name' => 'Żuczki',
                    'avatar' => new UploadedFile(
                        '/tmp/lesson2.PNG',
                        'lesson2.PNG',
                        'image/png',
                        null
                    ),
                    'color' => '#000',
                    'auto_accept' => false,
                    'hidden' => false,
                ]
            ],
            'bad' => [
                'user' => $dbData['teachers'][5],
                'group' => $dbData['groups'][0],
                'original_data' => clone $dbData['groups'][0],
                'data' => [
                    'name' => 'Żuczki ewfgewg',
                    'avatar' => new UploadedFile(
                        '/tmp/lesson2.PNG',
                        'lesson2.PNG',
                        'image/png',
                        null
                    ),
                    'color' => '#000',
                    'auto_accept' => false,
                    'hidden' => false,
                ]
            ],
            [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'original_data' => clone $dbData['groups'][0],
                'data' => [
                    'name' => 'Żuczki ewfgewg',
                    'avatar' => new UploadedFile(
                        '/tmp/lesson2.PNG',
                        'lesson2.PNG',
                        'image/png',
                        null
                    ),
                    'color' => '#000',
                    'auto_accept' => false,
                    'hidden' => false,
                ]
            ],
        ];
    }

    private function getRemoveStudent() : array {        
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'student' => $dbData['students'][0]
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'student' => $dbData['students'][0]
            ]
        ];
    }

    private function getGroupLeaveData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
            ]
        ];
    }

    private function getResourceAddData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'data' => new UploadedFile(
                    '/tmp/lesson2.PNG',
                    'lesson2.PNG',
                    'image/png',
                    null
                )
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'data' => new UploadedFile(
                    '/tmp/lesson2.PNG',
                    'lesson2.PNG',
                    'image/png',
                    null
                )
            ]
        ];
    }

    private function getResourceRemoveData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'resource' => $dbData['resources'][0]
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'resource' => $dbData['resources'][0]
            ]
        ];
    }
    
    private function getJoinData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
            ]
        ];
    }
    
    private function getRequestAcceptData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'request' => $dbData['requests'][0],
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'request' => $dbData['requests'][0],
            ]
        ];
    }
    
    private function getRequestDeclineData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'request' => $dbData['requests'][0],
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'request' => $dbData['requests'][0],
            ]
        ];
    }
    
    private function getRequestCancelData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'request' => $dbData['requests'][0],
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'request' => $dbData['requests'][0],
            ]
        ];
    }
    
    private function getInviteData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
            ]
        ];
    }
    
    private function getJoinWithTokenData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'token' => '',
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'token' => '',
            ]
        ];
    }
    
    private function getDeleteTokenData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'token' => $dbData['tokens'][0],
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'token' => $dbData['tokens'][0],
            ]
        ];
    }
    
    private function getCreateLiveLessonData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'data' => [
                    'title' => '',
                    'start' => new \DateTime('+5 days'),
                    'url' => '',
                ]
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'group' => $dbData['groups'][0],
                'data' => [
                    'title' => '',
                    'start' => new \DateTime('+5 days'),
                    'url' => '',
                ]
            ]
        ];
    }
    
    private function getUpdateLiveLessonData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'liveLesson' => $dbData['liveLessons'][0],
                'original' => clone $dbData['liveLessons'][0],
                'data' => [
                    'title' => '',
                    'start' => new \DateTime('+5 days'),
                    'url' => '',
                ]
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'liveLesson' => $dbData['liveLessons'][0],
                'original' => clone $dbData['liveLessons'][0],
                'data' => [
                    'title' => '',
                    'start' => new \DateTime('+5 days'),
                    'url' => '',
                ]
            ]
        ];
    }
    
    private function getAddLiveLessonUrlData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'liveLesson' => $dbData['liveLessons'][0],
                'original' => clone $dbData['liveLessons'][0],
                'url' => ''
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'liveLesson' => $dbData['liveLessons'][0],
                'original' => clone $dbData['liveLessons'][0],
                'url' => ''
            ]
        ];
    }
    
    private function getDeleteLiveLessonData() : array {
        $dbData = $this->getStaticData();
        return [
            'correct' => [
                'user' => $dbData['teachers'][0],
                'liveLesson' => $dbData['liveLessons'][0],
                'id' => clone $dbData['liveLessons'][0]->getId(),
            ],
            'bad' => [
                'user' => $dbData['teachers'][0],
                'liveLesson' => $dbData['liveLessons'][0],
                'id' => clone $dbData['liveLessons'][0]->getId(),
            ]
        ];
    }

    public function testCreate() {
        $this->init();
        $data = $this->getCreateData();
        foreach($data['correct'] as $row) {
            $result = $this->service->create($row['user'], $row['data']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof StudentGroup);
            $groupDb = $this->em->getRepository(StudentGroup::class)->findOneBy(['id' => $result->getData()->getId()]);
            $this->assertTrue($groupDb instanceof StudentGroup);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->create($row['user'], $row['data']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof StudentGroupException);
            $this->assertNull($result->getData());
        }
    }

    public function testUpdate() {
        $this->init();
        $data = $this->getUpdateData();
        foreach($data['correct'] as $row) {
            $result = $this->service->create($row['user'], $row['data']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof StudentGroup);
        
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->create($row['user'], $row['data']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof StudentGroupException);
            $this->assertNull($result->getData());
        }
    }

    public function testStudentRemove() {
        $this->init();
        $data = $this->getRemoveStudent();
        foreach($data['correct'] as $row) {
            $result = $this->service->removeStudent($row['user'], $row['group'], $row['student']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof StudentGroup);
        
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->removeStudent($row['user'], $row['group'], $row['student']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof StudentGroupException);
            $this->assertNull($result->getData());
        }
    }

    public function testGroupLeave() {
        $this->init();
        $data = $this->getGroupLeaveData();
        foreach($data['correct'] as $row) {
            $result = $this->service->leaveGroup($row['user'], $row['group']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof StudentGroup);
        
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->leaveGroup($row['user'], $row['group']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof StudentGroupException);
            $this->assertNull($result->getData());
        }
    }

    public function testResourceAdd() {
        $this->init();
        $data = $this->getResourceAddData();
        foreach($data['correct'] as $row) {
            $result = $this->service->addResource($row['user'], $row['group'], $row['data']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof StudentGroup);
        
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->addResource($row['user'], $row['group'], $row['data']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof StudentGroupException);
            $this->assertNull($result->getData());
        }
    }

    
    public function testResourceRemove() {
        $this->init();
        $data = $this->getResourceRemoveData();
        foreach($data['correct'] as $row) {
            $result = $this->service->addResource($row['user'], $row['resource']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof StudentGroup);
        
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->addResource($row['user'], $row['resource']);
            $this->assertTrue($result instanceof StudentGroupResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof StudentGroupException);
            $this->assertNull($result->getData());
        }
    }

    public function testGroupJoin() {
        $this->init();
        $data = $this->getJoinData();
        foreach($data['correct'] as $row) {
            $result = $this->service->addResource($row['user'], $row['group']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof GroupJoinRequest);
        
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->addResource($row['user'], $row['group']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof GroupJoinRequest);
            $this->assertNull($result->getData());
        }
    }    
    
    public function testAcceptRequest() {
        $this->init();
        $data = $this->getRequestAcceptData();
        foreach($data['correct'] as $row) {
            $result = $this->service->acceptRequest($row['user'], $row['request']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof GroupJoinRequest);
            $this->assertTrue($result->getData()->getAccepted());
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->acceptRequest($row['user'], $row['request']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof GroupJoinRequest);
            $this->assertNull($result->getData());
        }
    }

    public function testDeclineRequest() {
        $this->init();
        $data = $this->getRequestDeclineData();
        foreach($data['correct'] as $row) {
            $result = $this->service->declineRequest($row['user'], $row['request']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof GroupJoinRequest);
            $this->assertFalse($result->getData()->getAccepted());
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->declineRequest($row['user'], $row['request']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof GroupJoinRequest);
            $this->assertNull($result->getData());
        }
    }

    public function testCancelRequest() {
        $this->init();
        $data = $this->getRequestCancelData();
        foreach($data['correct'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['request']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof GroupJoinRequest);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['request']);
            $this->assertTrue($result instanceof GroupRequestResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof GroupJoinRequest);
            $this->assertNull($result->getData());
        }
    }

    public function testInvite() {
        $this->init();
        $data = $this->getInviteData();
        foreach($data['correct'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['group']);
            $this->assertTrue($result instanceof GroupInviteTokenResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof GroupInviteToken);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['group']);
            $this->assertTrue($result instanceof GroupInviteTokenResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof GroupInviteToken);
            $this->assertNull($result->getData());
        }
    }

    public function testJoinWithToken() {
        $this->init();
        $data = $this->getJoinWithTokenData();
        foreach($data['correct'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['token']);
            $this->assertTrue($result instanceof GroupInviteTokenResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof GroupInviteToken);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['token']);
            $this->assertTrue($result instanceof GroupInviteTokenResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof GroupInviteToken);
            $this->assertNull($result->getData());
        }
    }

    
    public function testDeleteToken() {
        $this->init();
        $data = $this->getDeleteTokenData();
        foreach($data['correct'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['token']);
            $this->assertTrue($result instanceof GroupInviteTokenResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof GroupInviteToken);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->cancelRequest($row['user'], $row['token']);
            $this->assertTrue($result instanceof GroupInviteTokenResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof GroupInviteToken);
            $this->assertNull($result->getData());
        }
    }

    public function testCreateLiveLesson() {
        $this->init();
        $data = $this->getCreateLiveLessonData();
        foreach($data['correct'] as $row) {
            $result = $this->service->createLiveLesson($row['user'], $row['group'], $row['data']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof LiveLesson);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->createLiveLesson($row['user'], $row['group'], $row['data']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof LiveLesson);
            $this->assertNull($result->getData());
        }
    }

    
    public function testUpdateLiveLesson() {
        $this->init();
        $data = $this->getUpdateLiveLessonData();
        foreach($data['correct'] as $row) {
            $result = $this->service->updateLiveLesson($row['user'], $row['liveLesson'], $row['data']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof LiveLesson);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->updateLiveLesson($row['user'], $row['liveLesson'], $row['data']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof LiveLesson);
            $this->assertNull($result->getData());
        }
    }

    public function testAddLessonUrl() {
        $this->init();
        $data = $this->getAddLiveLessonUrlData();
        foreach($data['correct'] as $row) {
            $result = $this->service->addLiveLessonMeetupUrl($row['user'], $row['liveLesson'], $row['url']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof LiveLesson);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->addLiveLessonMeetupUrl($row['user'], $row['liveLesson'], $row['url']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof LiveLesson);
            $this->assertNull($result->getData());
        }
    }

    public function testDeleteLiveLesson() {
        $this->init();
        $data = $this->getDeleteLiveLessonData();
        foreach($data['correct'] as $row) {
            $result = $this->service->addLiveLessonMeetupUrl($row['user'], $row['liveLesson']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof LiveLesson);
        }

        foreach($data['bad'] as $row) {
            $result = $this->service->addLiveLessonMeetupUrl($row['user'], $row['liveLesson']);
            $this->assertTrue($result instanceof LiveLessonResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof LiveLesson);
            $this->assertNull($result->getData());
        }
    }
}
