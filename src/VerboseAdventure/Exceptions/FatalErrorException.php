<?php

declare(strict_types=1);

namespace VerboseAdventure\Exceptions;

use ErrorException;

/**
 * This exception is thrown when a fatal error occurs.
 *
 * @author Stefano Arlandini <sarlandini@alice.it>
 */
final class FatalErrorException extends ErrorException
{
}
