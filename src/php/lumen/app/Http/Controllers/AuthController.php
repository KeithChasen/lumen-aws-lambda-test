<?php

namespace App\Http\Controllers;

use App\Entities\User;
use App\Transformers\UserTransformer;
use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Http\Request;
use Auth;

class AuthController extends Controller
{

    private $entityManager;
    private $userTransformer;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserTransformer $userTransformer
    ) {
        $this->entityManager = $entityManager;
        $this->userTransformer = $userTransformer;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {

        $email = $request->get('email');
        $password = $request->get('password');

        $userIsNew = $this->checkUserByEmail($email);

        if($userIsNew) {
            try {

                $hashedPassword = app('hash')->make($password);
                $user = new User($email, $hashedPassword);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $credentials = [
                    'email' => $email,
                    'password' => $password
                ];

                $token = Auth::attempt($credentials);

                if (!$token) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
                return $this->respondWithToken($token);

            } catch (\Exception $e) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }

        return response()->json(['error' => 'User is already registered'], 401);
    }

    public function login(Request $request)
    {
        $token = Auth::attempt([
            'email' => $request->get('email'),
            'password' => $request->get('password')
        ]);
        if (! $token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        $user = Auth::user();
        return response()->json([
            'access_token' => $token,
            'user' => $this->userTransformer->transform($user),
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 60
        ]);
    }

    /**
     * @param $email
     * @return bool
     */
    protected function checkUserByEmail($email)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                'email' => $email
            ]);

        if ($user) {
            return false;
        }

        return true;
    }

}