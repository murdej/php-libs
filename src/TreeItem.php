<?php

namespace Murdej;

/**
 * @template T
 */
class TreeItem
{
    public function __construct(
        /**
         * @var T
         */
        public mixed $item,
        /**
         * @var T[]
         */
        public array $children,
    ) { }

}
