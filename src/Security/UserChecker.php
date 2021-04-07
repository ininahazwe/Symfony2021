<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
        if(!$user instanceof User){
            return;
        }
    }
    public function checkPostAuth(UserInterface $user): void
    {
        if(!$user instanceof User){
            return;
        }
        /* Warning, if you enter a wrong password, the exception will be displayed. */
        if(!$user->getIsVerified()){
            throw new CustomUserMessageAccountStatusException("Please check you mailbox for account validation before {$user->getAccountMustBeVerifiedBefore()->format('d/m/Y @ H\hi')}");
        }
    }
}