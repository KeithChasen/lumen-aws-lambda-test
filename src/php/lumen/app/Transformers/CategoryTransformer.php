<?php

namespace App\Transformers;

use App\Entities\EntityInterface;

class CategoryTransformer extends BasicTransformer
{
    /**
     * @param EntityInterface $entity
     * @return array
     */
    public function transform(EntityInterface $entity)
    {
        return [
            'id' => $entity->getId(),
            'category' => $entity->getCategory()
        ];
    }
}