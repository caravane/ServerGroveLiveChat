<?php

namespace ServerGrove\LiveChatBundle\Document;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\AccountInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use ServerGrove\LiveChatBundle\Document\Operator;
use MongoDate;

/**
 * Description of OperatorRepository
 *
 * @author Ismael Ambrosi<ismael@servergrove.com>
 */
class OperatorRepository extends DocumentRepository implements UserProviderInterface
{

    /**
     * @return ServerGrove\LiveChatBundle\Document\Operator
     */
    public function loadUserByAccount(AccountInterface $user)
    {
        if (($user instanceof Operator)) {
            return $user;
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @return ServerGrove\LiveChatBundle\Document\Operator
     */
    public function loadUserByUsername($username)
    {
        $operator = $this->findOneBy(array('email' => $username));
        if (!$operator) {
            throw new UsernameNotFoundException('Invalid username');
        }

        return $operator;
    }

    public function getOnlineOperatorsCount()
    {
        return $this->createQueryBuilder()->field('isOnline')->equals(true)->getQuery()->count();
    }

    public function closeOldLogins()
    {
        $this->createQueryBuilder()
                ->field('isOnline')->set(false)
                ->field('isOnline')->equals(true)
                ->field('updatedAt')->lt(new MongoDate(time() - 86400))
                ->update()->getQuery()
                ->execute();
    }

    public function supportsClass($class)
    {
        return 'Operator' == $class;
    }

    /**
     * Refreshes the user for the account interface.
     *
     * It is up to the implementation if it decides to reload the user data
     * from the database, or if it simply merges the passed User into the
     * identity map of an entity manager.
     *
     * @throws UnsupportedUserException if the account is not supported
     * @param UserInterface $user
     *
     * @return UserInterface
     */
    function refreshUser(UserInterface $user)
    {
        // TODO: Implement refreshUser() method.
    }
}