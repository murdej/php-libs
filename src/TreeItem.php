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
         * @var TreeItem<T>[]
         */
        public array $children = [],

        public int $level = 0,

        /**
         * @var TreeItem<T>|null
         */
        public ?TreeItem $parent = null
    ) { }

}
