<?php

namespace Murdej;

/**
 * @template TO
 * @template TN
 * @template TK
 */
class CollectionDiff
{
    /** @var CollectionDiffItem<null, TN, TK>[] */
    public array $newItems;

    /**
     * @var TK[]
     */
    public array $newItemKeys;

    /** @var CollectionDiffItem<TO, TN, TK>[] */
    public array $updatedItems;

    /**
     * @var TK[]
     */
    public array $updatedItemKeys;

    /** @var CollectionDiffItem<TO, null, TK>[] */
    public array $deletedItems;

    /**
     * @var TK[]
     */
    public array $deletedItemKeys;


    /**
     * @param TO $oldCollection
     * @param TK $newCollection
     * @param callable|string $dbKeyField
     * @param callable|string $newKeyField
     * @param callable $changedCallback ($dbItem, $newItem) => true if diff
     */
    public static function compare(
        array           $oldCollection,
        callable|string $dbKeyField,
        array           $newCollection,
        callable|string $newKeyField,
        callable        $diffCallback
    ): self {
        $diffs = new self();
        $oldBK = ProcI::from($oldCollection)->struct($dbKeyField)->toArray();
        $newBK = ProcI::from($newCollection)->struct($newKeyField)->toArray();

        $diffs->newItemKeys = array_diff(array_keys($newBK), array_keys($oldBK));
        $diffs->newItems = ProcI::from($newBK)
            ->filterKey($diffs->newItemKeys)
            ->map(fn($item, $key) => new CollectionDiffItem(null, $item, $key))
            ->toArray();

        $diffs->deletedItemKeys = array_diff(array_keys($oldBK), array_keys($newBK));
        $diffs->deletedItems = ProcI::from($oldBK)
            ->filterKey($diffs->deletedItemKeys)
            ->map(fn($item, $key) => new CollectionDiffItem($item, null, $key))
            ->toArray();

        $diffs->updatedItems = [];
        $diffs->updatedItemKeys = [];

        foreach (array_intersect(array_keys($oldBK), array_keys($newBK)) as $key) {
            if ($diffCallback($oldBK[$key], $newBK[$key])) {
                $diffs->updatedItems[$key] = new CollectionDiffItem($oldBK[$key], $newBK[$key], $key);
                $diffs->updatedItemKeys[] = $key;
            }
        }

        return $diffs;
    }
}