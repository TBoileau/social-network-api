<?php

namespace App\Security\Provider;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class UserProvider
 * @package App\Security\Provider
 */
class UserProvider implements UserProviderInterface
{
    /**
     * @var UserLoaderInterface
     */
    private UserLoaderInterface $userLoader;

    /**
     * UserProvider constructor.
     * @param UserLoaderInterface $userLoader
     */
    public function __construct(UserLoaderInterface $userLoader)
    {
        $this->userLoader = $userLoader;
    }

    public function loadUserByUsername(string $username)
    {
        return $this->userLoader->loadUserByUsername($username);
    }

    public function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }

    public function supportsClass(string $class)
    {
        return $class === User::class;
    }
}
