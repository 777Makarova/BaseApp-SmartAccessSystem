<?php

namespace App\Controller\TokenJWT;


use App\Service\TokenJWT\CheckJWTService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJWTController extends AbstractController
{

    public function __invoke(Request $request, CheckJWTService $parseJWT):Response
    {
        $JWT = $request->get('JWT');
        $JWT = explode(', ',$JWT);

        $data= $parseJWT->parseJWT($JWT);

        $response = new Response();
        $response->setContent(implode(PHP_EOL, $data));

        return $response;
    }
}