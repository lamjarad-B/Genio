<?php

namespace App\Controller;

use App\Entity\Personne;
use App\Form\CreateTreeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CreateTreeController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }
    
    #[Route('/create_tree', name: 'app_create_tree')]
    public function index(Request $request): Response
    {
          // Crée un objet Personne vide pour l'utiliser pour créer le formulaire.
          $personne1 = new Personne();
          $personne2 = new Personne();

          // Crée le formulaire de recherche de personne.
          $form = $this->createForm(CreateTreeType::class);
  
          // Handle the form submission
          $form->handleRequest($request);
          

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérez les données du formulaire
            //dd($form);
            $personne1 = $form->get('groupe_pere')->getData();
            $personne2 = $form->get('groupe_mere')->getData();
            //dd($personne1);

            $nom = $personne1['nom'];
            $prenom = $personne1['prenom'];
            $date_naissance = $personne1['date_naissance'];
            $date_deces = $personne1['date_deces'];
            $lieu_naissance = $personne1['lieu_naissance'];

            $nomMere = $personne2['nom'];
            $prenomMere = $personne2['prenom'];
            $date_naissance_mere = $personne2['date_naissance'];
            $date_deces_mere = $personne2['date_deces'];
            $lieu_naissance_mere = $personne2['lieu_naissance'];

            // $nomMere = $data->getNom();
            // $prenomMere = $data->getPrenom();
            // $date_naissance_mere = $data->getDateNaissance();
            // $date_deces_mere = $data->getDateDeces();
            // $lieu_naissance_mere = $data->getLieuNaissance();
        
            
            $this->entityManager->getRepository(Personne::class)->createTree(
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
        }

        return $this->render('create_tree/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
