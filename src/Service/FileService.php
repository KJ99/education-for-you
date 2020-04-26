<?php
namespace App\Service;

use App\Service\EntityService;
use App\Entity\File;
use App\Result\FileResult;
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

class FileService extends EntityService {
    private $uploadsDir;
    private $maxSize;
    private $availableMimeTypes;

    private $finder;
    private $fs;
    
    public function __construct(EntityManagerInterface $em, ParameterBagInterface $params) {
        $this->uploadsDir = $params->get('kernel.project_dir') . '/uploads/';
        $this->maxSize = $params->get('maxFileSizeMb') * 1024 * 1024;
        $this->finder = new Finder();
        $this->fs = new Filesystem();
        $this->availableMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
            'image/bitmap',
            'video/mp4',
            'video/ogg',
            'video/webm',
            'video/x-ms-wmv',
            'video/x-msvideo',
            'video/3gpp',
            'video/x-flv',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.oasis.opendocument.presentation',
            'application/vnd.oasis.opendocument.spreadsheet',
            'application/vnd.oasis.opendocument.text',
            'application/pdf',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'text/plain',
            'application/encrypted'
            
        ];
        parent::__construct($em);
    }

    public function resolveFile(string $path, string $originalFileName, bool $autosave = false) : FileResult {
        $result = new FileResult();
        try {
            $file = $this->processFile($path, $originalFileName, $autosave);
            $result->setSuccess(true)->setData($file);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    private function processFile(string $path, string $originalFileName, bool $autosave = false) : File {
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
        // dump($mime);
        if(!in_array($mime, $this->availableMimeTypes)) {
            throw new FileException('mime.forbidden');
        }
        
        $serverFileName = $this->saveFile($info);
        if($serverFileName == null) {
            throw new FileException('file.save.failed');
        }
        
        $file = $this->buildEntity($serverFileName, $originalFileName, $mime);
        
        if($autosave) {
            $saveResult = $this->saveEntity($file);
            if(!$saveResult) {
                throw new DatabaseException('save.failed');
            }
        }

        return $file;
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
        $directory = $this->uploadsDir;
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

    private function buildEntity(string $fileName, string $originalFileName, string $mime) : File {
        $file = new File();
        $file 
            ->setMime($mime)
            ->setDirectory($this->uploadsDir)
            ->setFileName($fileName)
            ->setOriginalFileName($originalFileName);
        return $file;
    }

    private function saveEntity(File $file) : bool {
        $result;
        try {
            $this->em->persist($file);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

    public function deleteEntity(File $file) : bool {
        $result;
        try {
            $this->em->remove($file);
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

    public function delete(File $file) : bool {
        $path = $file->getDirectory() . $file->getFileName();
        return $this->deleteEntity($file) && $this->deleteFile($path);
    }
 
}