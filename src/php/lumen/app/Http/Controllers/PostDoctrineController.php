<?php

namespace App\Http\Controllers;

use App\Entities\Post;
use App\Transformers\PostTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;

class PostDoctrineController extends Controller
{
    private $entityManager;
    private $postTransformer;

    public function __construct(EntityManagerInterface $entityManager, PostTransformer $postTransformer)
    {
        $this->entityManager = $entityManager;
        $this->postTransformer = $postTransformer;
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
            $post = new Post($request->get('title'));

            $this->entityManager->persist($post);
            $this->entityManager->flush();

            return response()->json(['ok' => true, 'data' => $post], 201);
        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {

    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request) {

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id) {

    }

}