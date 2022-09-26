<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnimeByAuthor extends AbstractController
{
    /**
     * @var AuthorRepository
     */
    private AuthorRepository $authorRepository;

    /**
     * AnimeByAuthor constructor.
     * @param AuthorRepository $authorRepository
     */
    public function __construct(AuthorRepository $authorRepository)
    {
        $this->authorRepository = $authorRepository;
    }

    /**
     * @param string $author
     * @return array
     */
    public function __invoke(string $author): array
    {
        /** @var Author $author */
        $author = $this->authorRepository->findOneBy(['slug' => $author]);
        return $author->getAnimes()->toArray();
    }
}
