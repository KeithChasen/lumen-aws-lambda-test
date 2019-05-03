<?php

namespace App\Http\Controllers;


use App\Entities\Post;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;

class PostDoctrineController extends Controller
{
    /**
     * @param EntityManagerInterface $entityManager
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(EntityManagerInterface $entityManager) {

        $posts = $entityManager
            ->getRepository(Post::class)
            ->findAll();

        //use JsonSerializable->jsonSerialize() method to transform list of posts
        $transformedPosts = json_encode($posts);

        //transform to JSON
        $posts = json_decode($transformedPosts);

        return response()->json($posts);
    }

    /**
     * @param Request $request
     * @param EntityManagerInterface $entityManager
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, EntityManagerInterface $entityManager) {

        try {
            $post = new Post($request->get('title'));

            $entityManager->persist($post);
            $entityManager->flush();

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