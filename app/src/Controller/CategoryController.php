<?php
/**
 * Category controller.
 */

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Service\RecipeService;
use App\Service\CategoryService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CategoryController.
 *
 * @Route("/category")
 *
 */
class CategoryController extends AbstractController
{

    /**
     * Category service.
     *
     * @var CategoryService
     */
    private CategoryService $categoryService;

    /**
     * CategoryController constructor.
     *
     * @param CategoryService $categoryService Category service
     */
    public function __construct(CategoryService $categoryService)
    {
        $this->categoryService = $categoryService;
    }

    /**
     * Index acton.
     *
     * @param Request $request HTTP Request
     *
     * @return Response HTTP response
     */
    #[Route(
        name: 'category_index',
        methods: 'GET'
    )]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $pagination = $this->categoryService->createPaginatedList($page);

        return $this->render(
            'category/index.html.twig',
            ['pagination' => $pagination]
        );
    }

    /**
     * Show action.
     *
     * @param Category      $category      Category entity
     * @param RecipeService $recipeService Recipe service
     *
     * @return Response HTTP response
     */
    #[Route(
        '/{id}',
        name: 'category_show',
        requirements: ['id' => '[1-9]\d*'],
        methods: 'GET',
    )]
    public function show(Category $category, RecipeService $recipeService): Response
    {
        return $this->render(
            'category/show.html.twig',
            ['category' => $category,
                'recipes' => $recipeService->findBy(['category' => $category]),
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
        name: 'category_create',
        methods: 'GET|POST',
    )]
    #[isGranted('ROLE_ADMIN')]
    public function create(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setCreatedAt(new \DateTime());
            $category->setUpdatedAt(new \DateTime());
            $this->categoryService->save($category);

            $this->addFlash('success', 'message_created_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/create.html.twig',
            ['form' => $form->createView()]
        );
    }

    /**
     * Edit action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/edit', name: 'category_edit', requirements: ['id' => '[1-9]\d*'], methods: 'GET|PUT')]
    #[isGranted('ROLE_ADMIN')]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'PUT']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category->setUpdatedAt(new \DateTime());
            $this->categoryService->save($category);

            $this->addFlash('success', 'message_updated_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/edit.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param Request  $request  HTTP request
     * @param Category $category Category entity
     *
     * @return Response HTTP response
     */
    #[Route('/{id}/delete', name: 'category_delete', requirements: ['id' => '[1-9]\d*'], methods: 'GET|DELETE')]
    #[isGranted('ROLE_ADMIN')]
    public function delete(Request $request, Category $category): Response
    {
        // @codeCoverageIgnoreStart
        if ($category->getRecipes()->count()) {
            $this->addFlash('warning', 'message_category_contain_recipes');

            return $this->redirectToRoute('category_index');
        }
        // @codeCoverageIgnoreEnd
        $form = $this->createForm(CategoryType::class, $category, ['method' => 'DELETE']);
        $form->handleRequest($request);
        // @codeCoverageIgnoreStart
        if ($request->isMethod('DELETE') && !$form->isSubmitted()) {
            $form->submit($request->request->get($form->getName()));
        }
        // @codeCoverageIgnoreEnd

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryService->delete($category);
            $this->addFlash('success', 'message.deleted_successfully');

            return $this->redirectToRoute('category_index');
        }

        return $this->render(
            'category/delete.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
            ]
        );
    }
}
