<?php

declare(strict_types=1);

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ZipCodeRequiredValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof ZipCodeRequired) {
            throw new UnexpectedTypeException($constraint, ZipCodeRequired::class);
        }

        if (!is_object($value)) {
            throw new UnexpectedValueException($value, 'object');
        }

        if (null === $value->getStreet()) {
            return;
        }

        if (null !== $value->getPostcode()) {
            return;
        }

        $this->context
            ->buildViolation($constraint->message)
            ->atPath('postCode')
            ->addViolation();
    }
}
