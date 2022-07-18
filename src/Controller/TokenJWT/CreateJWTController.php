<?php

namespace App\Controller\TokenJWT;


use App\Entity\TokenJWT\Token;

use App\Repository\User\UserRepository;
use App\Service\TokenJWT\CreateJWTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class CreateJWTController extends AbstractController
{
    public function __invoke(Request $request, CreateJWTService $JWTService, UserRepository $repository): JsonResponse|Token
    {
        $user_id_fromPost = $request->get('user_id');

        print_r($user_id_fromPost);
        $user_id_fromBase = $repository->find($user_id_fromPost)->getId();
        print_r($user_id_fromBase);


        if ($user_id_fromBase===null){
            return new JsonResponse(['Exception'=>'user does not exist']);
        }
        $role = $repository->getRolesByUser($user_id_fromPost)[0]["roles"];

        $token = new Token();
        $token->user_id = $request->get('user_id');
//        $token->dateUpdate = new \DateTime();
//        $token->dateCreate = new \DateTime();
//        print_r($token);
        $token->token = $JWTService->generateJWT($user_id_fromPost, $role);
//        print_r($token);
        return $token;


    }
}