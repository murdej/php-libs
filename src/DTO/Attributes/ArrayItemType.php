<?php

declare(strict_types=1);

namespace App\Contracts\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_PROPERTY)]
final class ArrayItemType
{
    /**
     * @param  class-string  $itemType
     */
    public function __construct(
        public string $itemType,
    ) {}
}
