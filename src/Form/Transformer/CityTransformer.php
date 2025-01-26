<?php

declare(strict_types=1);

namespace App\Form\Transformer;

use Symfony\Component\Form\DataTransformerInterface;

class CityTransformer implements DataTransformerInterface
{
    public function transform(mixed $value): ?string
    {
        return $value;
    }

    public function reverseTransform(mixed $value): ?string
    {
        if (null === $value) {
            return null;
        }

        return ucfirst(strtolower($value));
    }
}
