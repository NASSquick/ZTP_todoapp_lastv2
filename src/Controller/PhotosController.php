<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov.
 *
 * For license information, see the LICENSE file.
 */

namespace App\Controller;

use App\Entity\Photo;
use App\Form\PhotosType;
// use App\Service\PhotosService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Service\PhotosServiceInterface;

/**
 * Controller responsible for managing photos.
 */
#[Route('/Photos')]
class PhotosController extends AbstractController
{
    private PhotosServiceInterface $photosService;

    /**
     * Constructor.
     *
     * @param PhotosService $photosService The photos service
     */
    public function __construct(PhotosServiceInterface $photosService)
    {
        $this->photosService = $photosService;
    }

    /**
     * List paginated photos.
     *
     * @param Request $request The current HTTP request
     *
     * @return Response The rendered response containing the paginated list of photos
     */
    #[Route('/', name: 'Photos_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $pagination = $this->photosService->createPaginatedList($request->query->getInt('page', 1));

        return $this->render('Photos/index.html.twig', ['pagination' => $pagination]);
    }

    /**
     * Show a single photo with its comments.
     *
     * @param int $id The photo identifier
     *
     * @return Response The rendered response containing the photo and its comments, or a redirect if not found
     */
    #[Route('/{id}', name: 'Photos_show', methods: ['GET'], requirements: ['id' => '[1-9]\d*'])]
    public function show(int $id): Response
    {
        $data = $this->photosService->getOneWithComments($id);

        if (!$data) {
            $this->addFlash('warning', 'Photo not found.');

            return $this->redirectToRoute('Photos_index');
        }

        return $this->render('Photos/show.html.twig', [
            'photo'    => $data['photo'],
            'comments' => $data['comments'],
        ]);
    }

    /**
     * Create a new photo.
     *
     * @param Request $request The current HTTP request
     *
     * @return Response The rendered response containing the create form or redirect after successful creation
     */
    #[Route('/create', name: 'photos_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $photo = new Photo();
        $this->denyAccessUnlessGranted('edit', $photo);

        $form = $this->createForm(PhotosType::class, $photo, ['required' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photosService->save($photo, $form->get('file')->getData());
            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('Photos_index');
        }

        return $this->render('Photos/create.html.twig', ['form' => $form->createView()]);
    }

    /**
     * Edit an existing photo.
     *
     * @param Request $request The current HTTP request
     * @param Photo   $photo   The photo entity to edit
     *
     * @return Response The rendered response containing the edit form or redirect after successful update
     */
    #[Route('/{id}/edit', name: 'Photos_edit', methods: ['GET', 'PUT'], requirements: ['id' => '[1-9]\d*'])]
    public function edit(Request $request, Photo $photo): Response
    {
        $this->denyAccessUnlessGranted('edit', $photo);

        $form = $this->createForm(PhotosType::class, $photo, ['method' => 'PUT', 'required' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photosService->save($photo, $form->get('file')->getData());
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('Photos_index');
        }

        return $this->render('Photos/edit.html.twig', [
            'form'  => $form->createView(),
            'photo' => $photo,
        ]);
    }

    /**
     * Delete a photo.
     *
     * @param Request $request The current HTTP request
     * @param Photo   $photo   The photo entity to delete
     *
     * @return Response The rendered response containing the delete form or redirect after successful deletion
     */
    #[Route('/{id}/delete', name: 'Photos_delete', methods: ['GET', 'DELETE'], requirements: ['id' => '[1-9]\d*'])]
    public function delete(Request $request, Photo $photo): Response
    {
        $this->denyAccessUnlessGranted('delete', $photo);

        $form = $this->createForm(FormType::class, $photo, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->photosService->delete($photo);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('Photos_index');
        }

        return $this->render('Photos/delete.html.twig', [
            'form'  => $form->createView(),
            'photo' => $photo,
        ]);
    }
}
