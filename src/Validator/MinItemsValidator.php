<?php

namespace App\Validator;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class MinItemsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof MinItems) {
            throw new UnexpectedTypeException($constraint, MinItems::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!$value instanceof Collection && !is_array($value)) {
            throw new UnexpectedTypeException($value, Collection::class);
        }

        if (count($value) < $constraint->min) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ min }}', (string) $constraint->min)
                ->addViolation();
        }
    }
}
