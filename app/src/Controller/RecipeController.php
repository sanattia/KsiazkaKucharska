<?php
/**
 * Recipe controller.
 */

namespace App\Controller;

use App\Entity\Recipe;
use App\Entity\Comment;
use App\Form\RecipeType;
use App\Form\CommentType;
use App\Service\RecipeService;
use App\Service\CommentService;
use DateTime;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class RecipeController.
 *
 * @Route("/recipe")
 *
 * @IsGranted("IS_AUTHENTICATED_ANONYMOUSLY")
 */
class RecipeController extends AbstractController
{
    /**
     * Recipe service.
     *
     * @var RecipeService
     */
    private RecipeService $recipeService;

    /**
     * RecipeController constructor.
     *
     * @param RecipeService $recipeService Recipe service
     */
    public function __construct(RecipeService $recipeService)
    {
        $this->recipeService = $recipeService;
    }

    /**
     * Index acton.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'recipe_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->recipeService->getPaginatedList($page);

        return $this->render(
            'recipe/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param Recipe         $recipe         Recipe entity
     * @param CommentService $commentService
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'recipe_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(Recipe $recipe, CommentService $commentService): Response
    {
        return $this->render(
            'recipe/show.html.twig',
            [
                'recipe' => $recipe,
                'comments' => $commentService->findBy(['recipe' => $recipe]),
            ]
        );
    }

    /**
     * Create action.
     *
     * @param Request $request HTTP request
     *
     * @return Response HTTP response
     */
    #[Route(
        '/create',
        name: 'recipe_create',
        methods: 'GET|POST',
    )]
    #[isGranted('ROLE_USER')]
    public function create(Request $request): Response
    {
        $recipe = new Recipe();
        $form = $this->createForm(RecipeType::class, $recipe);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setCreatedAt(new DateTime());
            $recipe->setUpdatedAt(new DateTime());
            $user = $this->getUser();
            $recipe -> setAuthor($user);
            $this->recipeService->save($recipe);
            $this->addFlash('success', 'message_added_successfully');

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'recipe/create.html.twig',
            ['form' => $form->createView(), 'recipe' => $recipe,
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param Request $request HTTP request
     * @param Recipe  $recipe  Recipe entity
     *
     * @return Response HTTP response
     *
     */
    #[Route('/{id}/edit', name: 'recipe_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[isGranted('EDIT', subject: 'recipe')]
    public function edit(Request $request, Recipe $recipe): Response
    {
        $form = $this->createForm(
            RecipeType::class,
            $recipe,
            [
                'method' => 'PUT',
                'action' => $this->generateUrl('recipe_edit', ['id' => $recipe->getId()]),
            ]
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipe->setUpdatedAt(new DateTime());
            $this->recipeService->save($recipe);
            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'recipe/edit.html.twig',
            [
                'form' => $form->createView(),
                'recipe' => $recipe,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request $request HTTP request
     * @param Recipe  $recipe  Recipe entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'recipe_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[isGranted('DELETE', subject: 'recipe')]
    public function delete(Request $request, Recipe $recipe): Response
    {
        $form = $this->createForm(FormType::class, $recipe, ['method' => 'DELETE']);
        $form->handleRequest($request);

        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $this->recipeService->delete($recipe);
            $this->addFlash('success', 'message_deleted_successfully');

            return $this->redirectToRoute('recipe_index');
        }

        return $this->render(
            'recipe/delete.html.twig',
            [
                'form' => $form->createView(),
                'recipe' => $recipe,
            ]
        );
    }

    /**
     * Comment action.
     *
     * @param Request        $request        HTTP request
     * @param Recipe         $recipe         Recipe entity
     * @param CommentService $commentService
     *
     * @return Response HTTP response
     *
     * @throws ORMException
     * @throws OptimisticLockException
     *
     */
    #[Route('/{id}/comment', name: 'recipe_comment', requirements: ['id' => '[1-9]\d*'], methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function comment(Request $request, Recipe $recipe, CommentService $commentService): Response
    {
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setRecipe($recipe);
            $comment->setAuthor($this->getUser());
            $comment->setCreatedAt(new DateTime());
            $comment->setUpdatedAt(new DateTime());
            $commentService->save($comment);

            $this->addFlash('success', 'message_added_successfully');

            return $this->redirectToRoute('recipe_show', ['id' => $recipe->getId()]);
        }

        return $this->render(
            'recipe/comment.html.twig',
            [
                'form' => $form->createView(),
                'recipe' => $recipe,
                'comment' => $comment,
            ]
        );
    }
}
