<?php

namespace GenjioBundle\Security;

use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Doctrine\ORM\EntityManager;
use GenjioBundle\Entity\User as GenjioUser;

class ApiUserProvider implements UserProviderInterface
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function loadUserByUsername($username)
    {
        $repo = $this->em->getRepository('GenjioBundle:User');
        $user = $repo->find($username);
        if (!$user) throw new UsernameNotFoundException('User does not exist');
        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return $class === GenjioUser::class;
    }
}
