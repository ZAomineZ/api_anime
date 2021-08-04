<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\Anime;
use App\Entity\Character;
use App\Entity\TypeAnime;
use App\Exception\FieldEntityExist;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class FieldNameExistSubscriber implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * FieldNameExistSubscriber constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return array[]
     */
    #[ArrayShape([KernelEvents::VIEW => "array"])] public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::VIEW => ['post', EventPriorities::POST_WRITE],
        ];
    }

    /**
     * @param ViewEvent $event
     * @throws FieldEntityExist
     */
    public function post(ViewEvent $event)
    {
        $result = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();
        $entity = get_class($event->getControllerResult());

        if (
            !$result instanceof Anime &&
            !$result instanceof Character &&
            !$result instanceof TypeAnime ||
            Request::METHOD_POST !== $method
        ) {
            return;
        }

        $find = $this->entityManager->getRepository($entity)->findBy(['name' => $result->getName()]);
        if ($find) {
            throw new FieldEntityExist('Ce champs name existe déja dans cette entitée.');
        }
    }
}
