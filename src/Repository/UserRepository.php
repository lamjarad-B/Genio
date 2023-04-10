<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\PasswordUpgraderInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository implements PasswordUpgraderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function save(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(User $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Used to upgrade (rehash) the user's password automatically over time.
     */
    public function upgradePassword(PasswordAuthenticatedUserInterface $user, string $newHashedPassword): void
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', \get_class($user)));
        }

        $user->setPassword($newHashedPassword);

        $this->save($user, true);
    }


    public function getArbreGenealogique($userId)
    {
        $entityManager = $this->getEntityManager();
    
        // Récupération de l'utilisateur initial
        $user = $entityManager->getRepository(User::class)->findOneBy(array('id' => $userId));
    
        if (!$user) {
            return null;
        }
    
        $arbreGenealogique = array();
    
        // Ajout de l'utilisateur initial à l'arbre généalogique
        $arbreGenealogique[] = $user;
    
        // Récupération des deux parents
        $parents = array($user->getParent1(), $user->getParent2());
    
        foreach ($parents as $parent) {
            if ($parent) {
                $parentEntity = $entityManager->getRepository(User::class)->findOneBy(array('id' => $parent));
                if ($parentEntity) {
                    // Ajout du parent à l'arbre généalogique
                    $arbreGenealogique[] = $parentEntity;
    
                    // Récupération des deux grands-parents
                    $grandsParents = array($parentEntity->getParent1(), $parentEntity->getParent2());
    
                    foreach ($grandsParents as $grandParent) {
                        if ($grandParent) {
                            $grandParentEntity = $entityManager->getRepository(User::class)->findOneBy(array('id' => $grandParent));
                            if ($grandParentEntity) {
                                // Ajout du grand-parent à l'arbre généalogique
                                $arbreGenealogique[] = $grandParentEntity;
    
                                // Récupération des arrières grands-parents, etc.
                                $arriereGrandsParents = array($grandParentEntity->getParent1(), $grandParentEntity->getParent2());
    
                                foreach ($arriereGrandsParents as $arriereGrandParent) {
                                    if ($arriereGrandParent) {
                                        $arriereGrandParentEntity = $entityManager->getRepository(User::class)->findOneBy(array('id' => $arriereGrandParent));
                                        if ($arriereGrandParentEntity) {
                                            $arbreGenealogique[] = $arriereGrandParentEntity;
    
                                            // Récupération des arrières arrières grands-parents, etc.
                                            while ($arriereGrandParentEntity->getParent1() || $arriereGrandParentEntity->getParent2()) {
                                                if ($arriereGrandParentEntity->getParent1()) {
                                                    $arriereGrandParentEntity = $entityManager->getRepository(User::class)->findOneBy(array('id' => $arriereGrandParentEntity->getParent1()));
    
                                                    if ($arriereGrandParentEntity) {
                                                        $arbreGenealogique[] = $arriereGrandParentEntity;
                                                    }
                                                }
    
                                                if ($arriereGrandParentEntity->getParent2()) {
                                                    $arriereGrandParentEntity = $entityManager->getRepository(User::class)->findOneBy(array('id' => $arriereGrandParentEntity->getParent2()));
    
                                                    if ($arriereGrandParentEntity) {
                                                        $arbreGenealogique[] = $arriereGrandParentEntity;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    
        return $arbreGenealogique;
    }
    
    


//    /**
//     * @return User[] Returns an array of User objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('u.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?User
//    {
//        return $this->createQueryBuilder('u')
//            ->andWhere('u.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
