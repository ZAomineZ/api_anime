<?php

namespace App\Controller;

use App\Repository\AnimeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AnimeByYearToFirstBroadcast extends AbstractController
{

    public function __construct(private AnimeRepository $animeRepository)
    {
    }

    /**
     * AnimeByYearToFirstBroadcast constructor.
     * @param string $year
     * @return array
     */
    public function __invoke(string $year): array
    {
        return $this->animeRepository->findAllByFirstBroadcast($year);
    }
}
