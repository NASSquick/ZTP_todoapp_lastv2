<?php

/**
 * This file is part of the TODO App project.
 *
 * (c) Hlib Ivanov.
 *
 * For license information, see the LICENSE file.
 */

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentsType;
use App\Service\CommentsService;
use App\Service\PhotosService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Controller responsible for managing comments.
 */
#[Route('/Comments')]
class CommentsController extends AbstractController
{
    private CommentsService $commentsService;
    private PhotosService $photosService;

    /**
     * CommentsController constructor.
     *
     * @param CommentsService $commentsService service for handling comments
     * @param PhotosService   $photosService   service for handling photos
     */
    public function __construct(CommentsService $commentsService, PhotosService $photosService)
    {
        $this->commentsService = $commentsService;
        $this->photosService = $photosService;
    }

    /**
     * List all comments with pagination.
     *
     * @param Request $request the HTTP request
     *
     * @return Response the response object
     */
    #[Route('/', name: 'Comments_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $pagination = $this->commentsService->createPaginatedList($request->query->getInt('page', 1));

        return $this->render('Comments/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Display a single comment.
     *
     * @param Comment $comment the comment entity
     *
     * @return Response the response object
     */
    #[Route('/{id}', name: 'Comments_show', methods: ['GET'], requirements: ['id' => '[1-9]\d*'])]
    public function show(Comment $comment): Response
    {
        return $this->render('Comments/show.html.twig', [
            'comment' => $comment,
        ]);
    }

    /**
     * Create a new comment for a given photo.
     *
     * @param Request $request the HTTP request
     * @param int     $photoId the photo ID
     *
     * @return Response the response object
     */
    #[Route('/create/{photoId}/photo', name: 'Comments_create', methods: ['GET', 'POST'])]
    public function create(Request $request, int $photoId): Response
    {
        $photos = $this->photosService->getOne($photoId);
        if (null === $photos) {
            return $this->redirectToRoute('Comments_index');
        }

        $comment = new Comment();

        $form = $this->createForm(CommentsType::class, $comment, [
            'action' => $this->generateUrl('Comments_create', ['photoId' => $photos->getId()]),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setPhoto($photos);
            $this->commentsService->save($comment);

            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('Comments_index');
        }

        return $this->render('Comments/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Delete a comment.
     *
     * @param Request $request the HTTP request
     * @param Comment $comment the comment entity
     *
     * @return Response the response object
     */
    #[Route('/{id}/delete', name: 'Comments_delete', methods: ['GET', 'DELETE'], requirements: ['id' => '[1-9]\d*'])]
    public function delete(Request $request, Comment $comment): Response
    {
        $this->denyAccessUnlessGranted('delete', $comment);

        $form = $this->createForm(FormType::class, $comment, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commentsService->delete($comment);

            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('Comments_index');
        }

        return $this->render('Comments/delete.html.twig', [
            'form' => $form->createView(),
            'comment' => $comment,
        ]);
    }
}
