<?php

declare(strict_types=1);

namespace Murdej\DTO\Attributes;

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
