<?php

namespace App\Controller;

use App\Entity\Doctorants;
use App\Entity\ValidatedDoctorants;
use App\Entity\Publication;

use App\Form\DoctorantsType;
use App\Repository\DoctorantsRepository;
use App\Repository\PublicationRepository;
use App\Repository\ValidatedDoctorantsRepository;
use App\Repository\PersonnelRepository;
use App\Repository\StructRechRepository;
use Doctrine\ORM\EntityManagerInterface;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Psr\Log\LoggerInterface; 



class DoctorantsController extends AbstractController
{
    /**
     * Route to add a new doctorant.
     * Handles the form submission and persists the new doctorant to the database.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
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

    /**
     * Route to list all doctorants.
     * Fetches and displays all doctorants, personnel, and research structures.
     *
     * @param DoctorantsRepository $doctorantsRepository
     * @param PersonnelRepository $personnelRepository
     * @param StructRechRepository $structRechRepository
     * @return Response
     */
    #[Route('/list-doctorants', name: 'list_doctorants')]
    public function listDoctorants(
        DoctorantsRepository $doctorantsRepository,
        PersonnelRepository $personnelRepository,
        StructRechRepository $structRechRepository,
        Request $request
    ): Response {
        $dateFilter = $request->query->get('dateEnvoi', null); // Get the filter from the query string
    
        // Fetch filtered doctorants
        $doctorants = $doctorantsRepository->findByDateEnvoi($dateFilter);
    
        $personnels = $personnelRepository->findAll();
        $structures = $structRechRepository->findAll();
    
        return $this->render('doctorants/list_doctorants.html.twig', [
            'doctorants' => $doctorants,
            'personnels' => $personnels,
            'structures' => $structures,
            'dateFilter' => $dateFilter, // Pass the current filter to the view
        ]);
    }
    

