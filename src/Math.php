<?php declare(strict_types=1);

namespace Murdej;

/**
 * Mathematics functions
 */
class Math
{
	/**
	 * @template T of (int|float)
	 * @param int|float $fullValue
	 * @param (int|float)[] $rates
	 * @return T[]
	 */

	public static function divideByRate(int|float $fullValue, array $rates): array
	{
		// if (array_filter($rates, fn (int|float $rate) => $rate != 0))
		// 	throw new \InvalidArgumentException('At least one of the $rates must be non-zero.');
		$sum = array_sum($rates);
		if ($sum === 0) throw new \Exception("Rates sum must be non-zero.");
		$mul = $fullValue / $sum;

		return  array_map(
			fn(int|float $value) => $value * $mul,
			$rates
		);
	}

    /**
     * Compares two mixed values after rounding them based on a specified accuracy.
     *
     * @param mixed $a The first value to compare.
     * @param mixed $b The second value to compare.
     * @param float $accuracy The divisor used for normalization before rounding. A higher value means lower "accuracy" (larger rounding unit). Defaults to 1.
     * @return int Returns -1 if $a is less than $b, 0 if they are equal, and 1 if $a is greater than $b (after normalization and rounding).
     */
    public static function compareAccuracy(mixed $a, mixed $b, float $accuracy = 1): int
    {
        $a = round((float)$a / $accuracy);
        $b = round((float)$b / $accuracy);

        return $a <=> $b;
    }
}