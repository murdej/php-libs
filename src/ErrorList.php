<?php declare(strict_types=1);

namespace Murdej;

use Murdej\DTO\ErrorListMessage;
use ArrayAccess;
use InvalidArgumentException;

/**
 * @phpstan-type FieldsWithMessage string|array<int|string,string>
 */
class ErrorList implements \JsonSerializable
{
	public string $pathSeparator = '/';

	/** @var ErrorListMessage[] */
	public array $messages = [];
	public function __construct(
		public array|ArrayAccess $src = [],
	) { }

    /**
     * @param FieldsWithMessage $field
     * @param string $message
     * @return void
     */
	public function checkFill(string|array $field, string $message = "Pole musí být vyplněno") {
		if (is_array($field)) {
			foreach ($field as $f => $m) {
				$this->checkFill(
					is_int($f) ? $m : $f,
					is_int($f) ? $message : $m
				);
			}
		} else if (!($this->getSrcValue($field) ?? false)) $this->addError($field, $message);
	}

    /**
     * @param FieldsWithMessage $field
     * @param string $regex
     * @param string $message
     * @return void
     */
    public function checkPreg(string|array $field, string $regex, string $message = "Pole má chybný formát") {
        if (is_array($field)) {
            foreach ($field as $f => $m) {
                $this->checkPreg(
                    is_int($f) ? $m : $f,
                    is_int($f) ? $message : $m
                );
            }
        } else if (!preg_match($regex, $this->getSrcValue($field) ?? '')) $this->addError($field, $message);
    }

    /**
     * @param FieldsWithMessage $field
     * @param string $message
     * @return void
     */
    public function checkEmail(string|array $field, string $message = "Pole musí obsahovat email"): void
    {
        $this->checkPreg(
            $field,
            '/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix',
            $message
        );
    }

    public function addError(string $field, string $message): ErrorListMessage
	{
		$elm = new ErrorListMessage($field, $message);
		$this->messages[] = $elm;

		return $elm;
	}

	public function getSrcValue(string $path): mixed
	{
		$pathParts = explode($this->pathSeparator, $path);
		$src = $this->src;
		$l = count($pathParts);
		foreach ($pathParts as $i => $pathPart) {
			if ($i === $l - 1) return $src[$pathPart] ?? null;
			else {
				if (isset($src[$pathPart])) {
					if (is_array($src[$pathPart])) $src = $src[$pathPart];
					else throw new InvalidArgumentException(
						"Value of '"
						. implode($this->pathSeparator, array_slice($pathParts, 0, $i + 1))
						. "' must be array."
					);
				}
			}
		}

		return null;
	}

	public function isValid()
	{
		return count($this->messages) === 0;
	}

	public function jsonSerialize(): mixed
	{
		return $this->messages;
	}
}