<?php

namespace Eloquent\Crud\Exception;

use Exception;
use Throwable;
use Illuminate\Database\Eloquent\Model;

class AccessDeniedException extends Exception
{
    /**
     * @var array
     */
    private array $arguments;

    /**
     * AccessDeniedException constructor.
     *
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     * @param array|null     $args
     * @param Model|null     $model
     */
    public function __construct(
        string $message = 'Access denied',
        int $code = 403,
        Throwable $previous = null,
        array $args = null,
        Model $model = null
    ) {
        parent::__construct($message, $code, $previous);
        if (empty($args)) {
            $this->arguments = func_get_args();
        } else {
            $this->arguments = [
                ...$args,
                $model,
            ];
        }
    }

    /**
     * Get all the arguments of the exception.
     *
     * @return array
     */
    public function getArguments(): array
    {
        return $this->arguments;
    }

    /**
     * Get the count of arguments.
     *
     * @return int
     */
    public function countArguments(): int
    {
        return count($this->arguments);
    }
}
