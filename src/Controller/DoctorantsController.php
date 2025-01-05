<?php

namespace App\Controller;

use App\Entity\Doctorants;
use App\Form\DoctorantType;
use App\Repository\DoctorantsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\File\UploadedFile;


class DoctorantsController extends AbstractController
{
    #[Route('/add-doctorant', name: 'add_doctorant')]
    public function addDoctorant(Request $request, EntityManagerInterface $entityManager): Response
    {
        $doctorant = new Doctorants();
        $form = $this->createForm(DoctorantType::class, $doctorant);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($doctorant);
            $entityManager->flush();

            $this->addFlash('success', 'Doctorant ajouté avec succès!');
            return $this->redirectToRoute('list_doctorants');
        }

        return $this->render('doctorants/add_doctorant.html.twig', [
            'form' => $form->createView(),
            'edit_mode' => false,
        ]);
    }

    #[Route('/list-doctorants', name: 'list_doctorants')]
    public function listDoctorants(DoctorantsRepository $doctorantsRepository): Response
    {
        $doctorants = $doctorantsRepository->findAll();
        return $this->render('doctorants/list_doctorants.html.twig', [
            'doctorants' => $doctorants,
        ]);
    }

    #[Route('/edit-doctorant/{id}', name: 'edit_doctorant')]
    public function editDoctorant(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        DoctorantsRepository $doctorantsRepository
    ): Response {
        // Get the existing doctorant
        $doctorant = $doctorantsRepository->find($id);
    
        if (!$doctorant) {
            $this->addFlash('error', 'Doctorant non trouvé.');
            return $this->redirectToRoute('list_doctorants');
        }
    
        $form = $this->createForm(DoctorantType::class, $doctorant);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Make sure we're working with the same entity
                $doctorant = $entityManager->merge($doctorant);
                
                // No need for persist() since it's an existing entity
                $entityManager->flush();
                
                $this->addFlash('success', 'Doctorant mis à jour avec succès!');
                return $this->redirectToRoute('list_doctorants');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour.');
            }
        }
    
        return $this->render('doctorants/add_doctorant.html.twig', [
            'form' => $form->createView(),
            'edit_mode' => true,
            'doctorant' => $doctorant,
        ]);
    }    

    #[Route('/delete-doctorant/{id}', name: 'delete_doctorant')]
    public function deleteDoctorant(
        int $id,
        EntityManagerInterface $entityManager,
        DoctorantsRepository $doctorantsRepository
    ): Response {
        $doctorant = $doctorantsRepository->find($id);

        if (!$doctorant) {
            $this->addFlash('error', 'Doctorant non trouvé.');
            return $this->redirectToRoute('list_doctorants');
        }

        $doctorant = $entityManager->merge($doctorant); // Ensure entity is managed
        $entityManager->remove($doctorant);
        $entityManager->flush();

        $this->addFlash('success', 'Doctorant supprimé avec succès!');
        return $this->redirectToRoute('list_doctorants');
    }


    #[Route('/import-doctorants', name: 'import_doctorants')]
    public function importDoctorants(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($request->isMethod('POST')) {
            /** @var UploadedFile $file */
            $file = $request->files->get('excel_file');

            if ($file) {
                try {
                    $spreadsheet = IOFactory::load($file->getPathname());
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();

                    // Remove header row
                    array_shift($rows);

                    $importCount = 0;
                    $errors = [];

                    foreach ($rows as $index => $row) {
                        if (empty(array_filter($row))) {
                            continue; // Skip empty rows
                        }

                        try {
                            $doctorant = new Doctorants();
                            $doctorant->setEmail($row[0] ?? ''); // E-mail Institutionnel
                            $doctorant->setCode($row[1] ?? '');  // Code Apogee
                            $doctorant->setNom($row[2] ?? '');   // Nom
                            $doctorant->setPrenom($row[3] ?? ''); // Prenom

                            $entityManager->persist($doctorant);
                            $importCount++;
                        } catch (\Exception $e) {
                            $errors[] = "Ligne " . ($index + 2) . ": " . $e->getMessage();
                        }
                    }

                    if ($importCount > 0) {
                        $entityManager->flush();
                        $this->addFlash('success', "$importCount doctorants importés avec succès!");
                    }

                    if ($errors) {
                        $this->addFlash('error', "Erreurs lors de l'import: " . implode(', ', $errors));
                    }

                } catch (\Exception $e) {
                    $this->addFlash('error', "Erreur lors de l'import du fichier: " . $e->getMessage());
                }
            } else {
                $this->addFlash('error', 'Aucun fichier n\'a été uploadé.');
            }

            return $this->redirectToRoute('list_doctorants');
        }

        return $this->render('doctorants/import_doctorants.html.twig');
    }
}
