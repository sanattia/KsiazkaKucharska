<?php
/**
 * Security controller.
 */

namespace App\Controller;

use App\Entity\User;
use App\Form\Type\EditUserType;
use App\Service\UserServiceInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * SecurityController class.
 */
class UserController extends AbstractController
{
    /**
     * User service.
     *
     * @var UserServiceInterface User service
     */
    private UserServiceInterface $userService;

    /**
     * Translator interface.
     *
     * @var TranslatorInterface Translator
     */
    private TranslatorInterface $translator;

    /**
     * Constructor.
     *
     * @param UserServiceInterface $userService User service
     * @param TranslatorInterface  $translator  Translator
     */
    public function __construct(UserServiceInterface $userService, TranslatorInterface $translator)
    {
        $this->userService = $userService;
        $this->translator = $translator;
    }

    /**
     * Change password action.
     *
     * @param Request                     $request            Request
     * @param UserPasswordHasherInterface $userPasswordHasher Hasher
     *
     * @return Response Response
     */
    #[Route(
        '/user',
        name: 'user_edit',
        methods: 'GET|POST'
    )]
    #[IsGranted('ROLE_USER')]
    public function edit(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        /** @var User $user */
        $user = $this->getUser();

        $form = $this->createForm(EditUserType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $password = $userPasswordHasher->hashPassword(
                $user,
                $form->get('password')->getData()
            );

            $this->userService->upgradePassword($user, $password);
            $this->userService->save($user);
            $this->addFlash(
                'success',
                $this->translator->trans('message.edited_successfully')
            );

            return $this->redirectToRoute('user_edit');
        }

        return $this->render(
            'user/edit.html.twig',
            [
                'form' => $form->createView(),
                'user' => $user,
            ]
        );
    }
}
