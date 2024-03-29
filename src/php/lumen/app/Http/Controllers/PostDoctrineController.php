<?php

namespace App\Http\Controllers;

use App\Entities\Category;
use App\Entities\Post;
use App\Transformers\PostTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Auth;
use Doctrine\ORM\Tools\Pagination\Paginator;

class PostDoctrineController extends Controller
{
    const DEFAULT_START_PAGINATION = 0;
    const DEFAULT_MAX_PAGINATION = 5;

    const POST_FILTERS = [
      'title'
    ];

    private $entityManager;
    private $postTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        PostTransformer $postTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->postTransformer = $postTransformer;
    }

    protected function checkFilters(Request $request) {
        $filterArray = [];
        foreach ($request->all() as $key => $value) {
            if (in_array($key, self::POST_FILTERS) && !empty($value)) {
                $filterArray[] = " p.{$key} LIKE :{$key}";
            }
        }

        $dql = "SELECT p from App\Entities\Post p " . (
            !empty($filterArray) ?
                ' WHERE ' . implode(' AND ', $filterArray) :
                ''
            );

        $query = $this->entityManager->createQuery($dql);

        foreach ($request->all() as $key => $value) {
            if (in_array($key, self::POST_FILTERS) && !empty($value)) {
                $query->setParameter($key, "%{$value}%");
            }
        }

        return $query;

    }

    /**
     * @param Request $request
     * @return array
     */
    public function index(Request $request) {

        $start = $request->get('start') ?? self::DEFAULT_START_PAGINATION;
        $max = $request->get('max') ?? self::DEFAULT_MAX_PAGINATION;

        $query = $this->checkFilters($request);

        $query = $query->setFirstResult($start)
            ->setMaxResults($max);

        $posts = new Paginator($query);

        $postsArray = [];

        foreach ($posts as $post) {
            $postsArray[] = $this->postTransformer->transform($post);
        }

        return $postsArray;

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {

        try {

            $user = Auth::user();
            $post = new Post($request->get('title'));
            $post->setUser($user);

            //check if there's categories and create relations
            if ($request->get('categories') && is_array($request->get('categories'))) {

                foreach ($request->get('categories') as $categoryId) {

                    $category = $this->entityManager
                        ->getRepository(Category::class)
                        ->findOneBy([
                            'id' => $categoryId
                        ]);

                    if (!is_null($category) && $category instanceof Category) {
                        $post->addCategory($category);
                    }

                }

            }

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return response()->json(
                [
                    'ok' => true,
                    'data' => $this->postTransformer->transform($post)
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }

    }

    /**
     * @param $id
     * @return array
     */
    public function show($id) {
        $post = $this->entityManager
            ->getRepository(Post::class)
            ->findOneBy([
                'id' => $id
            ]);

        return $this->postTransformer->transform($post);
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request) {
        try {
            $post = $this->entityManager
                ->getRepository(Post::class)
                ->findOneBy([
                    'id' => $id
                ]);

            $post->setTitle($request->get('title'));

            $this->entityManager->flush();

            return response()->json(
                [
                    'ok' => true,
                    'data' => $this->postTransformer->transform($post)
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {
        try {
            $post = $this->entityManager
                ->getRepository(Post::class)
                ->findOneBy([
                    'id' => $id
                ]);

            $this->entityManager->remove($post);
            $this->entityManager->flush();

            return response()->json(
                [
                    'ok' => true
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }
    }

}