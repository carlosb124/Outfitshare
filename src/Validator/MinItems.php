<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

#[\Attribute]
class MinItems extends Constraint
{
    public string $message = 'El outfit debe tener al menos {{ min }} prendas.';
    public int $min = 2;

    public function __construct(mixed $min = 2, mixed $options = null, ?array $groups = null, mixed $payload = null)
    {
        // Si se pasa un array como primer argumento (estilo antiguo/custom de Symfony), extraemos 'min'
        if (is_array($min)) {
            $options = $min;
            $min = $options['min'] ?? 2;
        }

        parent::__construct($options, $groups, $payload);
        $this->min = $min;
    }
}
