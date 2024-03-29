<?php

declare(strict_types=1);

namespace VerboseAdventure\Exceptions;

use ErrorException;

/**
 * Error that has been captured by the {@see ErrorHandler}, but that was silenced
 * with `@` in the source code.
 */
final class SilencedErrorException extends ErrorException
{
}
