<?php

namespace App\Controller;
use App\Entity\Personne;
use App\Repository\ArbreRepository;
use Doctrine\DBAL\Connection;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class EditTreeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('/editTree', name: 'app_edit_tree')]
    public function index(Request $request, ArbreRepository $arbreRepository, EntityManagerInterface $entityManager): Response
    {
        $queryBuilder = $entityManager->createQueryBuilder();

        $personneRepository = $this->entityManager->getRepository(Personne::class);

        $user = $this->getUser();
        $userId = $user->getId();
        $userName = $user->getNom();
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
            $cnx = "Connexion";
        }
        else{
            $cnx = "Déconnexion";
        }


        $query = $queryBuilder
            ->select('p.id')
            ->from('App\Entity\Personne', 'p')
            ->where('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery();

        $personId = $query->getSingleScalarResult();
        //dd( $personId);
        $personne = $personneRepository->find($personId);
        //dd($personne);
        $ancestors = $arbreRepository->getAncestors($personId);

        return $this->render('edit_tree/index.html.twig', [
             'personne' => $personne,
             'ancestors' => $ancestors,
             'originId' => $personId,
             'cnx' => $cnx,
             'user' => $user,
             'userName' => $userName,  
        ]);
    }

    #[Route('/addAncetors', name: 'app_add_ancetors')]
    public function addAncetors(Request $request, ArbreRepository $arbreRepository, EntityManagerInterface $entityManager): JsonResponse
    {

        $personId = $request->request->get("personId");
        $personId = intval($personId);

        $nom = $request->request->get('pere_nom');
        $prenom = $request->request->get('pere_prenom');

        $date_naissance = $request->request->get('pere_date_naissance');
        $date_naissance = new DateTime($date_naissance);

        $date_deces = $request->request->get('pere_date_deces');
        $date_deces = new DateTime($date_deces);

        $lieu_naissance = $request->request->get('pere_lieu_naissance');

        $nomMere = $request->request->get('mere_nom');
        $prenomMere = $request->request->get('mere_prenom');

        $date_naissance_mere = $request->request->get('mere_date_naissance');
        $date_naissance_mere = new DateTime($date_naissance_mere);

        $date_deces_mere = $request->request->get('mere_date_deces');
        $date_deces_mere = new DateTime($date_deces_mere);

        $lieu_naissance_mere = $request->request->get('mere_lieu_naissance');

        $this->entityManager->getRepository(Personne::class)->addAncetors(

            $personId,
            $nom,
            $prenom,
            $date_naissance,
            $date_deces,
            $lieu_naissance,
            $nomMere,
            $prenomMere,
            $date_naissance_mere,
            $date_deces_mere,
            $lieu_naissance_mere,

        );

		$newPereId = $this->entityManager->getRepository(Personne::class)->findBy(['nom' => $nom, 'prenom' => $prenom])[0]->getId();
		$newMereId = $this->entityManager->getRepository(Personne::class)->findBy(['nom' => $nomMere, 'prenom' => $prenomMere])[0]->getId();

        return new JsonResponse(["idPere" => $newPereId, "idMere" => $newMereId, "nomPere" => $nom ]);
    }
    #[Route('/editAncetors', name: 'app_edit_ancetors')]
    public function editAncetors(Request $request): Response
    {
        
        $conn = $this->entityManager->getConnection();

        $personId = $request->request->get("personId");
        $personId = intval($personId);

        $nom = $request->request->get('nom');
        $prenom = $request->request->get('prenom');

        $date_naissance = $request->request->get('date_naissance');
        $date_naissance = new DateTime($date_naissance);
        $date_naissance = $date_naissance->format('Y/m/d'); 

        $date_deces = $request->request->get('date_deces');
        $date_deces = new DateTime($date_deces);
        $date_deces = $date_deces->format('Y/m/d'); 

        $lieu_naissance = $request->request->get('lieu_naissance');

        $query = "UPDATE personne
        SET nom = :nom, prenom = :prenom, date_naissance = :dateNaissance, date_deces = :dateDeces, lieu_naissance = :lieuNaissance
        WHERE id = :personId
        ";
        $params = [
            'nom' => $nom,
            'prenom' => $prenom,
            'dateNaissance' => $date_naissance,
            'dateDeces' => $date_deces,
            'lieuNaissance' => $lieu_naissance,
            'personId' => $personId,
        ];

        $conn->executeQuery($query, $params);
        return new Response();
    }

    #[Route('/deleteAncetors', name: 'app_delete_ancetors')]
    public function deleteAncetors(Request $request): Response
    {
        $conn = $this->entityManager->getConnection();
        $personId = $request->request->get("personId");
        $personId = intval($personId);

        $query2 = "DELETE FROM relation WHERE personne1_id = :personId OR personne2_id = :personId";
        $params2 = [
            'personId' => $personId,
        ];

        $conn->executeQuery($query2, $params2);

        $query = "DELETE FROM personne WHERE id = :personId";
        $params = [
            'personId' => $personId,
        ];

        $conn->executeQuery($query, $params);

        return new Response();
    }
}
