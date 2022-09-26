<?php

namespace App\Controller\Media;

use App\Entity\Character;
use App\Repository\CharacterRepository;
use DateTime;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Request;

final class CreateMediaCharacter extends AbstractController
{
    /**
     * @var CharacterRepository
     */
    private CharacterRepository $characterRepository;

    /**
     * CreateMediaCharacter constructor.
     * @param CharacterRepository $characterRepository
     */
    public function __construct(CharacterRepository $characterRepository)
    {
        $this->characterRepository = $characterRepository;
    }

    /**
     * @param Request $request
     * @param string $id
     * @return array
     */
    #[ArrayShape(['id' => "int|null", 'fileUrl' => "null|string"])] public function __invoke(string $id, Request $request): array
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestException('The field "file" is required');
        }

        $character = $this->characterRepository->find($id);
        if (!$character) {
            throw new BadRequestException('This character don\'t exist');
        }

        $character->setFile($uploadedFile);
        $character->setCreatedAt(new DateTime());
        return [
            'id' => $character->getId(),
            'fileUrl' => $character->getFileUrl()
        ];
    }
}
