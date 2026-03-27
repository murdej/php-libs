<?php

namespace Murdej;

class ProcIReduceField
{
    public function __construct(
        public string $fieldName,
        public string|\Closure $getCallback,
        public string|\Closure $reduceCallback,
        public mixed $initValue,
    )
    {
    }
}