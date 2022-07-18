<?php

namespace App\Service\File;

use App\Entity\File\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use League\Flysystem\FilesystemOperator;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use DateTime;
use Symfony\Contracts\Translation\TranslatorInterface;

class FileService
{
    public function __construct(
        private FilesystemOperator $defaultStorage,
        private EntityManagerInterface $em,
        private TranslatorInterface $translator,
    ) {}

    public function createFile($file)
    {
        $currentDate = new DateTime();
        $currentDate = $currentDate->format('Y/m/d/').bin2hex(random_bytes(4));

        $formats = [
            'png' => 'image',
            'jpeg' => 'image',
            'jpg' => 'image',
            'svg' => 'image',
            'tiff' => 'image',
            'mp4' => 'video',
            'blob' => 'image',
        ];

        $objFile = new File();

        $name = md5(bin2hex(random_bytes(30)));
        $ext = $file->getClientOriginalExtension();
        $fileFormat = strtolower($ext);

        if (!isset($formats[$fileFormat])) {
            throw new BadRequestHttpException($this->translator->trans('file.creating.errors.unsupported_format'));
        }

        $filePath = $currentDate.'/'.$name.'.'.$fileFormat ?? '';
        $objFile->path = $filePath;
        try {
            $this->defaultStorage->write($objFile->path, $file->getContent());
        } catch (\Throwable $e) {
            throw new HttpException(424, $e->getMessage(), $e);
        }

        $this->em->persist($objFile);
        $this->em->flush();

        return $objFile;
    }
}