<?php

namespace App\Service\User;

use App\Entity\Code\Code;
use App\Entity\User\User;
use App\Service\Code\CodeService;

class ConfirmationService
{
    private CodeService $codeService;

    public function __construct(
        CodeService $codeService
    ) {
        $this->codeService = $codeService;
    }

    public function updateUserConfirmationCheck(User $data, User $previous_data)
    {
//        if ($data->getEmail() !== $previous_data->getEmail()) {
//            $this->startConfirmEmail($data);
//            $data->isEmailConfirmed = false;
//        }

        if ($data->phone !== $previous_data->phone) {
            $this->startConfirmPhone($data);
            $data->isPhoneConfirmed = false;
        }
    }

    private function startConfirmEmail(User $user)
    {
        $code = $this->codeService->createCode($user, Code::TYPE_CONFIRM_EMAIL, Code::SENT_BY_EMAIL);

        $this->codeService->sendCode($code);
    }

    private function startConfirmPhone(User $user)
    {
        $code = $this->codeService->createCode($user, Code::TYPE_CONFIRM_PHONE, Code::SENT_BY_SMS);

        $this->codeService->sendCode($code);
    }
}
