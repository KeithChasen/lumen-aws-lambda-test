<?php

namespace App\Http\Controllers;

use App\Entities\Category;
use App\Entities\Post;
use App\Transformers\CategoryTransformer;
use App\Transformers\PostTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Auth;

class PostDoctrineController extends Controller
{
    private $entityManager;
    private $postTransformer;

    private $categoryTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        PostTransformer $postTransformer

        ,CategoryTransformer $categoryTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->postTransformer = $postTransformer;
        $this->categoryTransformer = $categoryTransformer;
    }

    /**
     * @return array|\Illuminate\Http\JsonResponse
     */
    public function index() {

        $posts = $this->entityManager
            ->getRepository(Post::class)
            ->findAll();

        return $this->postTransformer->transformAll($posts);

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