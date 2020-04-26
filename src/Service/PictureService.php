<?php
namespace App\Service;

use App\Service\EntityService;
use App\Entity\Picture;
use App\Result\PictureResult;
use App\Exception\EduException;
use App\Exception\FileException;
use App\Exception\DatabaseException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Mime\MimeTypes;

class PictureService extends EntityService {
    private $projectPublicDir;
    private $uploadsDir;
    private $maxSize;
    private $availableMimeTypes;

    private $finder;
    private $fs;
    
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params) {
        $this->projectPublicDir = $params->get('kernel.project_dir') . '/public/';
        $this->uploadsDir = 'assets/images/uploads/';
        $this->maxSize = $params->get('maxPictureSizeMb') * 1024 * 1024;
        $this->finder = new Finder();
        $this->fs = new Filesystem();
        $this->availableMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        parent::__construct($em);
    }

    public function resolvePicture(string $path, bool $autosave = false) : PictureResult {
        $result = new PictureResult();
        try {
            $picture = $this->processFile($path, $autosave);
            $result->setSuccess(true)->setData($picture);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    private function processFile(string $path, bool $autosave = false) : Picture {
        $fileNameStartIndex = strrpos($path, '/') + 1;
        $directory = substr($path, 0, $fileNameStartIndex);
        $fileName = substr($path, $fileNameStartIndex);
        $files = iterator_to_array($this->finder->files()->in($directory)->name($fileName));
        if(count($files) == 0 || !isset( $files[$path])) {
            throw new FileException('source.file.not.found');
        }
        $info = $files[$path];

        if($info->getSize() > $this->maxSize) {
            throw new FileException('file.too_large');
        }

        $mime = $this->findMimeType($info);
        if(!in_array($mime, $this->availableMimeTypes)) {
            throw new FileException('mime.forbidden');
        }
        
        $serverFileName = $this->saveFile($info);
        if($serverFileName == null) {
            throw new FileException('file.save.failed');
        }
        
        $picture = $this->buildEntity($serverFileName, $fileName, $mime);
        
        if($autosave) {
            $saveResult = $this->saveEntity($picture);
            if(!$saveResult) {
                throw new DatabaseException('save.failed');
            }
        }

        return $picture;
    }

    private function findMimeType(SplFileInfo $info) : ?string {
        $result;
        $mimeTypes = new MimeTypes();
        try {
            $result = $mimeTypes->guessMimeType($info->getPathName());
        } catch(\Exception $e) {
            $result = null;
        }
        return $result;
    }

    private function saveFile(SplFileInfo $info) : ?string {
        $result;
        $directory = $this->projectPublicDir . $this->uploadsDir;
        $fileName = md5(uniqid() . time() . random_bytes(16)) . '.' . $info->getExtension();
        $dest = $directory . $fileName;
        try {
            $this->fs->copy($info->getPathName(), $dest);
            $result = $fileName;
        } catch(IOExceptionInterface $e) {
            $result = null;
        }
        return $result;
    }

    private function buildEntity(string $fileName, string $originalFileName, string $mime) : Picture {
        $picture = new Picture();
        $picture 
            ->setMime($mime)
            ->setDirectory($this->projectPublicDir . $this->uploadsDir)
            ->setPublicDirectory($this->uploadsDir)
            ->setFileName($fileName)
            ->setOriginalFileName($originalFileName);
        return $picture;
    }

    private function saveEntity(Picture $picture) : bool {
        $result;
        try {
            $this->em->persist($picture);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function deleteEntity(Picture $picture) : bool {
        $result;
        try {
            $this->em->remove($picture);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function deleteFile(string $path) : bool {
        $result;
        try {
            $this->fs->remove($path);
            $result = true;
        } catch(\IOExceptionInterface $e) {
            $result = false;
        }
        return $result;
    }

    public function deletePicture(Picture $picture) : bool {
        $path = $picture->getDirectory() . $picture->getFileName();
        return $this->deleteEntity($picture) && $this->deleteFile($path);
    }
 
}