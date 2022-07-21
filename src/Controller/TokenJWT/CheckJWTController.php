<?php

namespace App\Controller\TokenJWT;


use App\Service\TokenJWT\CheckJWTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;


class CheckJWTController extends AbstractController
{

    public function __invoke(Request $request, CheckJWTService $ChekJWTService): JsonResponse
    {
        $JWT = $request->get('JWT');
        $JWT = explode(', ',$JWT);

        $data= $ChekJWTService->parseJWT($JWT);

         $ChekJWTService->mercurePublisher($data);


        return new JsonResponse (['isTokenValid'=>  $data]);
    }



}