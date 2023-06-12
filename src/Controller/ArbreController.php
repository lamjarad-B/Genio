<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Repository\ArbreRepository;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\Request;

class ArbreController extends AbstractController
{   
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/arbre', name: 'app_arbre')]
    public function index(Connection $connection, Request $request, ArbreRepository $arbreRepository, EntityManagerInterface $entityManager): Response
    {   
        $queryBuilder = $entityManager->createQueryBuilder();
        $conn = $entityManager->getConnection();
        $user = $this->getUser();
        if($user){
            $cnx = "Déconnexion";
            $queryBuilder = $entityManager->createQueryBuilder();

            $personneRepository = $this->entityManager->getRepository(Personne::class);

            $userId = $user->getId();

            try 
            {
                $query = $queryBuilder
                ->select('p.id')
                ->from('App\Entity\Personne', 'p')
                ->where('p.user = :userId')
                ->setParameter('userId', $userId)
                ->getQuery();

                $personId = $query->getSingleScalarResult();

                $personne = $personneRepository->find($personId);

                
                $ancestors = $arbreRepository->getAncestors($personId);
                
            }
            catch(\Doctrine\ORM\NoResultException)
            {
                return $this->redirectToRoute("app_create_tree");
            }
            
        }
       else{
        $cnx = "Connexion";
        $personneRepository = $this->entityManager->getRepository(Personne::class);
        $personId = $request->get('id');
        $personne = $personneRepository->find($personId);
        
        $ancestors = $arbreRepository->getAncestors($personId);

        $this->redirectToRoute("app_create_tree");
       }
       


       // Chercher conjoint(e)
       $query = "SELECT *
            FROM Relation R
            INNER JOIN Personne P1 ON R.personne1_id = P1.id
            INNER JOIN Personne P2 ON R.personne2_id = P2.id
            INNER JOIN type_relation TR ON R.relation_type_id = TR.id
            WHERE P1.id = :personId
            AND TR.id = :relation_type_id
        ";
        $params = [
            'personId' => $personId,
            'relation_type_id' => 4,
        ];

        $results = $conn->executeQuery($query, $params);

        $conjoint = $results->fetch();


        // Afiicher les frères et soeurs

        // 1 On récupère le père du personId et on le stock dans personne1_id
        $pere = "
            SELECT R.personne1_id AS pere_id
            FROM Relation R
            INNER JOIN type_relation TR ON R.relation_type_id = TR.id
            WHERE R.personne2_id = :personId
            AND TR.nom_relation = 'père' GROUP BY pere_id
        ";

        $params = [
            'personId' => $personId,
        ];

        $results = $conn->executeQuery($pere, $params);
        $personne1_id = $results->fetchAssociative();
        $personne1_id = $personne1_id['pere_id'];

        // 2 On récupère le mère du personId et on le stock dans personne2_id
        $mere = "
            SELECT R.personne1_id AS mere_id
            FROM Relation R
            INNER JOIN type_relation TR ON R.relation_type_id = TR.id
            WHERE R.personne2_id = :personId
            AND TR.nom_relation = 'mere' GROUP BY mere_id
        ";

        $params = [
            'personId' => $personId,
        ];

        $results = $conn->executeQuery($mere, $params);
        $personne2_id = $results->fetchAssociative();
        $personne2_id = $personne2_id['mere_id'];

        // 3 On récupère les frères et soeurs
        $sql = "SELECT * 
            FROM Personne p
            INNER JOIN Relation r ON (p.id = r.personne1_id OR p.id = r.personne2_id)
            WHERE r.relation_type_id IN (
                SELECT tr.id
                FROM type_relation tr
                WHERE tr.nom_relation IN (:pere, :mere)
            )
            AND (r.personne1_id = :personne1_id OR r.personne2_id = :personne2_id)
            AND p.id != :personId
            AND p.id != :personne1_id
            AND p.id != :personne2_id
        ";
             $params = [
                'personId' => $personId,
                'pere'=> 'père',
                'mere'=> 'mère',
                'personne1_id'=> $personne1_id,
                'personne2_id'=> $personne2_id
            ];

            $results = $conn->executeQuery($sql, $params);

            $siblings = $results->fetchAssociative();
            if($siblings){
                $siblings = (object)$siblings;
            }
            

            // AFFicher les enfants

            $query = "
                SELECT DISTINCT P.*
                FROM Personne P
                INNER JOIN Relation R ON P.id = R.personne2_id
                INNER JOIN type_relation TR ON R.relation_type_id = TR.id
                WHERE R.personne1_id = :personId
                AND TR.nom_relation = :relation
                
            ";

            if($personne->getSexe() ==='M'){
                $relation = 'père';
            }
            else{
                $relation = 'mère';
            }

            $params = [
                'personId' => $personId,
                'relation' => $relation
            ];

            $results = $conn->executeQuery($query, $params);
            $children = $results->fetchAllAssociative();

        return $this->render('arbre/index.html.twig', [
            'personne' => @$personne,
            'ancestors' => @$ancestors,
            'originId' => @$personId,
            'user' => $user,
            'cnx' => $cnx,
            'conjoint' => $conjoint,
            'siblings' => $siblings,
            'children' => $children
        ]);
 
    }
    
}
