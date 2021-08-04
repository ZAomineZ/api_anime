<?php

namespace App\Controller;

use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CharacterBySlug extends AbstractController
{
    /**
     * @var CharacterRepository
     */
    private CharacterRepository $characterRepository;

    /**
     * CharacterBySlug constructor.
     * @param CharacterRepository $characterRepository
     */
    public function __construct(CharacterRepository $characterRepository)
    {
        $this->characterRepository = $characterRepository;
    }

    /**
     * @param string $slug
     * @return object[]
     */
    public function __invoke(string $slug): array
    {
        $character = $this->characterRepository->findBy(['slug' => $slug]);
        if (!$character) {
            throw $this->createNotFoundException('No character found for this slug');
        }
        return $character;
    }
}
