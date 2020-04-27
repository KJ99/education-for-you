<?php
namespace App\Service;

use App\Service\EntityService;
use App\Service\PictureService;
//Token Service
//Mailer Service

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

use App\Result\UserResult;

use App\Entity\User;
use App\Entity\Picture;
use App\Entity\UserToken;

use App\Exception\EduException;
use App\Exception\UserException;
use App\Exception\PictureException;
use App\Exception\DatabaseException;

class UserService extends EntityService {
    private $pictureService;
    private $encoder;
    private $defaultAvatarPath;

    public function __construct(
        EntityManagerInterface $em, 
        UserPasswordEncoderInterface $encoder, 
        PictureService $pictureService,
        ParameterBagInterface $params) {
        
        $this->defaultAvatarPath = $params->get('kernel.project_dir') . '/public/assets/images/default-user-avatar.png';
        $this->encoder = $encoder;
        $this->pictureService = $pictureService;
        parent::__construct($em);
    }

    public function createUserFromConsole(InputInterface $input, OutputInterface $output, QuestionHelper $helper) : UserResult {
        $questions = [
            'email' => new Question('Enter an email address: '),
            'password' => (new Question('Enter a password: '))->setHidden(true)->setHiddenFallback(false),
            'confirmPassword' => (new Question('Confirm password: '))->setHidden(true)->setHiddenFallback(false),
            'nickname' => new Question('Enter a nickname (optional): '),
            'firstName' => new Question('Enter a first name (optional): '),
            'role_num' => new Question('Select a role ([1] Student, [2] Teacher, [3] Admin): '),
        ];
        $data = [];
        foreach ($questions as $key => $question) {
            $data[$key] = $helper->ask($input, $output, $question);
        } 
        $role = $this->resolveUserRoleFromConsoleInput($data['role_num']);
        unset($data['role_num']);
        
        $avatarPath = $this->defaultAvatarPath;
        
        return $this->createUser($data, $role, $avatarPath, true); 
    }

    private function resolveUserRoleFromConsoleInput(?string $inputValue) : ?array {
        $result;
        switch(intval($inputValue)) {
            case 1:
                $result = ['ROLE_STUDENT'];
            break;
            case 2:
                $result = ['ROLE_TEACHER'];
            break;
            case 3:
                $result = ['ROLE_TEACHER', 'ROLE_ADMIN'];
            break;
            default:
                $result = null;
            break;
        }
        return $result;
    }

    private function createUser(array $data, ?array $roles, string $avatarPath, bool $active = false) : UserResult {
        $result = new UserResult();
        try {
            $user = $this->processNewUser($data, $roles, $avatarPath, $active);
            $result->setSuccess(true)->setData($user);
        } catch(EduException $e) {
            $result->setSuccess(false)->setError($e);
        }
        return $result;
    }

    private function processNewUser(array $data, ?array $roles, string $avatarPath, bool $active = false) : User {
        $validationErrorTag = $this->findDataValidationErrorTag($data, $roles);
        if($validationErrorTag != null) {
            throw new UserException($validationErrorTag);
        }

        $pictureResult = $this->pictureService->resolvePicture($avatarPath);
        if(!$pictureResult->getSuccess()) {
            throw $pictureResult->getError();
        }
        $avatar = $pictureResult->getData();

        $user = $this->buildEntity($data, $roles, $avatar, $active);
        $saved = $this->saveEntity($user);

        if(!$saved) {
            $this->pictureService->deleteFile($avatar->getDirectory() . $avatar->getFileName());
            throw new DatabaseException('save.failed');
        }

        return $user;
    }

    private function findDataValidationErrorTag(array $data, ?array $roles) : ?string {
        $repository = $this->em->getRepository(User::class);
        $error = null;
        if($data['email'] == null || strlen(trim($data['email'])) == 0) {
            $error = 'email.not.found';
        } else if(!preg_match('/^..*@..*\...*$/', $data['email'])) {
            $error = 'email.not.valid';
        } else if($repository->findOneBy(['email' => $data['email']])) {
            $error = 'email.taken';
        }  else if($data['password'] == null || strlen(trim($data['password'])) == 0) {
            $error = 'password.not.found';
        } else if(strlen($data['password']) < 6) {
            $error = 'password.too.short';
        } else if($data['confirmPassword'] == null || strlen($data['confirmPassword']) == 0) {
            $error = 'confirm.password.not.found';
        } else if($data['confirmPassword'] !== $data['password']) {
            $error = 'password.not.same';
        } else if($data['nickname'] != null && strlen(trim($data['nickname'])) < 3) {
            $error = 'nickname.too.short';
        } else if($data['nickname'] != null && $repository->findOneBy(['nickname' => $data['nickname']])) {
            $error = 'nickname.taken';
        } else if($data['firstName'] != null && strlen(trim($data['firstName'])) == 0) {
            $error = 'first.name.too.short';
        } else if($roles == null) {
            $error = 'roles.not.found';
        } else if($data['nickname'] == null && $data['firstName'] == null) {
            $error = 'nickname.and.first.name.null';
        } 
        return $error;
    }

    private function buildEntity(array $data, array $roles, Picture $avatar, bool $active = false) : User {
        $user = new User();
        $nickname = $data['nickname'] != null ? trim($data['nickname']) : null;
        $firstName = $data['firstName'] != null ? trim($data['firstName']) : null;
        $user 
            ->setEmail(trim($data['email']))
            ->setNickname($nickname)
            ->setFirstName($firstName)
            ->setRoles($roles)
            ->setActive($active)
            ->setPassword($this->encoder->encodePassword($user, $data['password']))
            ->setAvatar($avatar);
        return $user;
    }

    private function saveEntity(User $user) : bool {
        $result;
        try {
            $this->em->persist($user->getAvatar());
            $this->em->persist($user);
            $this->em->flush();
            $result = true;
        } catch(\Exception $e) {
            $result = false;
        }
        return $result;
    }
}