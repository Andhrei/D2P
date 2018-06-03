<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 6/3/2018
 * Time: 1:58 PM
 */

namespace App\Security\User;


use App\Entity\LdapUser;
use App\Manager\LdapUserManager;
use App\Repository\LdapUserRepository;
use App\Security\Ldap\LdapService;
use Symfony\Component\Ldap\Ldap;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LdapUserProvider implements UserProviderInterface
{
    private $service;
    private $manager;

    public function __construct(LdapService $service, LdapUserManager $manager)
    {
        $this->service = $service;
        $this->manager = $manager;
    }

    /**
     * Loads the user for the given username.
     *
     * This method must throw UsernameNotFoundException if the user is not
     * found.
     *
     * @param string $username The username
     *
     * @return UserInterface
     *
     * @throws UsernameNotFoundException if the user is not found
     */
    public function loadUserByUsername($username)
    {
        $entry = $this->service->getLdapUser($username);

        if($entry) {
            $user = $this->manager->findBy(['username' => $entry->getAttribute('sAMAccountName')[0]]);

            if(!$user)
            {
//                $user = new LdapUser($entry);
                $user = $this->manager->createFromEntry($entry);
            } else {
                $user = $user[0];
            }

            return $user;
        }

        throw new UsernameNotFoundException(
            sprintf('Username "%s" does not exist.',$username)
        );
    }

    /**
     * Refreshes the user.
     *
     * It is up to the implementation to decide if the user data should be
     * totally reloaded (e.g. from the database), or if the UserInterface
     * object can just be merged into some internal array of users / identity
     * map.
     *
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof LdapUser) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class)
    {
        return LdapUser::class === $class;
    }
}