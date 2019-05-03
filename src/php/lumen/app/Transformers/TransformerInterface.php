<?php

namespace App\Transformers;

use App\Entities\EntityInterface;

interface TransformerInterface
{
    public function transform(EntityInterface $entity);
}