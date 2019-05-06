<?php

namespace App\Transformers;

use App\Entities\EntityInterface;

class UserTransformer extends BasicTransformer
{

    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function transform(EntityInterface $entity)
    {
        return [
            'id' => $entity->getId(),
            'email' => $entity->getEmail(),
        ];
    }
}