    /**
     * Route to edit an existing doctorant.
     * Fetches the doctorant by ID, handles the form submission, and updates the doctorant in the database.
     *
     * @param int $id
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @param DoctorantsRepository $doctorantsRepository
     * @return Response
     */
    #[Route('/edit-doctorant/{id}', name: 'edit_doctorant')]
    public function editDoctorant(
        int $id,
        Request $request,
        EntityManagerInterface $entityManager,
        DoctorantsRepository $doctorantsRepository
    ): Response {
        $doctorant = $doctorantsRepository->find($id);
    
        if (!$doctorant) {
            $this->addFlash('error', 'Doctorant non trouvé.');
            return $this->redirectToRoute('list_doctorants');
        }
    
        $form = $this->createForm(DoctorantsType::class, $doctorant);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (!$entityManager->contains($doctorant)) {
                    $doctorant = $entityManager->merge($doctorant);
                }
    
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

    /**
     * Route to delete a doctorant.
     * Fetches the doctorant by ID and removes it from the database.
     *
     * @param int $id
     * @param EntityManagerInterface $entityManager
     * @param DoctorantsRepository $doctorantsRepository
     * @return Response
     */
    #[Route('/delete-doctorant/{id}', name: 'delete_doctorant')]
    public function deleteDoctorant(
        int $id,
        EntityManagerInterface $entityManager,
        DoctorantsRepository $doctorantsRepository
    ): Response {
        try {
            $doctorant = $doctorantsRepository->find($id);
    
            if (!$doctorant) {
                $this->addFlash('error', 'Doctorant non trouvé.');
                return $this->redirectToRoute('list_doctorants');
            }
    
            $doctorant = $entityManager->merge($doctorant);
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

    /**
     * Route to import doctorants from an Excel file.
     * Handles the file upload, processes the data, and persists new doctorants to the database.
     *
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
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
                            $doctorant->setDateEnvoi($row[87] ?? '');
    
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

    /**
     * Route to display statistics about doctorants.
     * Fetches and calculates various statistics such as gender distribution, nationality distribution, etc.
     *
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/doctorants-statistiques', name: 'doctorants_statistiques')]
    public function statistiques(EntityManagerInterface $entityManager): Response
    {
        $totalDoctorants = $entityManager->getRepository(Doctorants::class)->count([]);
        $validatedDoctorants = $entityManager->getRepository(ValidatedDoctorants::class)->count([]);
        $nonValidatedDoctorants = $totalDoctorants - $validatedDoctorants;
    
        $genderDistribution = [
            'MASCULIN' => $entityManager->getRepository(Doctorants::class)->count(['sexe' => 'MASCULIN']),
            'FEMININ' => $entityManager->getRepository(Doctorants::class)->count(['sexe' => 'FEMININ']),
        ];
    
        $nationalityDistribution = $entityManager->createQueryBuilder()
            ->select('d.nationalite AS nationality, COUNT(d.id) AS count')
            ->from(Doctorants::class, 'd')
            ->groupBy('d.nationalite')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    
        $nationalityLabels = array_column($nationalityDistribution, 'nationality');
        $nationalityCounts = array_column($nationalityDistribution, 'count');
    
        $bacMentions = $entityManager->createQueryBuilder()
            ->select('d.mentionBac AS mention, COUNT(d.id) AS count')
            ->from(Doctorants::class, 'd')
            ->groupBy('d.mentionBac')
            ->orderBy('count', 'DESC')
            ->getQuery()
            ->getResult();
    
        $bacMentionLabels = array_column($bacMentions, 'mention');
        $bacMentionCounts = array_column($bacMentions, 'count');
    
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
    
        $data = [
            'totalDoctorants' => $totalDoctorants,
            'validatedDoctorants' => $validatedDoctorants,
            'nonValidatedDoctorants' => $nonValidatedDoctorants,
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

    /**
     * Route to view details of a specific doctorant.
     * Fetches and displays the doctorant's details along with their validation history.
     *
     * @param int $id
     * @param DoctorantsRepository $doctorantsRepository
     * @param ValidatedDoctorantsRepository $validatedDoctorantsRepository
     * @return Response
     */
    #[Route('/view-doctorant/{id}', name: 'view_doctorant')]
    public function viewDoctorant(
        int $id,
        DoctorantsRepository $doctorantsRepository,
        ValidatedDoctorantsRepository $validatedDoctorantsRepository
    ): Response {
        $doctorant = $doctorantsRepository->find($id);
    
        if (!$doctorant) {
            throw $this->createNotFoundException('Doctorant introuvable.');
        }
    
        $validatedDoctorants = $validatedDoctorantsRepository->findBy(['doctorant' => $doctorant]);
    
        return $this->render('doctorants/view_doctorant.html.twig', [
            'doctorant' => $doctorant,
            'validatedDoctorants' => $validatedDoctorants,
        ]);
    }

    /**
     * Route to validate a doctorant.
     * Handles the validation of a doctorant by associating them with personnel and a research structure.
     *
     * @param Request $request
     * @param DoctorantsRepository $doctorantsRepository
     * @param ValidatedDoctorantsRepository $validatedDoctorantsRepository
     * @param PersonnelRepository $personnelRepository
     * @param StructRechRepository $structRechRepository
     * @param EntityManagerInterface $entityManager
     * @param int $id
     * @return Response
     */
    #[Route('/validate-doctorant/{id}', name: 'validate_doctorant', methods: ['POST'])]
    public function validateDoctorant(
        Request $request,
        DoctorantsRepository $doctorantsRepository,
        ValidatedDoctorantsRepository $validatedDoctorantsRepository,
        PersonnelRepository $personnelRepository,
        StructRechRepository $structRechRepository,
        EntityManagerInterface $entityManager,
        int $id
    ): Response {
        $doctorant = $doctorantsRepository->find($id);
    
        if (!$doctorant) {
            $this->addFlash('error', 'Doctorant introuvable.');
            return $this->redirectToRoute('list_doctorants');
        }
    
        $existingValidation = $validatedDoctorantsRepository->findOneBy(['doctorant' => $doctorant]);
        if ($existingValidation) {
            $this->addFlash(
                'warning',
                sprintf(
                    'Le doctorant <a href="/view-doctorant/%d" class="flash-link">%s %s</a> est déjà validé par %s %s dans la structure %s.',
                    $doctorant->getId(),
                    $doctorant->getNom(),
                    $doctorant->getPrenom(),
                    $existingValidation->getPersonnel()->getNom(),
                    $existingValidation->getPersonnel()->getPrenom(),
                    $existingValidation->getStructure()->getLibelleStructure()
                )
            );
            return $this->redirectToRoute('list_doctorants');
        }
    
        $personnelId = $request->request->get('personnel_id');
        $structureId = $request->request->get('structure_id');
        $personnel = $personnelRepository->find($personnelId);
        $structure = $structRechRepository->find($structureId);
    
        if (!$personnel || !$structure) {
            $this->addFlash('error', 'Personnel ou structure introuvable.');
            return $this->redirectToRoute('list_doctorants');
        }
    
        try {
            $validatedDoctorant = new ValidatedDoctorants();
            $validatedDoctorant->setDoctorant($doctorant)
                ->setPersonnel($personnel)
                ->setStructure($structure)
                ->setNom($doctorant->getNom())
                ->setPrenom($doctorant->getPrenom())
                ->setCin($doctorant->getCin())
                ->setCne($doctorant->getCne())
                ->setChoix($doctorant->getChoix())
                ->setSujet($doctorant->getSujet());
    
            $entityManager->persist($validatedDoctorant);
            $entityManager->flush();
    
            $this->addFlash(
                'success',
                sprintf(
                    'Doctorant <a href="/view-doctorant/%d" class="flash-link">%s %s</a> validé avec succès par %s %s dans la structure %s.',
                    $doctorant->getId(),
                    $doctorant->getNom(),
                    $doctorant->getPrenom(),
                    $personnel->getNom(),
                    $personnel->getPrenom(),
                    $structure->getLibelleStructure()
                )
            );
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors de la validation: ' . $e->getMessage());
        }
    
        return $this->redirectToRoute('list_doctorants');
    }

    /**
     * Route to list all validated doctorants.
     * Fetches and displays all doctorants that have been validated.
     *
     * @param ValidatedDoctorantsRepository $validatedDoctorantsRepository
     * @return Response
     */
    #[Route('/list-validated-doctorants', name: 'list_validated_doctorants')]
    public function listValidatedDoctorants(ValidatedDoctorantsRepository $validatedDoctorantsRepository): Response {
        try {
            $validatedDoctorants = $validatedDoctorantsRepository->findAll();

            return $this->render('doctorants/list_validated_doctorants.html.twig', [
                'validatedDoctorants' => $validatedDoctorants,
            ]);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Une erreur est survenue lors du chargement des doctorants validés: ' . $e->getMessage());
            return $this->redirectToRoute('list_doctorants');
        }
    }

    /**
     * Route to validate multiple doctorants at once.
     * Handles the validation of multiple doctorants by associating them with personnel and a research structure.
     *
     * @param Request $request
     * @param DoctorantsRepository $doctorantsRepository
     * @param ValidatedDoctorantsRepository $validatedDoctorantsRepository
     * @param PersonnelRepository $personnelRepository
     * @param StructRechRepository $structRechRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    #[Route('/validate-multiple-doctorants', name: 'validate_multiple_doctorants', methods: ['POST'])]
    public function validateMultipleDoctorants(
        Request $request,
        DoctorantsRepository $doctorantsRepository,
        ValidatedDoctorantsRepository $validatedDoctorantsRepository,
        PersonnelRepository $personnelRepository,
        StructRechRepository $structRechRepository,
        EntityManagerInterface $entityManager
    ): Response {
        try {
            $doctorantIds = $request->request->all('doctorant_ids');
            $personnelId = $request->request->get('personnel_id');
            $structureId = $request->request->get('structure_id');
    
            if (empty($doctorantIds)) {
                throw new \InvalidArgumentException('Aucun doctorant sélectionné.');
            }
    
            $personnel = $personnelRepository->find($personnelId);
            $structure = $structRechRepository->find($structureId);
    
            if (!$personnel || !$structure) {
                throw new \InvalidArgumentException('Personnel ou structure introuvable.');
            }
    
            $validatedCount = 0;
            $skippedDoctorants = [];
            $validatedDoctorants = [];
    
            foreach ($doctorantIds as $id) {
                $doctorant = $doctorantsRepository->find($id);
    
                if (!$doctorant) {
                    continue;
                }
    
                $existingValidation = $validatedDoctorantsRepository->findOneBy(['doctorant' => $doctorant]);
                if ($existingValidation) {
                    $skippedDoctorants[] = [
                        'id' => $doctorant->getId(),
                        'name' => $doctorant->getNom() . ' ' . $doctorant->getPrenom()
                    ];
                    continue;
                }
    
                $validatedDoctorant = new ValidatedDoctorants();
                $validatedDoctorant->setDoctorant($doctorant)
                    ->setPersonnel($personnel)
                    ->setStructure($structure)
                    ->setNom($doctorant->getNom())
                    ->setPrenom($doctorant->getPrenom())
                    ->setCin($doctorant->getCin())
                    ->setCne($doctorant->getCne())
                    ->setChoix($doctorant->getChoix())
                    ->setSujet($doctorant->getSujet());
    
                $entityManager->persist($validatedDoctorant);
    
                $validatedDoctorants[] = sprintf(
                    '<a href="/view-doctorant/%d" class="flash-link">%s</a>',
                    $doctorant->getId(),
                    $doctorant->getNom() . ' ' . $doctorant->getPrenom()
                );
    
                $validatedCount++;
            }
    
            $entityManager->flush();
    
            if ($validatedCount > 0) {
                $this->addFlash(
                    'success',
                    sprintf(
                        '%d doctorant(s) validé(s) avec succès par %s %s dans la structure %s: %s.',
                        $validatedCount,
                        $personnel->getNom(),
                        $personnel->getPrenom(),
                        $structure->getLibelleStructure(),
                        implode(', ', $validatedDoctorants)
                    )
                );
            }
    
            if (!empty($skippedDoctorants)) {
                $skippedLinks = array_map(function ($d) {
                    return sprintf('<a href="/view-doctorant/%d" class="flash-link">%s</a>', $d['id'], $d['name']);
                }, $skippedDoctorants);
    
                $this->addFlash(
                    'warning',
                    sprintf(
                        '%d doctorant(s) déjà validé(s) ont été ignorés: %s.',
                        count($skippedDoctorants),
                        implode(', ', $skippedLinks)
                    )
                );
            }
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur: ' . $e->getMessage());
        }
    
        return $this->redirectToRoute('list_doctorants');
    }












































    #[Route('/import-scopus-ids', name: 'import_scopus_ids', methods: ['GET', 'POST'])]
    public function importScopusIds(
        Request $request,
        EntityManagerInterface $entityManager,
        PersonnelRepository $personnelRepository,
        HttpClientInterface $client,
        LoggerInterface $logger
    ): Response {
        if ($request->isMethod('POST')) {
            $file = $request->files->get('excel_file');
            if ($file) {
                try {
                    $spreadsheet = IOFactory::load($file->getPathname());
                    $worksheet = $spreadsheet->getActiveSheet();
                    $rows = $worksheet->toArray();
                    array_shift($rows); // Remove header row
    
                    $processed = 0;
                    $skipped = 0;
                    $errors = [];
                    $batchSize = 5;
                    $apiKey = 'a0ff4e7a41d6e1c8a8c1170993e29668';
    
                    foreach (array_chunk($rows, $batchSize) as $batch) {
                        foreach ($batch as $row) {
                            try {
                                $personnelId = (int)trim($row[0] ?? null);
                                $scopusId = trim($row[4] ?? null);
    
                                if (!$personnelId || !$scopusId) {
                                    $skipped++;
                                    continue;
                                }
    
                                $personnel = $personnelRepository->find($personnelId);
                                if (!$personnel) {
                                    $errors[] = "Personnel ID $personnelId not found.";
                                    $skipped++;
                                    continue;
                                }
    
                                // Set the Scopus ID in the Personnel table
                                $personnel->setScopusId($scopusId);
                                $entityManager->persist($personnel);
    
                                // Fetch publications from the first URL
                                $url = "https://api.elsevier.com/content/search/scopus?query=au-id($scopusId)";
                                $response = $client->request('GET', $url, [
                                    'headers' => [
                                        'X-ELS-APIKey' => $apiKey,
                                        'Accept' => 'application/json',
                                    ],
                                ]);
    
                                $data = $response->toArray();
                                $entries = $data['search-results']['entry'] ?? [];
    
                                foreach ($entries as $entry) {
                                    $pubScopusId = str_replace('SCOPUS_ID:', '', $entry['dc:identifier']);
                                    $pubDetailsUrl = "https://api.elsevier.com/content/abstract/scopus_id/$pubScopusId";
    
                                    try {
                                        // Fetch publication details from the second URL
                                        $pubResponse = $client->request('GET', $pubDetailsUrl, [
                                            'headers' => [
                                                'X-ELS-APIKey' => $apiKey,
                                                'Accept' => 'application/json',
                                            ],
                                        ]);
    
                                        $pubData = $pubResponse->toArray();
                                        $abstracts = $pubData['abstracts-retrieval-response'];
    
                                        $publication = new Publication();
                                        $publication->setPersonnel($personnel);
                                        $publication->setPersonnelFullName($personnel->getNom() . ' ' . $personnel->getPrenom()); // Set personnel full name
                                        $publication->setTitle($abstracts['coredata']['dc:title'] ?? 'N/A');
                                        $publication->setAggregationType($abstracts['coredata']['prism:aggregationType'] ?? 'N/A');
                                        $publication->setPublicationName($abstracts['coredata']['prism:publicationName'] ?? 'N/A');
                                        $publication->setPublisher($abstracts['coredata']['dc:publisher'] ?? 'N/A');
                                        $publication->setPageRange($abstracts['coredata']['prism:pageRange'] ?? 'N/A');
                                        $publication->setCoverDate($abstracts['coredata']['prism:coverDate'] ?? 'N/A');
                                        $publication->setAbstract($abstracts['coredata']['dc:description'] ?? 'N/A');
    
                                        // Author Names
                                        $authors = $abstracts['authors']['author'] ?? [];
                                        $authorNames = [];
                                        $authorIds = [];
                                        foreach ($authors as $author) {
                                            $givenName = $author['ce:given-name'] ?? null;
                                            $surname = $author['ce:surname'] ?? null;
                                            if ($givenName && $surname) {
                                                $authorNames[] = "$givenName $surname";
                                            }
                                            $authorIds[] = $author['@auid'] ?? null;
                                        }
                                        $publication->setAuthorNames($authorNames);
                                        $publication->setAuthorIds(array_filter($authorIds));
    
                                        // Creator (from the first URL response)
                                        $creator = $entry['dc:creator'] ?? 'N/A';
                                        $publication->setCreator($creator);
    
                                        // Organization (from the second URL response)
                                        $organization = 'N/A';
                                        $affiliations = $abstracts['affiliation'] ?? [];
                                        if (is_array($affiliations)) {
                                            foreach ($affiliations as $affiliation) {
                                                if (isset($affiliation['organization'])) {
                                                    // Extract the second organization if available
                                                    $organizations = $affiliation['organization'];
                                                    if (is_array($organizations) && count($organizations) > 1) {
                                                        $organization = $organizations[1]; // Second organization
                                                    } elseif (is_string($organizations)) {
                                                        $organization = $organizations;
                                                    }
                                                    break;
                                                }
                                            }
                                        }
                                        $publication->setOrganization($organization);
    
                                        $entityManager->persist($publication);
                                    } catch (\Exception $e) {
                                        $errors[] = "Erreur publication $pubScopusId: " . $e->getMessage();
                                        $logger->error("Error processing publication $pubScopusId: " . $e->getMessage());
                                    }
                                }
    
                                $processed++;
                            } catch (\Exception $e) {
                                $errors[] = "Error processing row: " . $e->getMessage();
                                $logger->error("Error processing row: " . $e->getMessage());
                            }
                        }
    
                        // Flush in batches
                        $entityManager->flush();
                        $entityManager->clear();
    
                        // Sleep between batches to prevent rate limiting
                        sleep(1);
                    }
    
                    $this->addFlash('success', "$processed records processed, $skipped skipped.");
                    if (!empty($errors)) {
                        $this->addFlash('error', implode('<br>', $errors));
                    }
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Error processing file: ' . $e->getMessage());
                    $logger->error("Error processing file: " . $e->getMessage());
                }
    
                return $this->redirectToRoute('import_scopus_ids');
            }
    
            $this->addFlash('error', 'No file uploaded.');
        }
    
        // Render the form if no POST request is detected
        return $this->render('doctorants/import_scopus_ids.html.twig');
    }


    






/**
 * @Route("/list-personnel", name="personnel_list")
 */
public function listPersonnel(PersonnelRepository $personnelRepository): Response
{
    $personnels = $personnelRepository->findAll();

    return $this->render('personnel/list_personnel.html.twig', [
        'personnels' => $personnels,
    ]);
}
        /**
         * @Route("/personnel/details/{id}", name="view_personnel_details")
         */
        #[Route('/personnel/details/{id}', name: 'view_personnel_details')]
        public function viewPersonnelDetails(
            int $id,
            PersonnelRepository $personnelRepository,
            HttpClientInterface $client
        ): Response {
            $apiKey = 'a0ff4e7a41d6e1c8a8c1170993e29668';
            $personnel = $personnelRepository->find($id);
        
            if (!$personnel || !$personnel->getScopusId()) {
                throw $this->createNotFoundException('Personnel not found or Scopus ID is missing.');
            }
        
            $scopusId = $personnel->getScopusId();
            $publications = [];
        
            try {
                $url1 = "https://api.elsevier.com/content/search/scopus?query=au-id($scopusId)";
                $response1 = $client->request('GET', $url1, [
                    'headers' => [
                        'X-ELS-APIKey' => $apiKey,
                        'Accept' => 'application/json',
                    ],
                ]);
        
                $data1 = $response1->toArray();
                $entries = $data1['search-results']['entry'] ?? [];
        
                foreach ($entries as $entry) {
                    $pubScopusId = str_replace('SCOPUS_ID:', '', $entry['dc:identifier']);
                    $url2 = "https://api.elsevier.com/content/abstract/scopus_id/$pubScopusId";
        
                    try {
                        $response2 = $client->request('GET', $url2, [
                            'headers' => [
                                'X-ELS-APIKey' => $apiKey,
                                'Accept' => 'application/json',
                            ],
                        ]);
        
                        $publicationDetails = $response2->toArray();
                        $publications[] = [
                            'title' => $publicationDetails['abstracts-retrieval-response']['coredata']['dc:title'] ?? 'N/A',
                            'authors' => $publicationDetails['abstracts-retrieval-response']['authors']['author'] ?? [],
                            'citationCount' => $publicationDetails['abstracts-retrieval-response']['coredata']['citedby-count'] ?? 0,
                            'type' => $publicationDetails['abstracts-retrieval-response']['coredata']['prism:aggregationType'] ?? 'N/A', // Added publication type
                        ];
                    } catch (\Exception $e) {
                        continue; // Skip errors for individual publications
                    }
                }
            } catch (\Exception $e) {
                // Handle API errors
            }
        
            return $this->render('personnel/view_personnel.html.twig', [
                'personnel' => $personnel,
                'publications' => $publications,
            ]);
        }
















        #[Route('/personnel/statistics', name: 'personnel_statistics')]
        public function personnelStatistics(
            PersonnelRepository $personnelRepository,
            PublicationRepository $publicationRepository
        ): Response {
            // Fetch all personnel
            $personnels = $personnelRepository->findAll();
        
            // Initialize statistics arrays
            $totalPublications = 0;
            $mostPublications = ['personnel' => null, 'count' => 0];
            $mostCited = [];
            $publicationTypes = [];
            $publicationTrends = array_fill_keys(range(2000, (int)date('Y')), 0);
        
            // Loop through each personnel to calculate statistics
            foreach ($personnels as $personnel) {
                // Fetch publications for this personnel
                $publications = $publicationRepository->findBy(['personnel' => $personnel]);
        
                // Count publications
                $publicationCount = count($publications);
                $totalPublications += $publicationCount;
        
                // Check if this personnel has the most publications
                if ($publicationCount > $mostPublications['count']) {
                    $mostPublications = ['personnel' => $personnel, 'count' => $publicationCount];
                }
        
                // Calculate total citations for this personnel
                $citationCount = 0;
                foreach ($publications as $publication) {
                    // Dynamically calculate citation count based on publication data
                    $citationCount += $this->calculateCitationCount($publication);
        
                    // Track publication types
                    $type = $publication->getAggregationType() ?? 'Other';
                    $publicationTypes[$type] = ($publicationTypes[$type] ?? 0) + 1;
        
                    // Track publication trends by year
                    $year = $publication->getCoverDate() ? $publication->getCoverDate()->format('Y') : null;
                    if ($year && isset($publicationTrends[$year])) {
                        $publicationTrends[$year]++;
                    }
                }
        
                // Add to most cited list if this personnel has citations
                if ($citationCount > 0) {
                    $mostCited[] = ['personnel' => $personnel, 'citations' => $citationCount];
                }
            }
        
            // Sort most cited by citations (descending)
            usort($mostCited, fn($a, $b) => $b['citations'] <=> $a['citations']);
        
            // Prepare data for the view
            $statistics = [
                'totalPublications' => $totalPublications,
                'mostPublications' => $mostPublications,
                'mostCited' => $mostCited,
                'publicationTypes' => $publicationTypes,
                'publicationTrends' => array_filter($publicationTrends), // Remove empty years
            ];
        
            return $this->render('personnel/statistics.html.twig', [
                'statistics' => $statistics,
            ]);
        }
        
        /**
         * Dynamically calculate citation count for a publication.
         */
        private function calculateCitationCount(Publication $publication): int
        {
            // Example: Use the number of authors as a proxy for citation count
            $authorCount = count($publication->getAuthorNames() ?? []);
        
            // Example: Use a fixed value or another field as a proxy
            // return $publication->getSomeField() ?? 0;
        
            return $authorCount; // Replace with your logic
        }
        
        


    }