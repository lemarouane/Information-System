<?php

namespace App\Controller;

use App\Entity\Doctorants;
use App\Entity\ValidatedDoctorants;

use App\Form\DoctorantsType;
use App\Repository\DoctorantsRepository;
use App\Repository\ValidatedDoctorantsRepository;

use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;

class DoctorantsController extends AbstractController
{
    #[Route('/add-doctorant', name: 'add_doctorant')]
    public function addDoctorant(Request $request, EntityManagerInterface $entityManager): Response
    {
        $doctorant = new Doctorants();
        $form = $this->createForm(DoctorantsType::class, $doctorant);
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
        // Fetch the entity
        $doctorant = $doctorantsRepository->find($id);
    
        if (!$doctorant) {
            $this->addFlash('error', 'Doctorant non trouvé.');
            return $this->redirectToRoute('list_doctorants');
        }
    
        // Create and bind the form
        $form = $this->createForm(DoctorantsType::class, $doctorant);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                // Ensure Doctrine tracks the entity
                if (!$entityManager->contains($doctorant)) {
                    $doctorant = $entityManager->merge($doctorant);
                }
    
                // Flush the changes
                $entityManager->flush();
    
                $this->addFlash('success', 'Doctorant mis à jour avec succès!');
                return $this->redirectToRoute('list_doctorants');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Une erreur est survenue lors de la mise à jour: ' . $e->getMessage());
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
        try {
            // Fetch the entity directly from the database
            $doctorant = $doctorantsRepository->find($id);
    
            if (!$doctorant) {
                $this->addFlash('error', 'Doctorant non trouvé.');
                return $this->redirectToRoute('list_doctorants');
            }
    
            // Explicitly ensure the entity is managed
            $doctorant = $entityManager->merge($doctorant);
    
            // Proceed to remove the entity
            $entityManager->remove($doctorant);
            $entityManager->flush();
    
            $this->addFlash('success', 'Doctorant supprimé avec succès!');
        } catch (\Doctrine\ORM\ORMInvalidArgumentException $e) {
            $this->addFlash('error', 'Erreur Doctrine: ' . $e->getMessage());
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la suppression: ' . $e->getMessage());
        }
    
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
                            $doctorant->setEtablissement($row[2] ?? '');
                            $doctorant->setNom($row[3] ?? '');
                            $doctorant->setPrenom($row[4] ?? '');
                            $doctorant->setNomArabe($row[5] ?? '');
                            $doctorant->setPrenomArabe($row[6] ?? '');
                            $doctorant->setCin($row[7] ?? '');
                            $doctorant->setCne($row[8] ?? '');
                            $doctorant->setDateDeNaissance($row[9] ? new \DateTime($row[9]) : null);
                            $doctorant->setLieuDeNaissance($row[10] ?? '');
                            $doctorant->setProvinceDeNaissance($row[11] ?? '');
                            $doctorant->setPaysNaissance($row[12] ?? '');
                            $doctorant->setNationalite($row[13] ?? '');
                            $doctorant->setSexe($row[14] ?? '');
                            $doctorant->setTelephone($row[15] ?? '');
                            $doctorant->setEmail($row[16] ?? '');
                            $doctorant->setEmailInstitutionnel($row[17] ?? '');
                            $doctorant->setCodePostal($row[18] ?? '');
                            $doctorant->setOrganismeEmployeur($row[19] ?? '');
                            $doctorant->setProfession($row[20] ?? '');
                            $doctorant->setAcademieBac($row[21] ?? '');
                            $doctorant->setProvinceBac($row[22] ?? '');
                            $doctorant->setLyceeBac($row[23] ?? '');
                            $doctorant->setMentionBac($row[24] ?? '');
                            $doctorant->setNoteBac($row[25] !== '' ? (float)$row[25] : null);
                            $doctorant->setDateObtentionBac($row[26] ?? '');
                            $doctorant->setPaysBac($row[27] ?? '');
                            $doctorant->setSecteurBac($row[28] ?? '');
                            $doctorant->setSpecialiteBac($row[29] ?? '');
                            $doctorant->setLicenceEtablissement($row[30] ?? '');
                            $doctorant->setMentionLicence($row[31] ?? '');
                            $doctorant->setDateObtentionLicence($row[32] ?? '');
                            $doctorant->setLicencePays($row[33] ?? '');
                            $doctorant->setLicenceSpecialite($row[34] ?? '');
                            $doctorant->setUniversiteLicence($row[35] ?? '');
                            $doctorant->setNoteLicence($row[36] !== '' ? (float)$row[36] : null);
                            $doctorant->setLicenceSecteur($row[37] ?? '');
                            $doctorant->setMasterEtablissement($row[38] ?? '');
                            $doctorant->setNoteMaster($row[39] !== '' ? (float)$row[39] : null);
                            $doctorant->setMentionMaster($row[40] ?? '');
                            $doctorant->setDateObtentionMaster($row[41] ?? '');
                            $doctorant->setPaysObtentionMaster($row[42] ?? '');
                            $doctorant->setMasterSpecialite($row[43] ?? '');
                            $doctorant->setMasterUniversite($row[44] ?? '');
                            $doctorant->setMasterSecteur($row[45] ?? '');
                            $doctorant->setAutreDiplomeType($row[46] ?? '');
                            $doctorant->setAutreDiplomeEtablissement($row[47] ?? '');
                            $doctorant->setAutreDiplomeMention($row[48] ?? '');
                            $doctorant->setAutreDiplomeDateObtention($row[49] ?? '');
                            $doctorant->setAutreDiplomePays($row[50] ?? '');
                            $doctorant->setAutreDiplomeSpecialite($row[51] ?? '');
                            $doctorant->setAutreDiplomeUniversite($row[52] ?? '');
                            $doctorant->setAutreDiplomeSecteur($row[53] ?? '');
                            $doctorant->setLangue1($row[54] ?? '');
                            $doctorant->setLangue2($row[55] ?? '');
                            $doctorant->setLangue3($row[56] ?? '');
                            $doctorant->setLangue4($row[57] ?? '');
                            $doctorant->setNiveauLangue1($row[58] ?? '');
                            $doctorant->setNiveauLangue2($row[59] ?? '');
                            $doctorant->setNiveauLangue3($row[60] ?? '');
                            $doctorant->setNiveauLangue4($row[61] ?? '');
                            $doctorant->setEtablissement1($row[62] ?? '');
                            $doctorant->setEtablissement2($row[63] ?? '');
                            $doctorant->setEtablissement3($row[64] ?? '');
                            $doctorant->setExperience1($row[65] ?? '');
                            $doctorant->setExperience2($row[66] ?? '');
                            $doctorant->setExperience3($row[67] ?? '');
                            $doctorant->setFonction1($row[68] ?? '');
                            $doctorant->setFonction2($row[69] ?? '');
                            $doctorant->setFonction3($row[70] ?? '');
                            $doctorant->setPeriode1D($row[71] ?? '');
                            $doctorant->setPeriode1F($row[72] ?? '');
                            $doctorant->setPeriode2D($row[73] ?? '');
                            $doctorant->setPeriode2F($row[74] ?? '');
                            $doctorant->setPeriode3D($row[75] ?? '');
                            $doctorant->setPeriode3F($row[76] ?? '');
                            $doctorant->setSecteur1($row[77] ?? '');
                            $doctorant->setSecteur2($row[78] ?? '');
                            $doctorant->setSecteur3($row[79] ?? '');
                            $doctorant->setHandicape($row[80] ?? '');
                            $doctorant->setCed($row[81] ?? '');
                            $doctorant->setFd($row[82] ?? '');
                            $doctorant->setNEtabliss($row[83] ?? '');
                            $doctorant->setEnseignantChercheur($row[84] ?? '');
                            $doctorant->setChoix($row[85] ?? '');
                            $doctorant->setSujet($row[86] ?? '');
    
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
    
    
    #[Route('/doctorants-statistiques', name: 'doctorants_statistiques')]
    public function statistiques(EntityManagerInterface $entityManager): Response
    {
        // Fetch total number of doctorants
        $totalDoctorants = $entityManager->getRepository(Doctorants::class)->count([]);
    
        // Fetch number of validated doctorants
        $validatedDoctorants = $entityManager->getRepository(ValidatedDoctorants::class)->count([]);
    
        // Calculate non-validated doctorants
        $nonValidatedDoctorants = $totalDoctorants - $validatedDoctorants;
    
        // Fetch data for Répartition par Genre
        $genderDistribution = [
            'MASCULIN' => $entityManager->getRepository(Doctorants::class)->count(['sexe' => 'MASCULIN']),
            'FEMININ' => $entityManager->getRepository(Doctorants::class)->count(['sexe' => 'FEMININ']),
        ];
    
        // Fetch data for Répartition par Nationalité
        $nationalityDistribution = $entityManager->createQueryBuilder()
            ->select('d.nationalite AS nationality, COUNT(d.id) AS count')
            ->from(Doctorants::class, 'd')
            ->groupBy('d.nationalite')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    
        $nationalityLabels = array_column($nationalityDistribution, 'nationality');
        $nationalityCounts = array_column($nationalityDistribution, 'count');
    
        // Fetch data for Bac Mentions
        $bacMentions = $entityManager->createQueryBuilder()
            ->select('d.mentionBac AS mention, COUNT(d.id) AS count')
            ->from(Doctorants::class, 'd')
            ->groupBy('d.mentionBac')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    
        $bacMentionLabels = array_column($bacMentions, 'mention');
        $bacMentionCounts = array_column($bacMentions, 'count');
    
        // Fetch average Bac and Master Notes
        $averageBacNote = (float)$entityManager->createQueryBuilder()
            ->select('AVG(d.noteBac)')
            ->from(Doctorants::class, 'd')
            ->getQuery()
            ->getSingleScalarResult();
    
        $averageMasterNote = (float)$entityManager->createQueryBuilder()
            ->select('AVG(d.noteMaster)')
            ->from(Doctorants::class, 'd')
            ->getQuery()
            ->getSingleScalarResult();
    
        // Prepare data for the template
        $data = [
            'totalDoctorants' => $totalDoctorants, // Add total doctorants
            'validatedDoctorants' => $validatedDoctorants, // Add validated doctorants
            'nonValidatedDoctorants' => $nonValidatedDoctorants, // Add non-validated doctorants
            'genderDistribution' => $genderDistribution,
            'nationalityDistribution' => [
                'labels' => $nationalityLabels,
                'values' => $nationalityCounts,
            ],
            'bacMentions' => [
                'labels' => $bacMentionLabels,
                'values' => $bacMentionCounts,
            ],
            'averageBacNote' => $averageBacNote,
            'averageMasterNote' => $averageMasterNote,
        ];
    
        return $this->render('doctorants/statistiques.html.twig', [
            'data' => $data,
        ]);
    }
    
    

    #[Route('/view-doctorant/{id}', name: 'view_doctorant')]
public function viewDoctorant(int $id, DoctorantsRepository $doctorantsRepository): Response
{
    $doctorant = $doctorantsRepository->find($id);

    if (!$doctorant) {
        $this->addFlash('error', 'Doctorant non trouvé.');
        return $this->redirectToRoute('list_doctorants');
    }

    return $this->render('doctorants/view_doctorant.html.twig', [
        'doctorant' => $doctorant,
    ]);
}

















// src/Controller/DoctorantsController.php
#[Route('/validate-doctorant/{id}', name: 'validate_doctorant')]
public function validateDoctorant(
    int $id,
    EntityManagerInterface $entityManager,
    DoctorantsRepository $doctorantsRepository
): Response {
    // Fetch the doctorant from the general table
    $doctorant = $doctorantsRepository->find($id);

    if (!$doctorant) {
        $this->addFlash('error', 'Doctorant non trouvé.');
        return $this->redirectToRoute('list_doctorants');
    }

    // Create a new ValidatedDoctorants entity and populate it with the necessary data
    $validatedDoctorant = new ValidatedDoctorants();
    $validatedDoctorant->setNom($doctorant->getNom());
    $validatedDoctorant->setPrenom($doctorant->getPrenom());
    $validatedDoctorant->setCin($doctorant->getCin());
    $validatedDoctorant->setCne($doctorant->getCne());
    $validatedDoctorant->setChoix($doctorant->getChoix());
    $validatedDoctorant->setSujet($doctorant->getSujet());

    // Persist and flush the new entity
    $entityManager->persist($validatedDoctorant);
    $entityManager->flush();

    $this->addFlash('success', 'Doctorant validé et ajouté à la table des validés.');
    return $this->redirectToRoute('list_doctorants');
}
    






#[Route('/list-validated-doctorants', name: 'list_validated_doctorants')]
public function listValidatedDoctorants(ValidatedDoctorantsRepository $validatedDoctorantsRepository): Response
{
    // Fetch all validated doctorants from the validated_doctorants table
    $validatedDoctorants = $validatedDoctorantsRepository->findAll();

    // Log the fetched data for debugging (optional)
    // $this->logger->info('Fetched validated doctorants:', $validatedDoctorants);

    // Render the Twig template with the validated doctorants data
    return $this->render('doctorants/list_validated_doctorants.html.twig', [
        'validatedDoctorants' => $validatedDoctorants,
    ]);
}

}




