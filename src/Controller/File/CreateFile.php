<?php

namespace App\Controller\File;

use App\Entity\File\File;
use League\Flysystem\FilesystemException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use App\Service\File\FileService;
use Symfony\Contracts\Translation\TranslatorInterface;

final class CreateFile
{
    public function __construct(
        private FileService $fileService,
        private TranslatorInterface $translator
    ) {}

    /**
     * @throws FilesystemException
     */
    public function __invoke(Request $request): ?File
    {
        if (!$request->files->count()) {
            throw new BadRequestHttpException($this->translator->trans('file.creating.errors.no_file'));
        }
        return $this->fileService->createFile($request->files->get('file'));
    }
}
