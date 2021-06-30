<?php
/**
 * ImageController
 */

namespace App\Controller;

use App\Entity\Album;
use App\Entity\Comment;
use App\Entity\Image;
use App\Form\CommentFormType;
use App\Form\ImageFormType;
use App\Service\CommentService;
use App\Service\ImageService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Class ImageController
 */
class ImageController extends AbstractController
{
    /** @var \App\Service\ImageService */
    private $imageService;
    /** @var \App\Service\CommentService */
    private $commentService;

    /**
     * ImageController constructor.
     * @param \App\Service\ImageService    $imageService
     * @param \App\Service\CommentService    $commentService
     */
    public function __construct(ImageService $imageService, CommentService $commentService)
    {
        $this->imageService = $imageService;
        $this->commentService = $commentService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @Route(
     *     "/image",
     *     methods={"GET"},
     *     name="image_index",
     * )
     */
    public function index(Request $request): Response
    {
        return $this->render(
            'image/index.html.twig',
            [
                'pagination' => $this->imageService->createPaginatedList([], $request->query->getInt('page', 1)),
            ]
        );
    }

    /**
     * Create action.
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/image/create",
     *     methods={"GET", "POST"},
     *     name="image_create",
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function create(Request $request): Response
    {
        $image = new Image();
        $form = $this->createForm(ImageFormType::class, $image);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageService->save($image, $form->get('file')->getData());

            $this->addFlash('success', 'message_added_successfully');

            return $this->redirectToRoute('image_index');
        }

        return $this->render(
            'image/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Show action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Image                      $image   Image entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/image/{id}/show",
     *     methods={"GET", "POST"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="image_show",
     * )
     */
    public function show(Request $request, Image $image): Response
    {
        $comment = new Comment();
        $comment->setImage($image);
        $commentForm = $this->createForm(CommentFormType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $this->commentService->save($comment);

            $this->addFlash('success', 'comment_added_successfully');

            return $this->redirectToRoute('image_show', ['id' => $image->getId()]);
        }

        return $this->render(
            'image/show.html.twig',
            [
                'image' => $image,
                'commentForm' => $commentForm->createView()
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Image                      $image   Image entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/image/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="image_edit",
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function edit(Request $request, Image $image): Response
    {
        $form = $this->createForm(ImageFormType::class, $image, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageService->save($image, $form->get('file')->getData());

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('image_index');
        }

        return $this->render(
            'image/edit.html.twig',
            [
                'form' => $form->createView(),
                'image' => $image,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Category                      $image   Image entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/image/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="image_delete",
     * )
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function delete(Request $request, Image $image): Response
    {
        $form = $this->createForm(FormType::class, $image, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->imageService->delete($image);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('image_index');
        }

        return $this->render(
            'image/delete.html.twig',
            [
                'form' => $form->createView(),
                'image' => $image,
            ]
        );
    }
}
