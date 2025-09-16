<?php

/**
 * Controller responsible for managing galleries.
 */

namespace App\Controller;

use App\Entity\Gallery;
use App\Form\GalleriesType;
use App\Service\GalleriesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * GalleriesController handles CRUD operations for galleries.
 */
#[Route('/Galleries')]
class GalleriesController extends AbstractController
{
    private GalleriesService $galleriesService;

    /**
     * GalleriesController constructor.
     *
     * @param GalleriesService $galleriesService Service for handling galleries
     */
    public function __construct(GalleriesService $galleriesService)
    {
        $this->galleriesService = $galleriesService;
    }

    /**
     * List all galleries with pagination.
     *
     * @param Request $request HTTP request
     *
     * @return Response The rendered response containing the paginated galleries list
     */
    #[Route('/', name: 'galleries_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $pagination = $this->galleriesService->createPaginatedList($request->query->getInt('page', 1));

        return $this->render('Galleries/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Display a single gallery with its photos.
     *
     * @param int $id Gallery ID
     *
     * @return Response The rendered response containing the gallery details and its photos
     */
    #[Route('/{id}', name: 'galleries_show', methods: ['GET'], requirements: ['id' => '[1-9]\d*'])]
    public function show(int $id): Response
    {
        $data = $this->galleriesService->getOneWithPhotos($id);

        if (!$data) {
            $this->addFlash('warning', 'Gallery not found.');

            return $this->redirectToRoute('galleries_index');
        }

        return $this->render('Galleries/show.html.twig', [
            'gallery' => $data['gallery'],
            'photos'  => $data['photos'],
        ]);
    }

    /**
     * Create a new gallery.
     *
     * @param Request $request HTTP request
     *
     * @return Response The rendered response containing the create form or redirect after successful creation
     */
    #[Route('/create', name: 'galleries_create', methods: ['GET', 'POST'])]
    public function create(Request $request): Response
    {
        $gallery = new Gallery();
        $this->denyAccessUnlessGranted('create', $gallery);

        $form = $this->createForm(GalleriesType::class, $gallery);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleriesService->save($gallery);
            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('galleries_index');
        }

        return $this->render('Galleries/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edit a gallery.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity to edit
     *
     * @return Response The rendered response containing the edit form or redirect after successful update
     */
    #[Route('/{id}/edit', name: 'galleries_edit', methods: ['GET', 'PUT'], requirements: ['id' => '[1-9]\d*'])]
    public function edit(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted('edit', $gallery);

        $form = $this->createForm(GalleriesType::class, $gallery, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleriesService->save($gallery);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('galleries_index');
        }

        return $this->render('Galleries/edit.html.twig', [
            'form'    => $form->createView(),
            'gallery' => $gallery,
        ]);
    }

    /**
     * Delete a gallery.
     *
     * @param Request $request HTTP request
     * @param Gallery $gallery Gallery entity to delete
     *
     * @return Response The rendered response containing the delete form or redirect after successful deletion
     */
    #[Route('/{id}/delete', name: 'galleries_delete', methods: ['GET', 'DELETE'], requirements: ['id' => '[1-9]\d*'])]
    public function delete(Request $request, Gallery $gallery): Response
    {
        $this->denyAccessUnlessGranted('delete', $gallery);

        $form = $this->createForm(FormType::class, $gallery, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->galleriesService->delete($gallery);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('galleries_index');
        }

        return $this->render('Galleries/delete.html.twig', [
            'form'    => $form->createView(),
            'gallery' => $gallery,
        ]);
    }
}
