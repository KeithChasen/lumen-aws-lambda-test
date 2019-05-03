<?php

namespace App\Transformers;

use App\Entities\EntityInterface;

abstract class BasicTransformer implements TransformerInterface
{
    /**
     * @param EntityInterface $entity
     * @return array
     */
    abstract public function transform(EntityInterface $entity);

    /**
     * @param array $entities
     * @return array
     */
    public function transformAll(array $entities) {
        return array_map(
            function ($entity) {
                return $this->transform($entity);
            }, $entities
        );
    }
}