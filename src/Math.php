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
}