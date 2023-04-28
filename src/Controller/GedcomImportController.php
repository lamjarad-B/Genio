<?php
namespace App\Controller;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Service\FileUploader;
use App\Form\FileUploadType;
use Symfony\Component\Routing\Annotation\Route;

class GedcomImportController extends AbstractController
{
    #[Route('/import', name: 'app_import')]
    public function excelCommunesAction(Request $request, FileUploader $file_uploader)
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $file = $form['upload_file']->getData();
            if ($file)
            {
                $file_name = $file_uploader->upload($file);
                if (null !== $file_name) // for example
                {
                    $directory = $file_uploader->getTargetDirectory();
                    $full_path = $directory.'/'.$file_name;
                    // Do what you want with the full path file...
                    // Why not read the content or parse it !!!
                }
                else
                {
                    // Oups, an error occured !!!
                }
                return $this->redirectToRoute('app_affiche');
            }
        }
        return $this->render('gedcom_import/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/affiche', name : 'app_affiche')]
public function afficherGedcom(){

        $monFichier = ('uploads/charlie.txt');
        $buffer=[];
        $nameLines= [];
        if (file_exists($monFichier)) {
            $handle = fopen($monFichier, 'r');

            if ($handle) {
                while (!feof($handle)) {
                    $buffer[] = fgets($handle);
                   // var_dump($buffer);
                }
                foreach ($buffer as $line => $test){
                    if(strpos($test, 'NAME')){
                        $nameLines[] = $test;
                    }

                }
                fclose($handle);
                //return $this->render('gedcom_import/affiche.html.twig' , array ('liste' => $buffer));
            }else{
            $buffer[]="pas de liste";
            }

        }
        else{
            $buffer[]="fichier non trouvé";
            $this->redirectToRoute('app_import');
        }

            return $this->render('gedcom_import/affiche.html.twig' , ['nameLines' => $nameLines]);
        }
}