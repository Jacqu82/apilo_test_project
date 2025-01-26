<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

class ZipCodeRequired extends Constraint
{
    public string $message = 'This field is required if a street is provided.';
}
