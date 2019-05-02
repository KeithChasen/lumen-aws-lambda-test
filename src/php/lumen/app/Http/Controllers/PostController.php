<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        return response()->json(Post::all());
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request) {
        try {
            $post = Post::create($request->all());
            return response()->json(
                [
                    'ok' => true,
                    'data' => $post
                ],
                201
            );
        } catch (\Exception $e) {
            return response()->json(
                [
                    'ok' => false,
                    'error' => $e->getMessage()
                ],
                500
            );
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id) {
        try {
            return response()->json(
                [
                    Post::findOrFail($id),
                    'ok' => true
                ]
            );
        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }
    }

    /**
     * @param $id
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($id, Request $request) {
        try {
            $post = Post::findOrFail($id);
            $post->fill($request->all())->save();

            return response()->json(
                ['ok' => true], 201
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
            $post = Post::findOrFail($id);
            $post->delete();

            return response()->json(
                ['ok' => true], 200
            );
        } catch (\Exception $e) {
            return response()->json(['ok' => false], 500);
        }
    }
}