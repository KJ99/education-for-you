<?php
namespace App\Test\Service;

use App\Service\PictureService;
use App\Result\PictureResult;
use App\Entity\Picture;
use App\Exception\EduException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class PictureServiceTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        $this->service = $container->get('App\Service\PictureService');
        $this->em = $container->get('doctrine')->getManager();
    }

    private function getFilePaths() : array {
        return [
            '/tmp/picture-test/13 (4).doc',
            '/tmp/picture-test/13 (5).doc',
            '/tmp/picture-test/13 (6).doc',
            '/tmp/picture-test/13 (7).doc',
            '/tmp/picture-test/13 (8).doc',
            '/tmp/picture-test/13 (9).doc',
            '/tmp/picture-test/2 eko AB 2019 Miary dopasowania i modele nieliniowe h (1).docx',
            '/tmp/picture-test/2 eko AB 2019 Miary dopasowania i modele nieliniowe h (2).docx',
            '/tmp/picture-test/Cw_2_JG.docx',
            '/tmp/picture-test/PacketTracer-7.3.0-win64-setup.exe',
            '/tmp/picture-test/Logos_ByTailorBrands.zip',
            '/tmp/picture-test/3rd-Ed.-Market-Leader-Intermediate-11wih01.pdf',
            '/tmp/picture-test/CV Konrad Jezierski.pdf',
            '/tmp/picture-test/wiki2.jpg',

            '/tmp/picture-test/24kurczaki.jpeg',
            '/tmp/picture-test/japycz (1).jpeg',
            '/tmp/picture-test/japycz (3).jpeg',
            '/tmp/picture-test/po.jpeg',
            '/tmp/picture-test/b2.jpg',
            '/tmp/picture-test/bounc.jpg',
            '/tmp/picture-test/guns.jpg',
            '/tmp/picture-test/japycz (2).jpg',
            '/tmp/picture-test/Pad PS5 - 1.jpg',
            '/tmp/picture-test/zolw_paker.jpg',
            '/tmp/picture-test/petparty.png',
            '/tmp/picture-test/groups.PNG',
            '/tmp/picture-test/groups_stu_req.PNG',
            '/tmp/picture-test/jostz.PNG',
            '/tmp/picture-test/lekcja.PNG',
            '/tmp/picture-test/lesson2.PNG',
            '/tmp/picture-test/przysz.PNG',
            '/tmp/picture-test/register.PNG',
            '/tmp/picture-test/sss.PNG',
            '/tmp/picture-test/subject_learn.PNG',
            '/tmp/picture-test/windows.PNG',
            '/tmp/picture-test/xdd.PNG',
        ];
    }
    

    public function testFileResolveAndDelete() {
        $badCount = 14;

        $this->init();
        $paths = $this->getFilePaths();
        $saveResults = [];
        $pictures = [];
        foreach($paths as $path) {
            $saveResults[] = $this->service->resolvePicture($path);
        }
    
        for($i = 0; $i < $badCount; $i++) {
            //fails
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof PictureResult);
            $this->assertNull($res->getData());
            $this->assertTrue($res->getError() instanceof EduException);
            $this->assertFalse($res->getSuccess());
        }

        for($i = $badCount; $i < count($paths); $i++) {
            //success
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof PictureResult);
            $this->assertNull($res->getError());
            $this->assertTrue($res->getData() instanceof Picture);
            $this->assertTrue($res->getSuccess());
            $pictures[] = $res->getData();
        }

        foreach($pictures as $picture) {
            $path = $picture->getDirectory() . $picture->getFileName();
            $deleteResult = $this->service->deleteFile($path);
            $this->assertTrue($deleteResult);
        }
    }

    public function testResolveSaveAndDelete() {
        $badCount = 14;

        $this->init();
        $paths = $this->getFilePaths();
        $saveResults = [];
        $pictures = [];
        foreach($paths as $path) {
            $saveResults[] = $this->service->resolvePicture($path, true);
        }
    
        for($i = 0; $i < $badCount; $i++) {
            //fails
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof PictureResult);
            $this->assertNull($res->getData());
            $this->assertTrue($res->getError() instanceof EduException);
            $this->assertFalse($res->getSuccess());
        }

        for($i = $badCount; $i < count($paths); $i++) {
            //success
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof PictureResult);
            $this->assertNull($res->getError());
            $this->assertTrue($res->getData() instanceof Picture);
            $this->assertTrue($res->getSuccess());
            $picture = $res->getData();
            $pictureFromDB = $this->em->getRepository(Picture::class)->findOneBy(['fileName' => $picture->getFileName()]);
            $this->assertTrue($pictureFromDB instanceof Picture);
            $this->assertSame($picture->getId(), $pictureFromDB->getId());
            $this->assertSame($picture->getFileName(), $pictureFromDB->getFileName());
            $pictures[] = $picture;
        }

        foreach($pictures as $picture) {
            $fileName = $picture->getFileName();
            $deleteResult = $this->service->deletePicture($picture);
            $pictureFromDB = $this->em->getRepository(Picture::class)->findOneBy(['fileName' => $fileName]);
            $this->assertTrue($deleteResult);
            $this->assertNull($pictureFromDB);
        }
    }

}
