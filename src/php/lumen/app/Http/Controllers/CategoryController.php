<?php

namespace App\Http\Controllers;


use App\Entities\Category;
use App\Transformers\CategoryTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{

    private $entityManager;
    private $categoryTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        CategoryTransformer $categoryTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->categoryTransformer = $categoryTransformer;
    }

    /**
     * @return array
     */
    public function index()
    {
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findAll();

        return $this->categoryTransformer->transformAll($categories);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        try {

            $category = new Category($request->get('category'));
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            return response()->json(
                [
                    'ok' => true,
                    'data' => $this->categoryTransformer->transform($category)
                ],
                201
            );

        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }
    }
}