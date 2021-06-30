<?php
/**
 * UserController
 */

namespace App\Controller;

use App\Form\UserFormType;
use App\Service\UserService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class UserController
 */
class UserController extends AbstractController
{
    /** @var \App\Service\UserService */
    private $userService;

    /**
     * UserController constructor.
     * @param \App\Service\UserService    $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @Route(
     *     "/user/editProfile",
     *     methods={"GET", "POST"},
     *     name="user_edit_profile"
     * )
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     *
     * @IsGranted("ROLE_ADMIN")
     */
    public function editProfile(Request $request)
    {
        $user = $this->getUser();

        $form = $this->createForm(UserFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newPasswordPlain = $form->get('newPassword')->getData();
            $this->userService->save($user, $newPasswordPlain);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('user_edit_profile');
        }

        return $this->render(
            'user/edit_profile.html.twig',
            ['form' => $form->createView()]
        );
    }
}