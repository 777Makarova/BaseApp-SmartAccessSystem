<?php

namespace App\Entity\Code;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Controller\Code\ActionConfirmCode;
use App\Controller\Code\ActionResendRegisterCode;
use App\Controller\Code\ActionResetPassword;
use Symfony\Component\Serializer\Annotation\Groups;

#[ApiResource(
    description: "Подтверждение пользователей",
    collectionOperations: [
        "confirm"=> [
            "path"=> "/confirm-code",
            "method"=> "post",
            "controller"=> ActionConfirmCode::class,
        ],
        "password_reset"=> [
            "path"=> "/password/reset",
            "method"=> "post",
            "controller"=> ActionResetPassword::class,
        ],
        "resend_activation_code"=> [
              "path"=> "/resend-activation-code",
              "method"=> "POST",
              "controller"=> ActionResendRegisterCode::class,
        ]
    ],
    itemOperations: [],
    shortName: "ConfirmCode",
    attributes: ["denormalization_context"=>["groups"=>["CheckCode"]]],
    mercure: false
)]
class ConfirmCodeRequest
{
    public int $id;

    #[Groups(["CheckCode"])]
    public string $code;

    #[Groups(["CheckCode"])]
    public ?string $phone;

    #[Groups(["CheckCode"])]
    public ?string $email;

    #[Groups(["CheckCode"])]
    public string $newPassword;
}
