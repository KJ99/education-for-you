<?php
namespace App\Service;

use App\Service\EntityService;
use App\Service\PictureService;
use App\Processor\SiteSettingsProcessor;
use App\Entity\Subject;
use App\Entity\Level;
use App\Entity\User;
use App\Entity\Contributor;


use App\Result\AdminSiteSettingsResult;
use App\Exception\AdminSiteSettingsException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;

class SiteSettingsService extends EntityService {
    private $processor;

    public function __construct(EntityManagerInterface $em, PictureService $pictureService) {
        $this->processor = new SiteSettingsProcessor($em, $pictureService);
        parent::__construct($em);
    }

    public function addContributor(User $user, array $data) : AdminSiteSettingsResult {
        $result = new AdminSiteSettingsResult();
        try {
            $contributor = $this->processor->processContributorAddition($user, $data);
            $result->setSuccess(true)->setData($contributor);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function removeContributor(User $user, Contributor $contributor) : AdminSiteSettingsResult {
        $result = new AdminSiteSettingsResult();
        try {
            $contributor = $this->processor->processContributorRemoval($user, $contributor);
            $result->setSuccess(true)->setData($contributor);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function setSiteDescription(User $user, string $data) : AdminSiteSettingsResult {
        $result = new AdminSiteSettingsResult();
        try {
            $desc = $this->processor->processSiteDescriptionChange($user, $data);
            $result->setSuccess(true)->setData($desc);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    public function setPrivacyPolicy(User $user, string $data) : AdminSiteSettingsResult {
        $result = new AdminSiteSettingsResult();
        try {
            $policy = $this->processor->processPrivacyPolicyChange($user, $data);
            $result->setSuccess(true)->setData($policy);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    
    public function setTermsOfUse(User $user, string $data) : AdminSiteSettingsResult {
        $result = new AdminSiteSettingsResult();
        try {
            $terms = $this->processor->processTermsOfUseChange($user, $data);
            $result->setSuccess(true)->setData($terms);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }
}