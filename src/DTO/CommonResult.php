<?php

namespace Murdej\DTO;

use Murdej\ErrorList;

class CommonResult extends SmartDTO
{

	const StatusOk = 'ok';
	const StatusError = 'error';

	public function getStatus(): string
	{
		return $this->errors->isValid() ? self::StatusOk : self::StatusError;
	}

	public ErrorList $errors;

	public function __construct(
        public mixed $data = []
    )
	{
		$this->errors = new ErrorList($data ?? []);
	}

	public function jsonSerialize(): mixed
	{
		return [
			'errors' => $this->errors,
			'status' => $this->getStatus(),
            'data' => $this->data,
		];
	}

    public static function error(ErrorList $errors): static
    {
        $res = new static();
        $res->errors = $errors;
        return $res;
    }

    public static function ok(mixed $data = null, mixed $defaultData = []): static
    {
        return new static($data ?? $defaultData);
    }

}
