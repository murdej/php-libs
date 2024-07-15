<?php

namespace Murdej;

class StringWalk 
{
	const End = '__end';

	public $src = null;

	protected $mark = [];

	public $pos = 0;

	public $debug = false;

	public $debugCallback = null;

	public function __construct($src)
	{
		$this->src = $src;
		$this->mark[self::End] = strlen($this->src);
		$this->debugCallback = function($method, $msg)
		{
			echo "[$method] $msg | ".$this->pos." ".$this->getLeftSrc(20)."\n";
		};
	}

	protected function trace($method, $msg = "")
	{
		if ($this->debug) ($this->debugCallback)($method, $msg);
	}

	public function getLeftSrc($max = null)
	{
		return $max
			? substr($this->src, $this->pos, $max)
			: substr($this->src, $this->pos);
	}

	public $lastChunk = null;

	public $lastChunkNum = null;

	public $lastMarkName = null;

	public function findNext($needles, $cs = true)
	{
		$res = [];
		if (is_string($needles)) $needles = [$needles];
		// $leftSrc = $this->getLeftSrc();
		// Všechny výskyty
		foreach($needles as $n => $needle)
		{
			$p = $cs 
				? stripos($this->src, $needle, $this->pos) 
				: strpos($this->src, $needle, $this->pos);
			if ($p !== false) $res[] = [$needle, $p, $n];
		}
		// Pozičně první
		$minPos = null;
		$this->lastChunk = null;
		$this->lastChunkNum = null;
		foreach($res as $item)
		{
			list($needle, $p, $n) = $item;
			if ($p < $minPos || $minPos === null)
			{
				$this->lastChunk = $needle;
				$this->lastChunkNum = $n;
				$this->pos = $p;
				$minPos = $p;
			}
		}

		$this->trace('findNext', implode(', ', $needles));

		return $this->lastChunk;
	}

	public function saveMark($n = '.') 
	{
		$this->trace('saveMark', $n);

		$this->mark[$n] = $this->pos;
		$this->lastMarkName = $n;
	}

	public function goto($n)
	{
		$this->pos = $this->mark[$n];
	}

	public function toEndChunk()
	{
		$this->pos += strlen($this->lastChunk);
		$this->trace('toEndChunk');
	}

	/*
	substr() - from last mark to actual position
	substr($mark) - from mark to from actual position
	substr($mark1, $mark2) - from mark1 to mark2
	*/
	public function substr($f = null, $t = null)
	{
		if ($f === null && $t === null)
		{
			/* $pf = $this->pos;
			$pt = $this->srcLen() - 1; */
			$pf = $this->mark[$this->lastMarkName];
			$pt = $this->pos;
		}
		else if ($t === null)
		{
			$pf = $this->mark[$f];
			$pt = $this->pos;
		}
		else 
		{
			$pf = $this->mark[$f];
			$pt = $this->mark[$t];
		}

		return substr($this->src, $pf, $pt - $pf);
	}

	public function srcLen()
	{
		return strlen($this->src);
	}

	/**
	 * Check if left string starts with entered string
	 */
	public function startsWith(string $str) : bool
	{
		return $str == substr($this->getLeftSrc(), 0, strlen($str));
	}

	/**
	 * Check if left string starts with entered string
	 */
	public function findCurrent($needles)
	{
		if (is_string($needles)) $needles = [$needles];
		$this->lastChunk = null;
		$this->lastChunkNum = null;
		$i = 0;
		foreach($needles as $n => $needle)
		{
			if ($this->startsWith($needle))
			{
				$this->lastChunk = $needle;
				$this->lastChunkNum = $i++;

				return $this->lastChunk;
			}
		}

		return null;
	}
}