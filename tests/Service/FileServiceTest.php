<?php
namespace App\Test\Service;

use App\Service\FileService;
use App\Result\FileResult;
use App\Entity\File;
use App\Exception\EduException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class FileServiceTest extends KernelTestCase
{
    private $service;
    private $em;

    private function init() {
        self::bootKernel();
        $container = self::$container;
        $this->service = $container->get('App\Service\FileService');
        $this->em = $container->get('doctrine')->getManager();
    }

    private function getFilePaths() : array {
        return [
            '/tmp/file-test/Logos_ByTailorBrands.zip',
            '/tmp/file-test/PacketTracer-7.3.0-win64-setup.exe',
            '/tmp/file-test/3rd-Ed.-Market-Leader-Intermediate-11wih01.pdf',

            '/tmp/file-test/13 (4).doc',
            '/tmp/file-test/13 (5).doc',
            '/tmp/file-test/13 (6).doc',
            '/tmp/file-test/13 (7).doc',
            '/tmp/file-test/13 (8).doc',
            '/tmp/file-test/13 (9).doc',
            '/tmp/file-test/2 eko AB 2019 Miary dopasowania i modele nieliniowe h (1).docx',
            '/tmp/file-test/2 eko AB 2019 Miary dopasowania i modele nieliniowe h (2).docx',
            '/tmp/file-test/Cw_2_JG.docx',
            '/tmp/file-test/CV Konrad Jezierski.pdf',
            '/tmp/file-test/wiki2.jpg',
            '/tmp/file-test/24kurczaki.jpeg',
            '/tmp/file-test/japycz (1).jpeg',
            '/tmp/file-test/japycz (3).jpeg',
            '/tmp/file-test/po.jpeg',
            '/tmp/file-test/b2.jpg',
            '/tmp/file-test/bounc.jpg',
            '/tmp/file-test/guns.jpg',
            '/tmp/file-test/japycz (2).jpg',
            '/tmp/file-test/Pad PS5 - 1.jpg',
            '/tmp/file-test/zolw_paker.jpg',
            '/tmp/file-test/petparty.png',
            '/tmp/file-test/groups.PNG',
            '/tmp/file-test/groups_stu_req.PNG',
            '/tmp/file-test/jostz.PNG',
            '/tmp/file-test/lekcja.PNG',
            '/tmp/file-test/lesson2.PNG',
            '/tmp/file-test/przysz.PNG',
            '/tmp/file-test/register.PNG',
            '/tmp/file-test/sss.PNG',
            '/tmp/file-test/subject_learn.PNG',
            '/tmp/file-test/windows.PNG',
            '/tmp/file-test/xdd.PNG',
        ];
    }
    

    public function testFileResolveAndDelete() {
        $badCount = 3;

        $this->init();
        $paths = $this->getFilePaths();
        $saveResults = [];
        $files = [];
        foreach($paths as $path) {
            $saveResults[] = $this->service->resolveFile($path, 'hulatiwi');
        }
    
        for($i = 0; $i < $badCount; $i++) {
            //fails
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof FileResult);
            $this->assertNull($res->getData());
            $this->assertTrue($res->getError() instanceof EduException);
            $this->assertFalse($res->getSuccess());
        }

        for($i = $badCount; $i < count($paths); $i++) {
            //success
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof FileResult);
            $this->assertNull($res->getError());
            $this->assertTrue($res->getData() instanceof File);
            $this->assertTrue($res->getSuccess());
            $files[] = $res->getData();
        }

        foreach($files as $file) {
            $path = $file->getDirectory() . $file->getFileName();
            $deleteResult = $this->service->deleteFile($path);
            $this->assertTrue($deleteResult);
        }
    }

    public function testResolveSaveAndDelete() {
        $badCount = 3;

        $this->init();
        $paths = $this->getFilePaths();
        $saveResults = [];
        $files = [];
        foreach($paths as $path) {
            $saveResults[] = $this->service->resolveFile($path, 'original', true);
        }
    
        for($i = 0; $i < $badCount; $i++) {
            //fails
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof FileResult);
            $this->assertNull($res->getData());
            $this->assertTrue($res->getError() instanceof EduException);
            $this->assertFalse($res->getSuccess());
        }

        for($i = $badCount; $i < count($paths); $i++) {
            //success
            $res = $saveResults[$i];
            $this->assertTrue($res instanceof FileResult);
            $this->assertNull($res->getError());
            $this->assertTrue($res->getData() instanceof File);
            $this->assertTrue($res->getSuccess());
            $file = $res->getData();
            $fileFromDB = $this->em->getRepository(File::class)->findOneBy(['fileName' => $file->getFileName()]);
            $this->assertTrue($fileFromDB instanceof File);
            $this->assertSame($file->getId(), $fileFromDB->getId());
            $this->assertSame($file->getFileName(), $fileFromDB->getFileName());
            $files[] = $file;
        }

        foreach($files as $file) {
            $fileName = $file->getFileName();
            $deleteResult = $this->service->delete($file);
            $fileFromDB = $this->em->getRepository(File::class)->findOneBy(['fileName' => $fileName]);
            $this->assertTrue($deleteResult);
            $this->assertNull($fileFromDB);
        }
    }

}
