<?php
namespace App\Controller;

use App\Entity\Personne;
use App\Form\SearchPersonType;
use DateTime;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager){
        $this->entityManager = $entityManager;
    }

    #[Route('', name: 'app_home')]
    public function index(Request $request): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $cnx = "Connexion";
        }
        else{
            $cnx = "Déconnexion";
        }

        // Crée un objet Personne vide pour l'utiliser pour créer le formulaire.
        $personne = new Personne();

        // Crée le formulaire de recherche de personne.
        $form = $this->createForm(SearchPersonType::class, $personne);

        // Handle the form submission
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupère les données soumises par l'utilisateur
            $data = $form->getData();

            $nomConjoint = $request->request->get('nomConjoint');
            $prenomConjoint = $request->request->get('prenomConjoint');

            // Récupère les personnes correspondantes dans la base de données
            $personnes = $this->entityManager->getRepository(Personne::class)->findBySearchCriteria($data, $nomConjoint, $prenomConjoint);


            // Retourne le résultat de la recherche
            return $this->render('home/search.html.twig', [
                'personnes' => $personnes,
                 'cnx' => $cnx,
                 'user' => $user
            ]);

        }
        // $nom = "Doe";
        // $prenom = "John";
        // $date_naissance = new DateTime("1990-01-01");
        // $date_deces = null;
        // $lieu_naissance = "Paris";
        // $nomMere = "Smith";
        // $prenomMere = "Jane";
        // $date_naissance_mere = new DateTime("1965-05-10");
        // $date_deces_mere = null;
        // $lieu_naissance_mere = "New York";

        // // Appeler la fonction checkPerson avec les fausses valeurs
        // $existingPerson = $this->entityManager->getRepository(Personne::class)->checkPerson(
        //     $nom,
        //     $prenom,
        //     $date_naissance,
        //     $date_deces,
        //     $lieu_naissance,
        //     $nomMere,
        //     $prenomMere,
        //     $date_naissance_mere,
        //     $date_deces_mere,
        //     $lieu_naissance_mere
        // );

        // // Vérifier le résultat
        // if ($existingPerson) {
        //     echo "La personne existe déjà.";
        // } else {
        //     echo "La personne n'existe pas.";
        // }

        return $this->render('home/index.html.twig',
        [
            'form' => $form->createView(), // appeler la variable $form et creer la view de ce formulaire
            'cnx' => $cnx,
            'user' => $user
        ]);

    }


}
