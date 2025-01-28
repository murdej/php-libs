<?php

namespace Murdej;

/**
 * @template TO
 * @template TN
 * @template TK
 */
class CollectionDiffItem
{
    /**
     * @param TO $oldValue
     * @param TN $newValue
     * @param TK $key
     */
    public function __construct(
        public mixed $oldValue,
        public mixed $newValue,
        public mixed $key,
    ) { }
}