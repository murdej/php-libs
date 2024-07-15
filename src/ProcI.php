<?php

namespace Murdej;

class ProcI
{
	public $src;

	static function prepareCallback($callback)
	{
		if (is_string($callback))
		{
			$toInt = false;
			if (substr($callback, 0, 5) == '(int)')
			{
				$toInt = true;
				$callback = substr($callback, 5);
			}
			if ($toInt)
			{
				if ($callback == '.')
				{
					$callback = function($item) { return (int)$item; };
				}
				elseif ($callback[0] == '.')
				{
					$colName = substr($callback, 1);
					$callback = function($item) use ($colName) { return (int)$item->{$colName}; };
				}
				elseif ($callback[0] == '[')
				{
					$colName = substr($callback, 1);
					$callback = function($item) use ($colName) { return (int)$item[$colName]; };
				}
			}
			else
			{
				if ($callback == '.')
				{
					$callback = function($item) { return $item; };
				}
				elseif ($callback[0] == '.')
				{
					$colName = substr($callback, 1);
					$callback = function($item) use ($colName) { return $item->{$colName}; };
				}
				elseif ($callback[0] == '[')
				{
					$colName = substr($callback, 1);
					$callback = function($item) use ($colName) { return $item[$colName]; };
				}
			}
		}

		return $callback;
	}

	public function map($itemCallback, $keyCallback = null) : self
	{
		$res = [];
		$itemCallback = self::prepareCallback($itemCallback);
		$keyCallback = self::prepareCallback($keyCallback);

		foreach($this->src as $k => $item)
		{
			$v = $itemCallback ? $itemCallback($item, $k) : $item;
			if ($keyCallback) $k = $keyCallback($item, $k);
			$res[$k] = $v;
		}
		$this->src = $res;

		return $this;
	}

	public function orderBy(...$callbacks) : self
	{
		if (!is_array($this->src)) $this->src = $this->toArray();
		foreach($callbacks as $i => $callback)
		{
			$callbacks[$i] = self::prepareCallback($callback);
		}

		uasort($this->src, function($a, $b) use ($callbacks) 
		{
			foreach($callbacks as $callback)
			{
				$ac = $callback($a);
				$bc = $callback($b);
				if ($ac == $bc) continue; 
				return ($ac < $bc) ? -1 : 1;
			}
			return 0;
		});

		return $this;
	}

	public function filter($callback) : self
	{
		$res = [];
		$callback = self::prepareCallback($callback);

		foreach($this->src as $k => $item)
		{
			if ($callback($item, $k)) $res[$k] = $item;
		}
		$this->src = $res;

		return $this;
	}

	public function unique() : self
	{
		$this->src = array_unique($this->src);

		return $this;
	}

	public function struct(...$callbacks) : self
	{
		return $this->mapStruct(null, ...$callbacks);
	}

	public function mapStruct($mapCallback, ...$callbacks) : self
	{
		$res = [];
		$mapCallback = self::prepareCallback($mapCallback);
		foreach($callbacks as $i => $callback)
		{
			$callbacks[$i] = self::prepareCallback($callback);
		}
		end($callbacks);
		$lastCallbackI = key($callbacks);
		foreach($this->src as $k => $item)
		{
            $_k = $k;
			$a = &$res;
			foreach($callbacks as $i => $callback)
			{
				if ($callback)
				{
					$k = $callback($item, $k);
				} 
				else 
				{
					$a[] = [];
					end($a);
					$k = key($a);
				}
				if ($i === $lastCallbackI) 
				{
					$a[$k] = $mapCallback ? $mapCallback($item, $_k) : $item;
				}
				else
				{
					if (!isset($a[$k])) $a[$k] = [];
					$a = &$a[$k];
				}
			}
		}
		$this->src = $res;

		return $this;
	}

	public function reduce($callback, $initial)
	{
		return array_reduce($this->src, $callback, $initial);
	}

