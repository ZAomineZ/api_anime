<?php


namespace App\Controller;


use App\Entity\Character;
use App\Repository\CharacterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class CharacterByGenre extends AbstractController
{
    /**
     * @var CharacterRepository
     */
    private CharacterRepository $characterRepository;

    /**
     * CharacterByGenre constructor.
     * @param CharacterRepository $characterRepository
     */
    public function __construct(CharacterRepository $characterRepository)
    {
        $this->characterRepository = $characterRepository;
    }

    /**
     * @param string $genre
     * @return Character[]
     */
    public function __invoke(string $genre): array
    {
        $character = $this->characterRepository->findBy(['genre' => $genre]);
        if (!$character) {
            throw $this->createNotFoundException('No character found for this slug');
        }
        return $character;
    }
}
