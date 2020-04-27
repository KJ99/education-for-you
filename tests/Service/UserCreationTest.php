<?php
namespace App\Test\Service;

use App\Service\UserService;
use App\Result\UserResult;
use App\Entity\User;
use App\Exception\EduException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class UserCreationTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        $this->service = $container->get('App\Service\UserService');
        $this->em = $container->get('doctrine')->getManager();
    }

    private function getUsersData() : array {
        return [
            ['email' => 'MGlA4Qd6@edu.pl',        'password' => 'PLLBxt',        'confirmPassword' => 'PLLBxt',        'firstName' => 'OfjNLJxb',        'nickname' => 'OfjNLJxb'],
            ['email' => 'I5LM4cGw@edu.pl',        'password' => 'mXKznB',        'confirmPassword' => 'mXKznB',        'firstName' => 'lUNkAHke',        'nickname' => 'lUNkAHke'],
            ['email' => 'zRqPImJS@edu.pl',        'password' => 'QhbKIM',        'confirmPassword' => 'QhbKIM',        'firstName' => 'UgIWAvIq',        'nickname' => 'UgIWAvIq'],
            ['email' => 'boeLZOtC@edu.pl',        'password' => 'XCCPfp',        'confirmPassword' => 'XCCPfp',        'firstName' => 'iktqQKhg',        'nickname' => 'iktqQKhg'],
            ['email' => 'pefuoe7v@edu.pl',        'password' => 'MaeLvv',        'confirmPassword' => 'MaeLvv',        'firstName' => 'ZcTrpVgT',        'nickname' => 'ZcTrpVgT'],
            ['email' => 'x6R1cZnE@edu.pl',        'password' => 'dOdNRi',        'confirmPassword' => 'dOdNRi',        'firstName' => null,        'nickname' => 'fJZaCYrz'],
            ['email' => 'tg9Ssk7I@edu.pl',        'password' => 'fYlYpr',        'confirmPassword' => 'fYlYpr',        'firstName' => null,        'nickname' => 'iIQNYaui'],
            ['email' => 'gz9CmSHv@edu.pl',        'password' => 'OgpNuJ',        'confirmPassword' => 'OgpNuJ',        'firstName' => null,        'nickname' => 'wLjduXUo'],
            ['email' => 'm0nVehVm@edu.pl',        'password' => 'OAYzGq',        'confirmPassword' => 'OAYzGq',        'firstName' => null,        'nickname' => 'JetlvJzo'],
            ['email' => 'vUwCdzbN@edu.pl',        'password' => 'PhbEqm',        'confirmPassword' => 'PhbEqm',        'firstName' => null,        'nickname' => 'ZZpnczOt'],
            ['email' => 'Q6ts81vR@edu.pl',        'password' => 'IxqlIO',        'confirmPassword' => 'IxqlIO',        'firstName' => 'ZJizYxfT',        'nickname' => null],
            ['email' => 'G9ty5akR@edu.pl',        'password' => 'BTynIl',        'confirmPassword' => 'BTynIl',        'firstName' => 'nOQSyavQ',        'nickname' => null],
            ['email' => 'nN4uTawe@edu.pl',        'password' => 'UXuQkA',        'confirmPassword' => 'UXuQkA',        'firstName' => 'hqEICOaG',        'nickname' => null],
            ['email' => 'IjrFsZuL@edu.pl',        'password' => 'fKhkBn',        'confirmPassword' => 'fKhkBn',        'firstName' => 'cDwtClsc',        'nickname' => null],
            ['email' => 'ZVcdhPgB@edu.pl',        'password' => 'hoLAgL',        'confirmPassword' => 'hoLAgL',        'firstName' => 'YojibEad',        'nickname' => null],

            ['email' => 'Hpgy9XiP@edu.pl',        'password' => null,        'confirmPassword' => 'hoLAgL',        'firstName' => 'vDngXZOI',        'nickname' => null],
            ['email' => 'afouthmb@edu.pl',        'password' => '1234',        'confirmPassword' => 'wsgLzA',        'firstName' => 'sBoeSDzj',        'nickname' => null],
            ['email' => 'WpFqRJTq@edu.pl',        'password' => '1234',        'confirmPassword' => 'NpDYnj',        'firstName' => 'grfqBCGS',        'nickname' => null],
            ['email' => 'DjGTXxI6@edu.pl',        'password' => 'RghEJH',        'confirmPassword' => null,        'firstName' => 'bpUichFJ',        'nickname' => null],
            ['email' => 'YnMqunGE@edu.pl',        'password' => 'JmuWVa',        'confirmPassword' => 'hubabuba',        'firstName' => 'HFMgVpZd',        'nickname' => null],
            ['email' => 'AIrT5xIL@edu.pl',        'password' => null,        'confirmPassword' => null,        'firstName' => 'EuBwvTLr',        'nickname' => null],
            ['email' => 'lPQtLjS3@edu.pl',        'password' => 'idRyDC',        'confirmPassword' => 'idRyDC',        'firstName' => '   ',        'nickname' => null],
            ['email' => 'nwYr5W8A@edu.pl',        'password' => 'RsxcRo',        'confirmPassword' => 'RsxcRo',        'firstName' => '   ',        'nickname' => null],
            ['email' => 'p7UcTYzL@edu.pl',        'password' => 'PeHjpw',        'confirmPassword' => 'PeHjpw',        'firstName' => '   ',        'nickname' => null],
            ['email' => 'pWeQ0VIU@edu.pl',        'password' => 'Jfejuv',        'confirmPassword' => 'Jfejuv',        'firstName' => 'naXwSOnr',        'nickname' => '   '],
            ['email' => 'G6asgl0E@edu.pl',        'password' => 'ybjvlH',        'confirmPassword' => 'ybjvlH',        'firstName' => 'ioRPrGpG',        'nickname' => '   '],
            ['email' => 'gKOYkBYB@edu.pl',        'password' => 'nYRrAE',        'confirmPassword' => 'nYRrAE',        'firstName' => 'qwgzbVDd',        'nickname' => '   '],
            ['email' => 'bfnoZVpK@edu.pl',        'password' => 'lUAMiN',        'confirmPassword' => 'lUAMiN',        'firstName' => null,        'nickname' => null],
            ['email' => '4u98Cal7@edu.pl',        'password' => 'DVTGwy',        'confirmPassword' => 'DVTGwy',        'firstName' => null,        'nickname' => null],
            ['email' => 'xgsr9FWz@edu.pl',        'password' => 'yWEbSE',        'confirmPassword' => 'yWEbSE',        'firstName' => null,        'nickname' => null],
            ['email' => 'edx',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '@edx',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '@rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '@edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'lYTYSoIH@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'EJdsUuf8@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'RJVdOre8@edx',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'cSa9wM3O@edx',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'XzHX7dyT@rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'obNqinGu@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'i6sNFJ7p@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'xcExUukQ@rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'BR59i6is@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '3gclgKRu@rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'zIM1L5He@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'HMS1OlIY@edx',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '8PfkoODJ@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'grp9y6xS@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '5wJjWlYH@rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'JuYLfB1Q@rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'tHhgWYuR@.qw',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'Id4ckayY@edx',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'eM2DYz50@edx',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'Fo78yFvf@rq.',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'Ptz8WV3Ledu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'O1eOSLZ5edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '9MwkSLT0edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'FBZJZVnmedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'p1z44A5Vedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'n7zsjTIsedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '08yczdydedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'ZA9DiVRIedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'q9c139Tredu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'HdLrjPUQedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'FPmJ938Tedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'SQEzElqgedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'E0JFCd6Medu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'xcdCCmUiedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'rpF6wq85edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'c3tKORrVedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'VkgqzGG6edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'L6nxYZO8edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'Gl1BPrkzedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'rKMXYeDKedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => '5Wboo2Lxedu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'HvalNuk7edu.pl',        'password' => 'qwerty123456',        'confirmPassword' => 'qwerty123456',        'firstName' => 'Konrad',        'nickname' => null],
            ['email' => 'MGlA4Qd6@edu.pl',        'password' => 'PLLBxt',        'confirmPassword' => 'PLLBxt',        'firstName' => 'OfjNLJxb',        'nickname' => 'testuser1'],
            ['email' => 'I5LM4cGw@edu.pl',        'password' => 'mXKznB',        'confirmPassword' => 'mXKznB',        'firstName' => 'lUNkAHke',        'nickname' => 'testuser2'],
            ['email' => 'zRqPImJS@edu.pl',        'password' => 'QhbKIM',        'confirmPassword' => 'QhbKIM',        'firstName' => 'UgIWAvIq',        'nickname' => 'testuser3'],
            ['email' => 'boeLZOtC@edu.pl',        'password' => 'XCCPfp',        'confirmPassword' => 'XCCPfp',        'firstName' => 'iktqQKhg',        'nickname' => 'testuser4'],
            ['email' => 'pefuoe7v@edu.pl',        'password' => 'MaeLvv',        'confirmPassword' => 'MaeLvv',        'firstName' => 'ZcTrpVgT',        'nickname' => 'testuser5'],
            ['email' => 'emailero6@edu.pl',        'password' => 'dOdNRi',        'confirmPassword' => 'dOdNRi',        'firstName' => 'fJZaCYrz',        'nickname' => 'fJZaCYrz'],
            ['email' => 'emailero7@edu.pl',        'password' => 'fYlYpr',        'confirmPassword' => 'fYlYpr',        'firstName' => 'iIQNYaui',        'nickname' => 'iIQNYaui'],
            ['email' => 'emailero8@edu.pl',        'password' => 'OgpNuJ',        'confirmPassword' => 'OgpNuJ',        'firstName' => 'wLjduXUo',        'nickname' => 'wLjduXUo'],
            ['email' => 'emailero9@edu.pl',        'password' => 'OAYzGq',        'confirmPassword' => 'OAYzGq',        'firstName' => 'JetlvJzo',        'nickname' => 'JetlvJzo'],
            ['email' => 'emailero10@edu.pl',        'password' => 'PhbEqm',        'confirmPassword' => 'PhbEqm',        'firstName' => 'ZZpnczOt',        'nickname' => 'ZZpnczOt'],
        ];
    }
    

    public function testCreate() {
        $successCount = 15;
        $data = $this->getUsersData();
        $this->init();
        $results = [];
        foreach ($data as $userData) {
            $results[] = $this->service->createUser($userData, ['ROLE_STUDENT'], "/workspace/education-for-you/public/assets/images/default-user-avatar.png");
        }

        for($i = 0; $i < $successCount; $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UserResult);
            $this->assertTrue($result->getSuccess());
            $this->assertNull($result->getError());
            $this->assertTrue($result->getData() instanceof User);
            $userFromDb = $this->em->getRepository(User::class)->findOneBy(['email' => $result->getData()->getEmail()]);
            $this->assertTrue($userFromDb instanceof User);
            $this->assertSame($userFromDb->getId(), $result->getData()->getId());
        }   

        for($i = $successCount; $i < count($results); $i++) {
            $result = $results[$i];
            $this->assertTrue($result instanceof UserResult);
            $this->assertFalse($result->getSuccess());
            $this->assertTrue($result->getError() instanceof EduException);
            $this->assertNull($result->getData());
        }
    }

}
