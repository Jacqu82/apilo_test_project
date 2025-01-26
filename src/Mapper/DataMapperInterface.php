<?php

namespace App\Mapper;

interface DataMapperInterface
{
    public function deserialize(string $data, string $class, string $format, array $context = []): object;

    public function serialize(object $data, string $format, array $context = []): string;

    public function denormalize(array $data, string $class, string $format, array $context = []): object;

    public function normalize(object $data, string $format, array $context = []): array;
}
