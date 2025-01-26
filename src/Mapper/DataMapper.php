<?php

declare(strict_types=1);

namespace App\Mapper;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class DataMapper implements DataMapperInterface
{
    public function __construct(
        private readonly SerializerInterface $serializer,
        private readonly DenormalizerInterface $denormalizer,
        private readonly NormalizerInterface $normalizer,
    ) {
    }

    public function deserialize(string $data, string $class, string $format, array $context = []): object
    {
        return $this->serializer->deserialize($data, $class, $format, $context);
    }

    public function serialize(object $data, string $format, array $context = []): string
    {
        return $this->serializer->serialize($data, $format, $context);
    }

    public function denormalize(array $data, string $class, string $format, array $context = []): object
    {
        return $this->denormalizer->denormalize($data, $class, $format, $context);
    }

    public function normalize(object $data, string $format, array $context = []): array
    {
        return $this->normalizer->normalize($data, $format, $context);
    }
}
