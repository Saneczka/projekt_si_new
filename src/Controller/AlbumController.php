<?php
/**
 * AlbumController
 */

namespace App\Controller;

use App\Entity\Album;
use App\Form\AlbumFormType;
use App\Service\AlbumService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AlbumController
 */
class AlbumController extends AbstractController
{
    /** @var \App\Service\AlbumService */
    private $albumService;

    /**
     * AlbumController constructor.
     * @param \App\Service\AlbumService    $albumService
     */
    public function __construct(AlbumService $albumService)
    {
        $this->albumService = $albumService;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @Route(
     *     "/",
     *     methods={"GET"},
     *     name="album_index",
     * )
     */
    public function index(Request $request): Response
    {
        return $this->render(
            'album/index.html.twig',
            [
                'pagination' => $this->albumService->createPaginatedList([], $request->query->getInt('page', 1)),
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
     *     "/album/create",
     *     methods={"GET", "POST"},
     *     name="album_create",
     * )
     */
    public function create(Request $request): Response
    {
        $album = new Album();
        $form = $this->createForm(AlbumFormType::class, $album);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $album->setUser($this->getUser());
            $this->albumService->save($album);

            $this->addFlash('success', 'message_added_successfully');

            return $this->redirectToRoute('album_index');
        }

        return $this->render(
            'album/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Show action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Album                      $album   Album entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/album/{id}/show",
     *     methods={"GET"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="album_show",
     * )
     */
    public function show(Request $request, Album $album): Response
    {
        return $this->render(
            'album/show.html.twig',
            [
                'album' => $album,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Album                      $album   Album entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/album/{id}/edit",
     *     methods={"GET", "PUT"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="album_edit",
     * )
     */
    public function edit(Request $request, Album $album): Response
    {
        $form = $this->createForm(AlbumFormType::class, $album, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $album->setUser($this->getUser());
            $this->albumService->save($album);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('album_index');
        }

        return $this->render(
            'album/edit.html.twig',
            [
                'form' => $form->createView(),
                'album' => $album,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP request
     * @param \App\Entity\Category                      $album   Album entity
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP response
     *
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @Route(
     *     "/album/{id}/delete",
     *     methods={"GET", "DELETE"},
     *     requirements={"id": "[1-9]\d*"},
     *     name="album_delete",
     * )
     */
    public function delete(Request $request, Album $album): Response
    {
        $form = $this->createForm(FormType::class, $album, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->albumService->delete($album);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('album_index');
        }

        return $this->render(
            'album/delete.html.twig',
            [
                'form' => $form->createView(),
                'album' => $album,
            ]
        );
    }
}
