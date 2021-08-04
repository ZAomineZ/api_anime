<?php


namespace App\DataPersister;


use ApiPlatform\Core\DataPersister\ContextAwareDataPersisterInterface;
use App\Entity\Anime;
use App\Entity\Character;
use App\Exception\NotSlugValid;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

final class AnimeDataPersister implements ContextAwareDataPersisterInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;
    /**
     * @var SluggerInterface
     */
    private SluggerInterface $slugger;

    /**
     * CharacterDataPersister constructor.
     * @param EntityManagerInterface $entityManager
     * @param SluggerInterface $slugger
     */
    public function __construct(EntityManagerInterface $entityManager, SluggerInterface $slugger)
    {
        $this->entityManager = $entityManager;
        $this->slugger = $slugger;
    }

    /**
     * @param $data
     * @param array $context
     * @return bool
     */
    public function supports($data, array $context = []): bool
    {
        return $data instanceof Anime;
    }

    /**
     * @param Character $data
     * @param array $context
     * @return object|void
     * @throws NotSlugValid
     */
    public function persist($data, array $context = [])
    {
        dd($data);
        $slug = $data->getSlug();
        if ($this->slugger->slug($slug)->toString() !== $slug) throw new NotSlugValid('Ceci n\'est pas un slug valid.');

        $this->entityManager->persist($data);
        $this->entityManager->flush();
    }

    /**
     * @param $data
     * @param array $context
     */
    public function remove($data, array $context = [])
    {
        $this->entityManager->remove($data);
        $this->entityManager->flush();
    }
}
