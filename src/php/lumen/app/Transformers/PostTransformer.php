<?php

namespace App\Transformers;

use App\Entities\EntityInterface;

class PostTransformer extends BasicTransformer
{
    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function transform(EntityInterface $entity)
    {
        return [
            'id' => $entity->getId(),
            'title' => $entity->getTitle()
        ];
    }
}