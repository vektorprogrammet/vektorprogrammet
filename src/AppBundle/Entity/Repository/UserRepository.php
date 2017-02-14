<?php

namespace AppBundle\Entity\Repository;

use AppBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository implements UserProviderInterface
{
    public function findAllUsersByDepartment($department)
    {
        $users = $this->getEntityManager()->createQuery('
		
		SELECT u
		FROM AppBundle:User u
		JOIN u.fieldOfStudy fos
		JOIN fos.department d
		WHERE d.id = :department
		
		')
            ->setParameter('department', $department)
            ->getResult();

        return $users;
    }

    public function findAllActiveUsersByDepartment($department)
    {
        $users = $this->getEntityManager()->createQuery('
		
		SELECT u
		FROM AppBundle:User u
		JOIN u.fieldOfStudy fos
		JOIN fos.department d
		WHERE d.id = :department
			AND u.isActive = :active
		')
            ->setParameter('department', $department)
            ->setParameter('active', 1)
            ->getResult();

        return $users;
    }

    public function findAllInActiveUsersByDepartment($department)
    {
        return $this->createQueryBuilder('user')
            ->select('user')
            ->join('user.fieldOfStudy', 'fos')
            ->where('user.isActive = false')
            ->andWhere('fos.department = :department')
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    public function findAllUsersByDepartmentAndRoles($department, $roles)
    {
        return $this->createQueryBuilder('u')
            ->select('u')
            ->join('u.roles', 'r')
            ->join('u.fieldOfStudy', 'f')
            ->join('f.department', 'd')
            ->where('r.role IN (:roles)')
            ->andWhere('d.id = :department')
            ->setParameter('roles', $roles)
            ->setParameter('department', $department)
            ->getQuery()
            ->getResult();
    }

    public function findUserByUsername($username)
    {
        return $this->createQueryBuilder('User')
            ->select('User')
            ->where('User.user_name = :username')
            ->setParameter('username', $username)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param $email
     *
     * @return User
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByEmail($email)
    {
        return $this->createQueryBuilder('User')
            ->select('User')
            ->where('User.email = :email')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findUserById($id)
    {
        return $this->createQueryBuilder('User')
            ->select('User')
            ->where('User.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param $id
     *
     * @return User
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function findUserByNewUserCode($id)
    {
        return $this->createQueryBuilder('User')
            ->select('User')
            ->where('User.new_user_code = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /*
    These functions are used by UserProviderInterface
    */

    public function loadUserByUsername($username)
    {
        $q = $this
            ->createQueryBuilder('u')
            ->where('u.user_name = :user_name OR u.email = :email')
            ->setParameter('user_name', $username)
            ->setParameter('email', $username)
            ->getQuery();

        try {
            // The Query::getSingleResult() method throws an exception
            // if there is no record matching the criteria.
            $user = $q->getSingleResult();
        } catch (NoResultException $e) {
            $message = sprintf(
                'Unable to find an active admin VektorVektorBundle:User object identified by "%s".',
                $username
            );
            throw new UsernameNotFoundException($message, 0, $e);
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        $class = get_class($user);
        if (!$this->supportsClass($class)) {
            throw new UnsupportedUserException(
                sprintf(
                    'Instances of "%s" are not supported.',
                    $class
                )
            );
        }

        return $this->find($user->getId());
    }

    public function supportsClass($class)
    {
        return $this->getEntityName() === $class
        || is_subclass_of($class, $this->getEntityName());
    }
}
