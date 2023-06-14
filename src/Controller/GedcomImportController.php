<?php
namespace App\Controller;
use App\Entity\Relation;
use App\Entity\TypeRelation;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use App\Form\FileUploadType;
use Symfony\Component\Routing\Annotation\Route;
use DateTime;
use App\Entity\Personne;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PersonneRepository;

class GedcomImportController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/import', name: 'app_import')]
    public function excelCommunesAction(Request $request, FileUploader $file_uploader)
    {
        $i = 0;
        $j = 0;
        $k = 0;

        $user = $this->getUser();

        if (!$user) {
            $cnx = "Connexion";
        }
        else{
            $cnx = "Déconnexion";
        }


        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);
        $fileUploaded = false;
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['upload_file']->getData();
            if ($file) {
                $fileUploaded = true;
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory . '/' . $file_name;
                    // Do what you want with the full path file...
                    // Why not read the content or parse it !!!

                    //-----------------------DEBUT PARSE DOCUMENT---------------------------------
                    $monFichier = ($full_path);
                    $buffer = [];
                    $nameLines = [];
                    if (file_exists($monFichier)) {
                        $handle = fopen($monFichier, 'r');

                        if ($handle) {
                            while (!feof($handle)) {
                                $buffer[] = fgets($handle);
                            }

                            //-----------------------------INITIALISATION---------------------

                            $firstNames = []; // initialisez votre tableau qui contiendra les prénoms
                            $lastNames = []; // initialisez votre tableau qui contiendra les noms de famille
                            $sexe = [];
                            $naissance = [];
                            $birtFound = false;
                            $deathFound = false;
                            $mort = [];
                            $mortFormatee = [];
                            $naissanceFormatee = [];
                            $id = [];
                            $dateNaissanceFormatee = null;
                            $dateMortFormatee = null;
                            $firstDateAfterBirt = null;
                            $dateNaissance = '';
                            $dateMort = '';
                            $indi = 0;
                            $essaie = false;
                            $dateFound = false;


                            $personne1 = null;
                            $personne2 = null;
                            $personne3 = null;
                            $personne1a = [];
                            $personne2a = [];
                            $personne3a = [];
                            $indi1 = 0;
                            $husbFound = false;
                            $wifeFound = false;
                            $chilFound = false;
                            $relationMere=null;
                            $relationPere=null;


                            //----------------------DEBUT PARSE----------------------------

                            foreach ($buffer as $line => $test) {
                                if (strpos($test, 'INDI')) {
                                    $id[] = trim(str_replace(['INDI', '@'], '', preg_replace('/^0([^0\s]*)/', '$1', $test)));
                                }

                                if (strpos($test, 'NAME')) {
                                    $name = trim(str_replace(['NAME', "\n", "\r", 1], '', $test)); // supprime les espaces, les sauts de ligne et les caractères inutiles
                                    $nameParts = explode('/', $name);
                                    if (count($nameParts) > 1) {
                                        $firstNames[] = $nameParts[0];
                                        $lastNames[] = $nameParts[1];
                                    }
                                }
                                if (strpos($test, 'SEX')) {
                                    $sexe[] = trim(str_replace(['SEX', "\n", "\r", " ", "1"], '', $test));
                                }


//----------------------RELATIONS----------------------------


//                    ---------------------------TROUVER DATE NAISSANCE ---------------------------------------

                                if (strpos($test, 'BIRT')) {
                                    $birtFound = true;
                                }
                                if ($birtFound && strpos($test, 'DATE')) {

                                    $naissance[] = trim(str_replace(['2 DATE', "\n", "\r"], '', $test));
                                    //Convertie l'Array $naissance en String
                                    $dateNaissanceStr = end($naissance);


                                    // Vérifier si la variable $naissance contient au moins l'un des mots recherchés
                                    $mois = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                                    $motTrouve = false;
                                    $joursTrouve = false;

                                    $words = explode(' ', $dateNaissanceStr);
                                    $premierMot = $words[0];

                                    foreach ($mois as $mot) {
                                        if (strpos($dateNaissanceStr, $mot) !== false) {
                                            $motTrouve = true;
                                            if ($premierMot === $mot) {
                                                $joursTrouve = true;
                                                break;
                                            }
                                            break;
                                        }

                                    }

                                    if ($joursTrouve) {
                                        $dateNaissanceStr = '01 ' . $dateNaissanceStr;
                                    }

                                    if ($motTrouve) {
                                        // La variable $dateNaissanceStr contient au moins l'un des mots recherchés
                                    } else {
                                        // Si la date n'es pas assez précise, nous instention cette date au 01 JAN
                                        if (strpos($dateNaissanceStr, 'BEF') !== false) {
                                            $dateNaissanceStr = str_replace('BEF', '01 JAN', $dateNaissanceStr);
                                        } elseif (strpos($dateNaissanceStr, 'ABT') !== false) {
                                            $dateNaissanceStr = str_replace('ABT', '01 JAN', $dateNaissanceStr);
                                        } elseif (strpos($dateNaissanceStr, 'BET') !== false) {
                                            $lastSpace = strrpos($dateNaissanceStr, ' ');
                                            $dateNaissanceStr = substr($dateNaissanceStr, $lastSpace + 1);
                                            $dateNaissanceStr = '01 JAN ' . $dateNaissanceStr;
                                        } else {
                                            $dateNaissanceStr = '01 JAN ' . $dateNaissanceStr;
                                        }
                                    }
                                    //Formatage de la date pour passer du type de 01 JAN 0000
                                    $dateNaissance = DateTime::createFromFormat('j M Y', $dateNaissanceStr);
                                    if ($dateNaissance !== false) {
                                        $dateNaissanceFormatee = $dateNaissance->format('Y-m-d');
                                        $naissanceFormatee[] = $dateNaissanceFormatee;
                                        $birtFound = false;
                                    } else {
                                    }
                                    $birtFound = false;
                                }

                                /* -----------------------------------TROUVER MORT -------------------------------------*/

                                if (strpos($test, 'DEAT')) {
                                    $deathFound = true;
                                }
                                if (strpos($test, 'INDI')) {
                                    $indi++;

                                }

                                if ($deathFound && strpos($test, 'DATE') && $indi == 1) {
                                    $dateFound = true;

                                    $mort[] = trim(str_replace(['2 DATE', "\n", "\r"], '', $test));
                                    //Convertie l'Array $naissance en String
                                    $dateMortStr = end($mort);

                                    // Vérifier si la variable $naissance contient au moins l'un des mots recherchés
                                    $mois = ['JAN', 'FEB', 'MAR', 'APR', 'MAY', 'JUN', 'JUL', 'AUG', 'SEP', 'OCT', 'NOV', 'DEC'];
                                    $motTrouve = false;
                                    $joursTrouve = false;

                                    $words = explode(' ', $dateMortStr);
                                    $premierMot = $words[0];

                                    foreach ($mois as $mot) {
                                        if (strpos($dateMortStr, $mot) !== false) {
                                            $motTrouve = true;
                                            if ($premierMot === $mot) {
                                                $joursTrouve = true;
                                                break;
                                            }
                                            break;
                                        }
                                    }

                                    if ($joursTrouve) {
                                        $dateMortStr = '01 ' . $dateMortStr;
                                    }

                                    if ($motTrouve) {
                                        if (strpos($dateMortStr, 'BEF') !== false) {
                                            $dateMortStr = str_replace('BEF', '01', $dateMortStr);
                                        } elseif (strpos($dateMortStr, 'ABT') !== false) {
                                            $dateMortStr = str_replace('ABT', '01', $dateMortStr);
                                        } elseif (strpos($dateMortStr, 'AFT') !== false) {
                                            $dateMortStr = str_replace('AFT', '01', $dateMortStr);
                                        }

                                    } else {
                                        // Si la date n'es pas assez précise, nous instention cette date au 01 JAN
                                        if (strpos($dateMortStr, 'BEF') !== false) {
                                            $dateMortStr = str_replace('BEF', '01 JAN', $dateMortStr);
                                        } elseif (strpos($dateMortStr, 'ABT') !== false) {
                                            $dateMortStr = str_replace('ABT', '01 JAN', $dateMortStr);
                                        } elseif (strpos($dateMortStr, 'AFT') !== false) {
                                            $dateMortStr = str_replace('AFT', '01 JAN', $dateMortStr);
                                        } elseif (strpos($dateMortStr, 'BET') !== false) {
                                            $lastSpace = strrpos($dateMortStr, ' ');
                                            $dateMortStr = substr($dateMortStr, $lastSpace + 1);
                                            $dateMortStr = '01 JAN ' . $dateMortStr;
                                        } else {
                                            $dateMortStr = '01 JAN ' . $dateMortStr;
                                        }


                                    }
                                    //Formatage de la date pour passer du type de 01 JAN 0000
                                    $dateMort = DateTime::createFromFormat('j M Y', $dateMortStr);
                                    if ($dateMort !== false) {
                                        $dateMortFormatee = $dateMort->format('Y-m-d');
                                        $mortFormatee[] = $dateMortFormatee;


                                    } else {
                                        $dateMortStr = substr($dateMortStr, -10);
                                        $dateMort = DateTime::createFromFormat('j M Y', $dateMortStr);
                                        if ($dateMort !== false) {
                                            $dateMortFormatee = $dateMort->format('Y-m-d');
                                            $mortFormatee[] = $dateMortFormatee;
                                        }
                                    }
                                    $indi = 0;
                                    $deathFound = false;
                                    $dateFound = false;

                                } elseif ($indi == 2 && (!$deathFound || !$dateFound)) {
                                    $indi = 1;
                                    $mort[] = '01 JAN 0000';
                                    $dateMortStr = end($mort);
                                    $dateMort = DateTime::createFromFormat('j M Y', $dateMortStr);
                                    if ($dateMort !== false) {
                                        $dateMortFormatee = $dateMort->format('Y-m-d');
                                        $mortFormatee[] = $dateMortFormatee;

                                    } else {
                                    }
                                }


                            }

                            $personneRepository = $this->entityManager->getRepository(Personne::class);

                            foreach ($firstNames as $index => $firstName) {
                                $dateNaissance = DateTime::createFromFormat('Y-m-d', $naissanceFormatee[$index]);
                                $dateMort = DateTime::createFromFormat('Y-m-d', $mortFormatee[$index]);

                                $existingPersonne = $personneRepository->findIfExist(
                                    $id[$index]
                                );

                                if (empty($existingPersonne)) {
                                    $personne = new Personne();
                                    $personne->setPrenom($firstName);
                                    $personne->setNom($lastNames[$index]);
                                    $personne->setSexe($sexe[$index]);
                                    if ($dateNaissance !== false) {
                                        $personne->setDateNaissance($dateNaissance);
                                    }
                                    if ($dateMort !== false) {
                                        $personne->setDateDeces($dateMort);
                                    }
                                    $personne->setIdGedcom($id[$index]);

                                    $this->entityManager->persist($personne);

                                }
                            }
                            $this->entityManager->flush();

                            $typeRelation = $this->entityManager->getRepository(TypeRelation::class);
                            $relationPere = $typeRelation->findById(1);
                            $relationMere = $typeRelation->findById(2);
                            $relationEnfant = $typeRelation->findById(3);
                            $relationConjoint = $typeRelation->findById(4);

                            foreach ($buffer as $line => $test) {
                                // Recherche de HUSB
                                if (strpos($test, 'HUSB') !== false) {
                                    $k++;
                                    $personne1 = trim(str_replace(['1 HUSB', '@'], '', preg_replace('/^1 \(([^)]+)\)/', '$1', $test)));
                                    $tmp = $personneRepository->findForRelation(
                                        $personne1
                                    );
                                    if (count($tmp) == 1) $personne1a[] = $tmp;//[0]["id"];
//                                    else continue;
                                    $indi1++;
                                }
// Recherche de WIFE
                                if (strpos($test, 'WIFE') !== false) {
                                    $k++;
                                    $personne2 = trim(str_replace(['1 WIFE',  '@'], '', preg_replace('/^1 \(([^)]+)\)/', '$1', $test)));
                                    $tmp = $personneRepository->findForRelation(
                                        $personne2
                                    );
                                    if (count($tmp) == 1) $personne2a[] = $tmp;//[0]["id"];
//                                    else $personne2a[] = "pas de femme";
                                }
// Recherche de CHIL
                                if (strpos($test, 'CHIL') !== false) {
                                    $k++;
                                    $personne3 = trim(str_replace(['1 CHIL',  '@'], '', preg_replace('/^1 \(([^)]+)\)/', '$1', $test,1)));
                                    $tmp = $personneRepository->findForRelation(
                                        $personne3
                                    );
                                    if (count($tmp) == 1) $personne3a[] = $tmp;//[0]["id"];
//                                    else $personne3a[] = "pas d'enfant";
                                }
                            }

                            foreach ($personne1a as $index => $personne1ar) {
//                                dd($personne1ar);
                                if (($personne3a[$index][0] != "pas d'enfant" && $personne1a[$index] != "pas de mari")) {
                                    $relation = new Relation();
                                    $relation->setPersonne1($this->entityManager->find(Personne::class, $personne1ar[0]));
                                    $relation->setPersonne2($this->entityManager->find(Personne::class, $personne3a[$index][0]));
                                    $relation->setRelationType($relationPere[0]);
                                    $this->entityManager->persist($relation);
                                }
                            }

                            foreach ($personne3a as $index => $personne3ar) {
                                if (!empty($existingPersonne) && ($personne3a[$index][0] != "pas d'enfant" && $personne2a[$index][0] != "pas de femme")) {
                                    $relation = new Relation();
                                    $relation->setPersonne1($this->entityManager->find(Personne::class, $personne2a[$index][0]));
                                    $relation->setPersonne2($this->entityManager->find(Personne::class, $personne3ar[0]));
                                    $relation->setRelationType($relationMere[0]);
                                    $this->entityManager->persist($relation);
                                }
                            }
                            foreach ($personne3a as $index => $personne3ar) {
                                if (!empty($existingPersonne) && ($personne3a[$index][0] != "pas d'enfant" && $personne2a[$index][0] != "pas de femme")) {
                                    $relation = new Relation();
                                    $relation->setPersonne1($this->entityManager->find(Personne::class, $personne3ar[0]));
                                    $relation->setPersonne2($this->entityManager->find(Personne::class, $personne2a[$index][0]));
                                    $relation->setRelationType($relationEnfant[0]);
                                    $this->entityManager->persist($relation);
                                }
                            }
                            foreach ($personne1a as $index => $personne1ar) {
//                                dd($personne1ar);
                                if (($personne3a[$index][0] != "pas d'enfant" && $personne1a[$index] != "pas de mari")) {
                                    $relation = new Relation();
                                    $relation->setPersonne1($this->entityManager->find(Personne::class, $personne3a[$index][0]));
                                    $relation->setPersonne2($this->entityManager->find(Personne::class, $personne1ar[0]));
                                    $relation->setRelationType($relationEnfant[0]);
                                    $this->entityManager->persist($relation);
                                }
                            }
                            foreach ($personne1a as $index => $personne1ar) {
//                                dd($personne1ar);
                                if (($personne2a[$index][0] != "pas de femme" && $personne1a[$index] != "pas de mari")) {
                                    $relation = new Relation();
                                    $relation->setPersonne1($this->entityManager->find(Personne::class, $personne2a[$index][0]));
                                    $relation->setPersonne2($this->entityManager->find(Personne::class, $personne1ar[0]));
                                    $relation->setRelationType($relationConjoint[0]);
                                    $this->entityManager->persist($relation);
                                }
                            }
                            foreach ($personne1a as $index => $personne1ar) {
//                                dd($personne1ar);
                                if (($personne2a[$index][0] != "pas de femme" && $personne1a[$index] != "pas de mari")) {
                                    $relation = new Relation();
                                    $relation->setPersonne1($this->entityManager->find(Personne::class, $personne1ar[0]));
                                    $relation->setPersonne2($this->entityManager->find(Personne::class, $personne2a[$index][0]));
                                    $relation->setRelationType($relationConjoint[0]);
                                    $this->entityManager->persist($relation);
                                }
                            }

                            $this->entityManager->flush();


                            fclose($handle);
                        }
                        else{
                            $buffer[] = "pas de liste";
                        }

                    } else {
                        $buffer[] = "fichier non trouvé";
                        $this->redirectToRoute('app_import');
                    }

                    return $this->render('gedcom_import/index.html.twig', ['id' => $id, 'firstNames' => $firstNames, 'lastNames' => $lastNames, 'sexe' => $sexe,
                        'naissance' => $naissanceFormatee, 'mort' => $mortFormatee, 'form' => $form->createView(), 'fileUploaded' => $fileUploaded,
                        'cnx' => $cnx, 'user' => $user
                    ]);
                } else {
                    // Oups, an error occured !!!
                }
                // return $this->redirectToRoute('app_affiche');

            }
        }
        return $this->render('gedcom_import/index.html.twig', [
            'form' => $form->createView(),
            'fileUploaded' => $fileUploaded,
            'cnx' => $cnx,
            'user' => $user,

        ]);
    }

}
