<?php

namespace App\Serializer;

use App\Entity\Character;
use ArrayObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Vich\UploaderBundle\Storage\StorageInterface;

final class MediaCharacterObjectNormalizer implements ContextAwareNormalizerInterface, NormalizerAwareInterface
{

    use NormalizerAwareTrait;

    private const ALREADY_CALLED = 'MEDIA_CHARACTER_OBJECT_NORMALIZER_ALREADY_CALLED';

    /**
     * MediaCharacterObjectNormalizer constructor.
     * @param StorageInterface $storage
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(private StorageInterface $storage, private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @param mixed $data
     * @param string|null $format
     * @param array $context
     * @return bool
     */
    public function supportsNormalization($data, string $format = null, array $context = []): bool
    {
        if (isset($context[self::ALREADY_CALLED])) return false;

        return $data instanceof Character;
    }

    /**
     * @param NormalizerInterface $normalizer
     */
    public function setNormalizer(NormalizerInterface $normalizer)
    {
        $this->normalizer = $normalizer;
    }

    /**
     * @param Character $object
     * @param string|null $format
     * @param array $context
     * @return array|ArrayObject|bool|float|int|string|void|null
     * @throws ExceptionInterface
     */
    public function normalize($object, string $format = null, array $context = [])
    {
        $context[self::ALREADY_CALLED] = true;

        if (isset($context['collection_operation_name']) && $context['collection_operation_name'] === 'post_image') {
            $object->setFileUrl($this->storage->resolveUri($object, 'file'));

            $this->entityManager->persist($object);
            $this->entityManager->flush();
        }
        return $this->normalizer->normalize($object, $format, $context);
    }
}
