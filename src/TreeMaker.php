<?php

namespace Murdej;

class TreeMaker
{
    /**
     * @param array $elements
     * @param callable|string $getParentId
     * @param callable|string $getId
     * @param mixed|null $parentId
     * @return TreeItem[]
     */
    public static function make(
        array $elements,
        callable|string $getParentId,
        callable|string $getId,
        mixed $parentId = null,
    ) : array
    {
        $byParents = ProcI::from($elements)
            ->struct(fn($item) => json_encode(ProcI::prepareCallback($getParentId)($item)), null)
            ->toArray();

        $processElements = function (array $elements, ?TreeItem $parent) use (&$processElements, $getId, &$byParents) {
            return array_map(
                function ($element) use ($processElements, $byParents, $getId, $parent) {
                    $node = new TreeItem(
                        $element,
                        parent: $parent,
                    );
                    $childs = $byParents[json_encode(ProcI::prepareCallback($getId)($element))] ?? false;
                    if ($childs)
                        $node->children = $processElements($childs, $node);

                    return $node;
                },
                $elements,
            );
        };

        return $processElements($byParents[json_encode($parentId)] ?? [], null);
    }

    /**
     * @param TreeItem[]|TreeItem $items
     * @return array
     */
    public static function linearize(array|TreeItem $items): array
    {
        $res = [];
        $processNode = function (TreeItem $item, bool $add = true) use (&$res, &$processNode) {
            if ($add) $res[] = $item;
            foreach ($item->children as $subItem) {
                $subItem->level = $item->level + 1;
                $processNode($subItem);
            }
        };

        if ($items instanceof TreeItem) $processNode($items);
        else $processNode(new TreeItem(null, $items, -1), false);

        return $res;

    }

    /**
     * Find first matching node
     * @template T
     * @param array|TreeItem<T> $items
     * @param callable(TreeItem<T>):bool $predicate
     * @return TreeItem<T>|null
     */
    public static function findFirst(array|TreeItem $items, callable $predicate): ?TreeItem
    {
        if ($items instanceof TreeItem) $items = [ $items ];
        foreach ($items as $item) {
            if ($predicate($item)) {
                return $item;
            }
            $res = self::findFirst($item->children, $predicate);
            if ($res !== null) return $res;
        }
        return null;
    }

    public static function path(TreeItem $node): array
    {
        $res = [];
        while ($node) {
            $res[] = $node;
            $node = $node->parent;
        }

        return array_reverse($res);
    }
}