<?php
namespace App\Processor;

use App\Entity\StudentGroup;
use App\Entity\GroupJoinRequest;
use App\Entity\GroupInviteToken;
use App\Entity\Level;
use App\Entity\User;

use App\Result\StudentGroupResult;
use App\Service\PictureService;
use App\Exception\StudentGroupException;
use App\Exception\DatabaseException;
use App\Exception\EduException;

use Doctrine\ORM\EntityManagerInterface;

class GroupJoinProcessor extends Processor {

    public function processJoinAttempt(User $user, StudentGroup $group) : GroupJoinRequest {
        $attemptError = $this->getJoinAttemptError($user, $group);
        if($attemptError != null) {
            throw new StudentGroupException($attemptError);
        }
        $request = new GroupJoinRequest();
        $request
            ->setStudentGroup($group)
            ->setUser($user);

        if($group->getAutoAccept()) {
            $this->acceptRequest($request);
        }

        $saved = $this->saveRequest($request);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $request;
    }

    public function processJoinRequestAccept(User $user, GroupJoinRequest $request) : GroupJoinRequest {
        return $this->processJoinRequestExecution($user, $request, true);
    }

    public function processJoinRequestDecline(User $user, GroupJoinRequest $request) : GroupJoinRequest {
        return $this->processJoinRequestExecution($user, $request, false);
    }

    private function processJoinRequestExecution(User $user, GroupJoinRequest $request, bool $accept) : GroupJoinRequest {
        if($request->getStudentGroup()->getTeacher()->getId() != $user->getId()) {
            throw new StudentGroupException('access.denied');
        }

        if($accept) {
            $this->acceptRequest($request);
        } else {
            $request->setAccepted(false);
        }

        $saved = $this->saveRequest($request);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $request;
    }

    public function processRequestCancelation(User $user, GroupJoinRequest $request) : GroupJoinRequest {
        $accessError = $this->getCancelAccessError($user, $request);
        if($accessError) {
            throw new StudentGroupException($accessError);
        }

        $saved = $this->deleteRequest($request);

        if(!$saved) {
            throw new DatabaseException('delete.failed');
        }

        return $request;
    }

    public function processInviteTokenCreation(User $user, StudentGroup $group) : GroupInviteToken {
        if($group()->getTeacher()->getId() != $user->getId()) {
            throw new StudentGroupException('access.denied');
        }
        $content = md5(uniqid() . $group()->getId() . uniqid() . time() . random_bytes(256));
        $token = new GroupInviteToken();
        $token
            ->setStudentGroup($group())
            ->setToken($content)
            ->setExpires(new \DateTime('+1 day'));
        
        $saved = $this->saveToken($token);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $token;
    }

    public function processInviteTokenUse(User $user, string $tokenString) : GroupInviteToken {
        $token = $this->em->getRepository(GroupInviteToken::class)->findOneBy(['token' => $tokenString]);
        if($token == null) {
            throw new StudentGroupException('token.not.found');
        }
        $error = $this->getJoinWithTokenError($user, $token);
        if($error != null) {
            throw new StudentGroupException($error);
        }
        $group = $token->getStudentGroup();
        $group->addStudent($user);
        
        $saved = $this->saveGroup($group);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $token;
    }

    public function processInviteTokenDeletion(User $user, GroupInviteToken $token) : GroupInviteToken {
        if($token->getStudentGroup()->getTeacher()->getId() != $user->getId()) {
            throw new StudentGroupException('access.denied');
        }

        $saved = $this->deleteToken($token);

        if(!$saved) {
            throw new DatabaseException('save.failed');
        }

        return $token;
        
    }

    private function saveRequest(GroupJoinRequest $request) : bool {
        $saved;
        try {
            $this->em->persist($request->getStudentGroup());
            $this->em->persist($request);
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }
        return $saved;
    }

    private function saveToken(GroupInviteToken $token) : bool {
        $saved;
        try {
            $this->em->persist($token);
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }
        return $saved;
    }

    private function saveGroup(StudentGroup $group) : bool {
        $saved;
        try {
            $this->em->persist($group);
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }
        return $saved;
    }

    private function deleteRequest(GroupJoinRequest $request) : bool {
        $saved;
        try {
            $this->em->remove($request);
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }
        return $saved;
    }

    private function deleteToken(GroupInviteToken $token) : bool {
        $saved;
        try {
            $this->em->remove($token);
            $this->em->flush();
            $saved = true;
        } catch(\Exception $e) {
            $saved = false;
        }
        return $saved;
    }

    private function acceptRequest(GroupJoinRequest $request) {
        $request->getStudentGroup()->addStudent($request->getUser());
        $request->setAccepted(true);
    }

    private function getJoinAttemptError(User $user, StudentGroup $group) : ?string {
        $error = null;
        $request = $this->em->getRepository(GroupJoinRequest::class)->findOneBy([
            'studentGroup' => $group,
            'user' => $user
        ]);
        if(!$user->isStudent()) {
            $error = 'access.denied';
        } else if($group->getTeacher()->getId() == $user->getId()) {
            $error = 'attempt.user.is.teacher';
        } else if($group->getStudents()->contains($user)) {
            $error = 'already.member';
        } else if($request != null) {
            $error = 'already.requested';
        } else if($group->getHidden()) {
            $error = 'group.private';
        }
        return $error;
    }

    private function getCancelAccessError(User $user, GroupJoinRequest $request) : ?string {
        $error = null;
        $isTeacher = $user->getId() == $request->getStudentGroup()->getTeacher()->getId();
        $isStudent = $user->getId() == $request->getUser()->getId();
        if(!$isStudent && !$isTeacher) {
            $error = 'access.denied';
        } else if($isStudent && $request->getAccepted() != null) {
            $error = 'cancel.request.executed';
        } else if($isTeacher && $request->getAccepted() == null) {
            $error = 'cancel.request.not.executed';
        } else if($isTeacher && $request->getAccepted()) {
            $error = 'cancel.request.already.accepted';
        }
        return $error;
    }

    private function getJoinWithTokenError(User $user, GroupInviteToken $token) : ?string {
        $error = null;
        $declinedRequest = $this->em->getRepository(GroupJoinRequest::class)->findOneBy([
            'studentGroup' => $token->getStudentGroup(),
            'user' => $user,
            'accepted' => false
        ]);

        if(!$user->isStudent()) {
            $error = 'access.denied';
        } else if($token->getStudentGroup()->getTeacher()->getId() == $user->getId()) {
            $error = 'attempt.user.is.teacher';
        } else if($token->getStudentGroup()->getStudents()->contains($user)) {
            $error = 'already.member';
        } else if($declinedRequest != null) {
            $error = 'already.declined';
        } else if($token->getStudentGroup()->getHidden()) {
            $error = 'group.private';
        } else if($token->getExpires()->getTimestamp() < strtotime('now')) {
            $error = 'token.expired';
        }

        return $error;
    }
 }