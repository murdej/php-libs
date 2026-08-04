<?php

namespace Murdej\DTO;

use Murdej\ProcI;

/**
 * @template T
 */
class ListResult extends SmartDTO
{
    public function __construct(
        /**
         * @var T[]
         */
        public array $items,
        public ?int $totalCount = null,
        public int $limitFrom = 0,
        public ?int $limitCount = null,
    )
    {
    }

    public function getIsLast(): bool
    {
        return $this->limitFrom + $this->limitCount >= $this->totalCount;
    }

    public function getNextLimitFrom(): int
    {
        return $this->limitFrom + $this->limitCount;
    }

    public function mapItems($callback) {
        $this->items = ProcI::from($this->items)->map($callback)->toArray();
    }

}