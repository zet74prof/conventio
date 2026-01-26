<?php

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVerifiedChecker implements UserCheckerInterface
{

    public function checkPreAuth(UserInterface $user): void
    {
        if (!$user instanceof User) {
            return;
        }

        // You can add checks here that happen BEFORE checking the password
        // (e.g. if the account is deleted/banned)
    }

    public function checkPostAuth(UserInterface $user, ?TokenInterface $token = null): void
    {
        if (!$user instanceof User) {
            return;
        }

        // This runs AFTER the password has been verified.
        // We do it here to prevent "User Enumeration" (hackers guessing emails).
        if (!$user->isVerified()) {
            // This exception message will be displayed to the user
            // We use a translation key here.
            throw new CustomUserMessageAccountStatusException('login.error.not_verified');
        }
    }
}
