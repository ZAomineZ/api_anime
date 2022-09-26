<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Repository\AnimeRepository;
use App\Repository\TagRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnimeByTag extends AbstractController
{
    /**
     * @var TagRepository
     */
    private TagRepository $tagRepository;

    /**
     * AnimeByTag constructor.
     * @param TagRepository $tagRepository
     */
    public function __construct(TagRepository $tagRepository)
    {
        $this->tagRepository = $tagRepository;
    }

    /**
     * @param string $tag
     * @return Tag[]
     */
    public function __invoke(string $tag): array
    {
        $tag = $this->tagRepository->findOneBy(['slug' => $tag]);
        return $tag->getAnimes()->toArray();
    }
}
