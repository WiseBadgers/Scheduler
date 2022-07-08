<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Note;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class NoteVoter extends Voter
{
    public const GET_ITEM = 'GET_ITEM';
    public const DELETE_ITEM = 'DELETE_ITEM';
    public const PATCH_ITEM = 'PATCH_ITEM';

    protected function supports(string $attribute, $subject): bool
    {
        return in_array($attribute, [self::GET_ITEM, self::DELETE_ITEM, self::PATCH_ITEM])
            && $subject instanceof Note;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        /* @var Note $subject */
        return match ($attribute) {
            'GET_ITEM' => $this->getItem($user, $subject),
            'DELETE_ITEM' => $this->deleteItem($user, $subject),
            'PATCH_ITEM' => $this->patchItem($user, $subject),
        };
    }

    private function getItem(UserInterface $user, Note $subject): bool
    {
        return $user === $subject->getStudent() || $user === $subject->getTeacher();
    }

    private function deleteItem(UserInterface $user, Note $subject): bool
    {
        return $user === $subject->getTeacher();
    }

    private function patchItem(UserInterface $user, Note $subject): bool
    {
        return $user === $subject->getTeacher();
    }
}
