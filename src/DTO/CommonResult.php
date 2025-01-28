<?php

namespace Murdej\DTO;

use Murdej\ErrorList;

class CommonResult implements \JsonSerializable
{

	const StatusOk = 'ok';
	const StatusError = 'error';

	public mixed $entity = null;

	public function getStatus(): string
	{
		return $this->errors->isValid() ? self::StatusOk : self::StatusError;
	}

	public ErrorList $errors;

	public function __construct(array $data = [])
	{
		$this->errors = new ErrorList($data);
	}

	public function jsonSerialize(): mixed
	{
		return [
			'errors' => $this->errors,
			'status' => $this->getStatus(),
            'entity' => $this->entity,
		];
	}
}
