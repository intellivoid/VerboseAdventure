<?php


    namespace VerboseAdventure\Exceptions;


    use Exception;
    use Throwable;

    /**
     * Class CannotFindSystemLogDirectoryException
     * @package VerboseAdventure\Exceptions
     */
    class CannotFindSystemLogDirectoryException extends Exception
    {
        /**
         * CannotFindSystemLogDirectoryException constructor.
         * @param string $message
         * @param int $code
         * @param Throwable|null $previous
         */
        public function __construct($message = "", $code = 0, Throwable $previous = null)
        {
            parent::__construct($message, $code, $previous);
        }
    }