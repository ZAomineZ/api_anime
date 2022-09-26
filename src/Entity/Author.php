<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AuthorRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=AuthorRepository::class)
 */
#[ApiResource(
    collectionOperations: [
    'get' => [
        'access_control' => "is_granted('ROLE_USER')"
    ],
    'post' => [
        'access_control' => "is_granted('ROLE_ADMIN')"
    ]
],
    itemOperations: [
    'get' => [
        'access_control' => "is_granted('ROLE_USER')"
    ],
    'put' => [
        'access_control' => "is_granted('ROLE_ADMIN')"
    ],
    'delete' => [
        'access_control' => "is_granted('ROLE_USER')"
    ]
],
    denormalizationContext: ['groups' => ['create:author']],
    normalizationContext: ['groups' => ['read:author']]
)]
class Author
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:author'])]
    private ?int $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['create:author', 'read:author', 'read:anime'])]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['create:author', 'read:author'])]
    private ?string $slug;

    /**
     * @ORM\OneToMany(targetEntity=Anime::class, mappedBy="author")
     */
    private $animes;

    /**
     * Author constructor.
     */
    #[Pure] public function __construct()
    {
        $this->animes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return $this
     */
    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return Collection|Anime[]
     */
    public function getAnimes(): Collection
    {
        return $this->animes;
    }

    public function addAnime(Anime $anime): self
    {
        if (!$this->animes->contains($anime)) {
            $this->animes[] = $anime;
            $anime->setAuthor($this);
        }

        return $this;
    }

    public function removeAnime(Anime $anime): self
    {
        if ($this->animes->removeElement($anime)) {
            // set the owning side to null (unless already changed)
            if ($anime->getAuthor() === $this) {
                $anime->setAuthor(null);
            }
        }

        return $this;
    }
}
