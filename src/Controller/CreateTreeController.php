<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Entity\User;
use App\Form\CreateTreeType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class CreateTreeController extends AbstractController
{
    private $entityManager;
    private $userRepository;

    public function __construct(EntityManagerInterface $entityManager, UserRepository $userRepository){
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }
    
    #[Route('/create_tree', name: 'app_create_tree')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        //dd($userId);
        
        if (!$user) {
            throw $this->createAccessDeniedException('Vous devez être connecté pour accéder à cette page.');
        }

        if (!$user) {
            $cnx = "Connexion";
        }
        else{
            $cnx = "Déconnexion";
        }

        // Crée des objets Personne vide pour l'utiliser pour créer le formulaire.
        $personne1 = new Personne();
        $personne2 = new Personne();
        $personne3 = new Personne();

        //$userId = $this->entityManager->find(User::class, $userId);

        $personne1->setNom($user->getNom()); 
        $personne1->setPrenom($user->getPrenom());

        $form = $this->createForm(CreateTreeType::class, $personne1);
        

        // Handle the form submission
        $form->handleRequest($request);
          

        if ($form->isSubmitted() && $form->isValid()) {

            // Récupérez les données du formulaire
            //dd($form);
            $personne1 = $form->get('groupe_proprietaire')->getData();
            $personne2 = $form->get('groupe_pere')->getData();
            $personne3 = $form->get('groupe_mere')->getData();
            //dd($personne1);

            $sexeProprietaire = $personne1['sexe'];
            $nomProprietaire = $personne1['nom'];
            $prenomProprietaire = $personne1['prenom'];
            $date_naissance_Proprietaire = $personne1['date_naissance'];
            //$date_deces_Proprietaire = $personne1['date_deces'];
            $lieu_naissance_Proprietaire = $personne1['lieu_naissance'];

            $nom = $personne2['nom'];
            $prenom = $personne2['prenom'];
            $date_naissance = $personne2['date_naissance'];
            $date_deces = $personne2['date_deces'];
            $lieu_naissance = $personne2['lieu_naissance'];

            $nomMere = $personne3['nom'];
            $prenomMere = $personne3['prenom'];
            $date_naissance_mere = $personne3['date_naissance'];
            $date_deces_mere = $personne3['date_deces'];
            $lieu_naissance_mere = $personne3['lieu_naissance'];

            
            
            $this->entityManager->getRepository(Personne::class)->createTree(
                $nomProprietaire,
                $prenomProprietaire,
                $sexeProprietaire,
                $date_naissance_Proprietaire,
                //$date_deces_Proprietaire,
                $lieu_naissance_Proprietaire,
                $userId,
                $nom,
                $prenom,
                $date_naissance,
                $date_deces,
                $lieu_naissance,
                $nomMere,
                $prenomMere,
                $date_naissance_mere,
                $date_deces_mere,
                $lieu_naissance_mere

            );

            return $this->redirectToRoute("app_edit_tree");

        }

        return $this->render('create_tree/index.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'cnx' => $cnx
        ]);
    }
}
