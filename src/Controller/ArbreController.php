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
                //dd( $personId);
                $personne = $personneRepository->find($personId);
                //dd( $personne);
    
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
       // dd($ancestors);
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
        // dd($results);

        $conjoint = $results->fetch();

        return $this->render('arbre/index.html.twig', [
            'personne' => @$personne,
            'ancestors' => @$ancestors,
            'originId' => @$personId,
            'user' => $user,
            'cnx' => $cnx,
            'conjoint' => $conjoint
        ]);
 
    }
    
}