	public function first($callback, $default = null)
	{
		$callback = self::prepareCallback($callback);

		foreach($this->src as $k => $item)
		{
			if ($callback($item, $k)) return $item;
		}

		return $default;
	}

	public function __construct($src)
	{
		if ($src instanceof self) $this->src = $src->src;
		else $this->src = $src;
	}
	
	public function toArray($iterable = null)
	{
		if ($iterable === null) $iterable = $this->src;
		if (!is_array($iterable)) {
			$arr = [];
			foreach($this->src as $k => $v) $arr[$k] = $v;
			$this->src = $arr;
		}
		return $this->src;
	}

    public function values() : self {
        $this->src = array_values($this->src);
        return $this;
    }

	public static function from(...$srcs) : self
	{
        $src = [];
        if (count($srcs) > 1) {
            foreach ($srcs as $block) {
                foreach ($block as $item) {
                    $src[] = $item;
                }
            }
        } else {
            foreach (reset($srcs) as $k => $item) {
                $src[$k] = $item;
            }
        }

		return new ProcI($src);
	}

	//
	public static function selectFields($src, array $fields, bool $trans = false) : array
	{
		$res = [];
		foreach($src as $k => $v)
		{
			if ($trans)
				if (isset($fields[$k])) $res[$fields[$k]] = $v;
			else 
				if (in_array($k, $fields)) $res[$k] = $v;
		}
		return $res;
	}

	public static function cbValue($value)
	{
		return function() use ($value) { return $value; };
	}

	public static function firstKey($arr)
	{
		reset($arr);
		return key($arr);
	}

	public function deepMap(int $level, $callback)
	{
		$callback = $this->prepareCallback($callback);
		// if (!is_array($this->src)) $this->src = $this->toArray();
		$this->_deepMap($level, $callback, $this->src, []);
		return $this;
	}

    /**
     * Vrací true pokud předaná podmínka platí pro všechny prvky, nebo je pole prázdné
     * @param $callback
     * @return bool
     */
    public function allMeets($callback) : bool {
        $callback = self::prepareCallback($callback);
        foreach ($this->src as $k => $v) {
            if (!$callback($v, $k)) return false;
        }

        return true;
    }

    /**
     * Vrací true pokud předaná podmínka platí aspoň pro jeden prvek
     * @param $callback
     * @return bool
     */
    public function anyMeets($callback) : bool {
        $callback = self::prepareCallback($callback);
        foreach ($this->src as $k => $v) {
            if ($callback($v, $k)) return true;
        }

        return false;
    }

    public function allAre(mixed $value, bool $exact = false) : bool {
        foreach ($this->src as $k => $v) {
            if ($exact ? ($v !== $value) : ($v != $value)) return false;
        }

        return true;
    }

    public function anyIs(mixed $value, bool $exact = false) : bool {
        foreach ($this->src as $k => $v) {
            if ($exact ? ($v === $value) : ($v == $value)) return true;
        }

        return false;
    }

    public function _deepMap(int $level, $callback, &$ls, array $ks)
	{
		if ($level)
		{
			foreach($ls as $k => $v) $this->_deepMap(
				$level - 1, 
				$callback, 
				$ls[$k],
				array_merge($ks, [$k])
			);
		}
		else
		{
			$ls = $callback($ls, $ks);
		}
	}
	
	/* todo:
	public static function replace(array $arr, $oldValues, $newValue, $onlyOnce = false)
	{
		$removed = false;
		foreach($arr as $k => $v)
		{
			if (in_array($v, $oldValues))
			{
				if (!$removed || !$onlyOnce)
				{
					// Nahraď

				}
			}
		}
	} */
    public function iterSrc()
    {
        $newSrc = [];
        foreach ($this->src as $k => $v)
            $newSrc[$k] = $v;

        $this->src = $newSrc;

        return $this;
    }
}

function PrI($src)
{
	return new ProcI($src);
}
