<?php

namespace App\Service\TokenJWT;

use App\Entity\AccessLog\AccessLog;
use App\Repository\User\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\UnencryptedToken;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Mercure\HubInterface;


class CheckJWTService
{
    public function __construct(private Configuration $configuration, private  UserRepository $userRepository, private  EntityManagerInterface $entityManager, private HubInterface $hub)
    {
    }


    public function parseJWT (array $JWT)
    {
        $data =[];
        $resultArray = [];
        foreach ($JWT as $value) {

            $token = $this->configuration->parser()->parse($value);
            assert($token instanceof UnencryptedToken);

            $claimUserID = $token->claims()->get('user_id');

            $claimRoles = $token->claims()->get('scope');

            $roleByClaim = $this->userRepository->getRolesByUser(intval($claimUserID))[0]['roles'];
            $checkResult = $this->checkJWT(json_encode($claimRoles),json_encode($roleByClaim));
            $resultArray[] = $checkResult;

            $accessLog = new AccessLog();
            $accessLog->user_id_byClaim = intval($claimUserID);
            $accessLog->roles_byClaim=$roleByClaim;
            $accessLog->token=$value;
            $accessLog->result = $checkResult;


            $this->entityManager->persist($accessLog);
            $this->entityManager->flush();


        }
        return $resultArray;
    }


    public function checkJWT(string $actualRole, string $Role): int
    {
        if (strcasecmp($actualRole,$Role)==0)
        {
            return 1;
        } else {
            return 0;
        }
    }

    public function mercurePublisher(array $checkResults)
    {
        foreach ($checkResults as $checkResult){
            if ($checkResult == 0){

                $update = new Update(
                    'https://localhost/JWTChecker',
                    json_encode([
                        'status' => 'Token is invalid',
                        'date_of_check' => date("Y-m-d H:i:s")
                    ]));
            } else {
                $update = new Update(
                    'https://localhost/JWTChecker',
                    json_encode([
                        'status' => 'Token is valid',
                        'date_of_check' => date("Y-m-d H:i:s")
                    ]));

            }

            $this->hub->publish($update);
        }
        return 'ok';
    }



}