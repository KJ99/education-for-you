<?php
namespace App\Processor;

use App\Entity\Unit;
use App\Entity\Lesson;
use App\Entity\User;
use App\Entity\Picture;


use App\Entity\Contributor;
use App\Entity\PrivacyPolicy;
use App\Entity\SiteDescription;
use App\Entity\TermsOfUse;

use App\Service\PictureService;

use App\Result\LessonResult;
use App\Exception\AdminSiteSettingsException;
use App\Exception\DatabaseException;
use App\Exception\EduException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

class SiteSettingsProcessor extends Processor {
    
    private $pictureService;

    public function __construct(EntityManagerInterface $em, PictureService $pictureService) {
        $this->pictureService = $pictureService;
        parent::__construct($em);
    }
    
    public function processContributorAddition(User $user, array $data) : Contributor {
        $validationError = $this->getContributorDataError($user, $data);
        if($validationError != null) {
            throw new AdminSiteSettingsException($validationError);
        }
        $avatarResult = $this->pictureService->resolvePicture($data['avatar']->getPathName());
        if(!$avatarResult->getSuccess()) {
            throw $avatarResult->getError();
        }
        $avatar = $avatarResult->getData();
        
        $contributor = new Contributor();
        $contributor
            ->setName($data['name'])
            ->setRole($data['role'])
            ->setAvatar($avatar);
        $this->em->persist($avatar);
        $this->em->persist($contributor);

        $saved = $this->flushDatabase();
        if(!$saved) {
            $this->pictureService->deleteFile($avatar->getDirectory() . $avatar->getFileName);
            throw new DatabaseException('save.failed');
        }
        
        return $contributor;
    }

    public function processContributorRemoval(User $user, Contributor $contributor) : Contributor {
        $accessError = $this->getAccessError($user);
        if($accessError != null) {
            throw new AdminSiteSettingsException($accessError);
        }
        $avatarFilePath = $contributor->getAvatar()->getDirectory() . $contributor->getAvatar()->getFileName();

        $this->em->remove($contributor->getAvatar());
        $this->em->remove($contributor);
        
        $saved = $this->flushDatabase();
        if($saved) {
            $this->pictureService->deleteFile($avatarFilePath);
        } else {
            throw new DatabaseException('save.failed');
        }
        return $contributor;
    }

    public function processSiteDescriptionChange(User $user, string $data) : SiteDescription {
        $accessError = $this->getAccessError($user);
        if($accessError != null) {
            throw new AdminSiteSettingsException($accessError);
        }
        
        $description = (new SiteDescription())
            ->setContent($data)
            ->setPublishDate(new \DateTime())
            ->setAuthor($user);

        $this->em->persist($description);

        $saved = $this->flushDatabase();
        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $description;

    } 

    public function processPrivacyPolicyChange(User $user, string $data) : PrivacyPolicy {
        $accessError = $this->getAccessError($user);
        if($accessError != null) {
            throw new AdminSiteSettingsException($accessError);
        }

                
        $policy = (new PrivacyPolicy())
            ->setText($data)
            ->setPublishDate(new \DateTime())
            ->setAuthor($user);

        $this->em->persist($policy);
        
        $saved = $this->flushDatabase();
        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $policy;
    }

    public function processTermsOfUseChange(User $user, string $data) : TermsOfUse {
        $accessError = $this->getAccessError($user);
        if($accessError != null) {
            throw new AdminSiteSettingsException($accessError);
        }

        $terms = (new TermsOfUse())
            ->setText($data)
            ->setPublishDate(new \DateTime())
            ->setAuthor($user);

        $this->em->persist($terms);
                
        $saved = $this->flushDatabase();
        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $terms;
    }
 
    private function getAccessError(User $user) : ?string {
        $error = null;
        if(!$user->isAdmin()) {
            $error = 'access.denied';
        }
        return $error;
    }

    private function getContributorDataError(User $user, array $data) {
        $error = null;
        $accessError = $this->getAccessError($user);
        if($accessError != null) {
            $error = $accessError;
        } else if(!array_key_exists('name', $data) || gettype($data['name']) != 'string') {
            $error = 'name.empty';
        } else if(!array_key_exists('role', $data) || gettype($data['role']) != 'string') {
            $error = 'role.empty';
        } else if(!array_key_exists('avatar', $data) || !($data['avatar'] instanceof UploadedFile)) {
            $error = 'avatar.empty';
        }
        return $error;
    }

    private function flushDatabase() : bool {
        $result;
        try {
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }

}