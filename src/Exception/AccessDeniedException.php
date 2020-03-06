<?php

namespace Eloquent\Crud\Exception;

use Exception;
use Throwable;

class AccessDeniedException extends Exception
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * AccessDeniedException constructor.
     *
     * @param string     $message
     * @param int        $code
     * @param \Throwable $previous
     */
    public function __construct($message = 'Access denied', $code = 403, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->arguments = func_get_args();
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
