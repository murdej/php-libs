<?php

namespace Murdej;

class TreeMaker
{
    /**
     * @param array $elements
     * @param callable|string $getParentId
     * @param callable|string $getId
     * @param mixed|null $parentId
     * @return TreeItem
     */
    public static function make(
        array $elements,
        callable|string $getParentId,
        callable|string $getId,
        mixed $parentId = null,
    ) : array
    {
        $byParents = ProcI::from($elements)->struct(fn($item) => json_encode(ProcI::prepareCallback($getParentId)($item)))->toArray();
        $processElements = fn (array $elements) => array_map(
            fn ($element) => new TreeItem(
                $element,
                $processElements($byParents[json_encode($getId($element))] ?? []),
            ),
            $elements,
        );

        return $processElements($processElements($byParents[$parentId] ?? []));
    }
}