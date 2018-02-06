<?php

namespace Eloquent\Crud\Exception;

use Exception;

class AccessDeniedException extends Exception
{
    /**
     * @var array
     */
    private $arguments;

    /**
     * AccessDeniedException constructor.
     */
    public function __construct()
    {
        $this->arguments = func_get_args();
        $this->code = 403;
        $this->message = 'Access denied';
    }

    /**
     * Get all the arguments of the exception.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * Get the count of arguments.
     *
     * @return int
     */
    public function countArguments()
    {
        return count($this->arguments);
    }
}
