<?php

    /** @noinspection PhpMissingFieldTypeInspection */
    /** @noinspection PhpUnused */

    namespace VerboseAdventure;

    use Exception;
    use VerboseAdventure\Abstracts\EventType;
    use VerboseAdventure\Abstracts\Options\EventToStringOptions;
    use VerboseAdventure\Exceptions\CannotFindSystemLogDirectoryException;
    use VerboseAdventure\Objects\Event;
    use VerboseAdventure\Utilities\Converter;
    use VerboseAdventure\Utilities\Validator;

    /**
     * Class VerboseAdventure
     * @package VerboseAdventure
     */
    class VerboseAdventure
    {
        /**
         * The name of the
         *
         * @var string|null
         */
        private ?string $name;

        /**
         * @var string|null
         */
        private ?string $logging_path;

        /**
         * @var string|null
         */
        private ?string $exception_dumps_path;

        /**
         * Indicates if logging events should print to stdout
         *
         * @var bool|null
         */
        public static $stdout = false;

        /**
         * The Unix Timestamp for when this class checked the structure of the file path
         *
         * @var int|null
         */
        public static $last_check_timestamp;

        /**
         * VerboseAdventure constructor.
         * @param string $name
         * @throws CannotFindSystemLogDirectoryException
         */
        public function __construct(string $name)
        {
            $this->name = $name;
            $this->createStructure();
        }

        /**
         * Creates the logging structure if available
         *
         * @return bool
         * @throws CannotFindSystemLogDirectoryException
         * @noinspection DuplicatedCode
         */
        private function createStructure(): bool
        {
            $path = null;

            if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN")
            {

                $path = realpath(DIRECTORY_SEPARATOR);

                if(file_exists($path . "logs") == false)
                {
                    mkdir($path . "logs");
                    $path = realpath(DIRECTORY_SEPARATOR . "logs");
                }
            }
            else
            {
                $path = realpath(DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "log");
            }

            if($path == false)
            {
                throw new CannotFindSystemLogDirectoryException("There was an error while trying to verify the path '$path'");
            }

            if(file_exists($path) == false)
            {
                throw new CannotFindSystemLogDirectoryException("Cannot find the path '$path'");
            }

            $this->logging_path = $path . DIRECTORY_SEPARATOR . "php" . DIRECTORY_SEPARATOR . Converter::nameSafe($this->name);
            $this->exception_dumps_path = $this->logging_path . DIRECTORY_SEPARATOR . "exceptions";

            if(file_exists($path . DIRECTORY_SEPARATOR . "php") == false)
            {
                mkdir($path . DIRECTORY_SEPARATOR . "php");
            }

            if(file_exists($this->logging_path) == false)
            {
                mkdir($this->logging_path);
            }

            if(file_exists($this->exception_dumps_path) == false)
            {
                mkdir($this->exception_dumps_path);
            }

            return true;
        }

        /**
         * Returns the generic system logging path
         *
         * @return string
         * @throws CannotFindSystemLogDirectoryException
         * @noinspection DuplicatedCode
         */
        public static function getGenericLoggingPath(): string
        {
            $path = null;

            if (strtoupper(substr(PHP_OS, 0, 3)) === "WIN")
            {

                $path = realpath(DIRECTORY_SEPARATOR);

                if(file_exists($path . "logs") == false)
                {
                    mkdir($path . "logs");
                    $path = realpath(DIRECTORY_SEPARATOR . "logs");
                }
            }
            else
            {
                $path = realpath(DIRECTORY_SEPARATOR . "var" . DIRECTORY_SEPARATOR . "log");
            }

            if($path == false)
            {
                throw new CannotFindSystemLogDirectoryException("There was an error while trying to verify the path '$path'");
            }

            if(file_exists($path) == false)
            {
                throw new CannotFindSystemLogDirectoryException("Cannot find the path '$path'");
            }

            if(file_exists($path . DIRECTORY_SEPARATOR . "php") == false)
            {
                mkdir($path . DIRECTORY_SEPARATOR . "php");
            }

            return $path . DIRECTORY_SEPARATOR . "php";
        }


        /**
         * @return string|null
         * @noinspection PhpUnused
         */
        public function getExceptionDumpsPath(): ?string
        {
            return $this->exception_dumps_path;
        }

        /**
         * @return string|null
         * @noinspection PhpUnused
         */
        public function getLoggingPath(): ?string
        {
            return $this->logging_path;
        }

        /**
         * @return string|null
         */
        public function getName(): ?string
        {
            return $this->name;
        }

        /**
         * Prepares the logging stream and archives the current log if necessary
         *
         * @param string $path
         * @param bool $skip_last_check
         */
        private static function prepareLoggingStream(string $path, bool $skip_last_check=false)
        {
            // To speed up the process, this will only be processed every 10 minutes ASSUMING if the
            // the program is running for that long
            if($skip_last_check == false)
            {
                if(self::$last_check_timestamp !== null)
                {
                    if((self::$last_check_timestamp + 600) > time())
                    {
                        return;
                    }
                }
            }

            $date_file_path = $path . DIRECTORY_SEPARATOR . ".date";
            $stream_file_path = $path . DIRECTORY_SEPARATOR . "main.log";

            if(file_exists($stream_file_path) == false)
            {
                file_put_contents($stream_file_path, (string)null);
            }

            if(file_exists($date_file_path) == false)
            {
                file_put_contents($date_file_path, date("Ymd"));
            }
            else
            {
                $current_date = date("Ymd");
                $stated_date = file_get_contents($date_file_path);

                if($current_date !== $stated_date)
                {
                    if(file_exists($path . DIRECTORY_SEPARATOR . "archives") == false)
                    {
                        mkdir($path . DIRECTORY_SEPARATOR . "archives");
                    }

                    $archive_path = $path . DIRECTORY_SEPARATOR . "archives" . DIRECTORY_SEPARATOR . $stated_date . ".log";

                    if(file_exists($archive_path) == false)
                    {
                        copy($stream_file_path, $archive_path); // Archive the file
                        unlink($stream_file_path); // Delete the current stream
                        file_put_contents($stream_file_path, (string)null); // Re-create the stream
                    }
                }
            }

            self::$last_check_timestamp = (int)time();
        }

        /**
         * Writes the current input to the stream
         *
         * @param string $logging_path
         * @param string $input
         */
        public static function writeToStream(string $logging_path, string $input)
        {
            self::prepareLoggingStream($logging_path);
            $stream_file_path = $logging_path . DIRECTORY_SEPARATOR . "main.log";
            file_put_contents($stream_file_path, $input, FILE_APPEND);
        }

        /**
         * @param string $event_type
         * @param string $message
         * @param string|null $module
         * @noinspection DuplicatedCode
         */
        public function log(string $event_type, string $message, ?string $module=null)
        {
            if(Validator::eventType($event_type) == false)
            {
                $event_type = EventType::UNKNOWN;
            }

            $EventObject = new Event();
            $EventObject->vendor_name = $this->name;
            $EventObject->module = $module;
            $EventObject->message = $message;
            $EventObject->event_type = $event_type;
            $EventObject->timestamp = (int)time();

            $output = $EventObject->toString([
                EventToStringOptions::IncludeVendor,
                EventToStringOptions::IncludeModule,
                EventToStringOptions::IncludeTimestamp,
                EventToStringOptions::IncludeEventType,
                EventToStringOptions::IncludeInfoEventType,
                EventToStringOptions::IncludeNewLine
            ]);

            if(self::$stdout)
                print($output);
            self::writeToStream($this->logging_path, $output);
        }


        /**
         * Dumps the exception and optionally logs the event
         *
         * @param Exception $exception
         * @param string|null $module
         * @param bool $log_event
         * @return string
         */
        public function logException(Exception $exception, ?string $module, bool $log_event=true): string
        {
            $dump = Converter::exceptionToDump($exception, $this->name, $module);
            $dump_json = json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $dump_id = date('m-d-Y_hia') . "_" . hash("crc32b", $dump_json . (int)time());

            if($module !== null)
            {
                $dump_file_path = $this->exception_dumps_path . DIRECTORY_SEPARATOR . Converter::nameSafe($module) . "_" . $dump_id . ".json";
            }
            else
            {
                $dump_file_path = $this->exception_dumps_path . DIRECTORY_SEPARATOR . $dump_id . ".json";
            }

            file_put_contents($dump_file_path, $dump_json);

            if($log_event)
            {
                $this->log(EventType::ERROR, $exception->getMessage() . " => '$dump_file_path' ($dump_id)", $module);
            }

            return $dump_id;
        }

        /**
         * @param string $event_type
         * @param string $message
         * @param string|null $module
         * @throws CannotFindSystemLogDirectoryException
         * @throws CannotFindSystemLogDirectoryException
         * @noinspection DuplicatedCode
         */
        public static function logGlobal(string $event_type, string $message, ?string $module=null)
        {
            if(Validator::eventType($event_type) == false)
            {
                $event_type = EventType::UNKNOWN;
            }

            $EventObject = new Event();
            $EventObject->vendor_name = "runtime";
            $EventObject->module = $module;
            $EventObject->message = $message;
            $EventObject->event_type = $event_type;
            $EventObject->timestamp = (int)time();

            $output = $EventObject->toString([
                EventToStringOptions::IncludeVendor,
                EventToStringOptions::IncludeModule,
                EventToStringOptions::IncludeTimestamp,
                EventToStringOptions::IncludeEventType,
                EventToStringOptions::IncludeInfoEventType,
                EventToStringOptions::IncludeNewLine
            ]);

            if(self::$stdout)
                print($output);

            self::writeToStream(self::getGenericLoggingPath() . DIRECTORY_SEPARATOR . "runtime", $output);
        }

        /**
         * This methods logs an unexpected but captured exception
         *
         * @param Exception $exception
         * @param bool $log_event
         * @param string|null $module
         * @return string
         * @throws CannotFindSystemLogDirectoryException
         */
        public static function logCapturedException(Exception $exception, bool $log_event=true, ?string $module=null): string
        {
            $dump = Converter::exceptionToDump($exception, "runtime");
            $dump_json = json_encode($dump, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $dump_id = date('m-d-Y_hia') . "_" . hash("crc32b", $dump_json . (int)time());
            $runtime_path = self::getGenericLoggingPath() . DIRECTORY_SEPARATOR . "runtime";
            $exceptions_path = $runtime_path . DIRECTORY_SEPARATOR . "exceptions";

            if($module !== null)
            {
                $dump_file_path = $exceptions_path . DIRECTORY_SEPARATOR . Converter::nameSafe($module) . "_" . $dump_id . ".json";
            }
            else
            {
                $dump_file_path = $exceptions_path . DIRECTORY_SEPARATOR . $dump_id . ".json";
            }

            if(file_exists($runtime_path) == false)
            {
                mkdir($runtime_path);
            }

            if(file_exists($exceptions_path) == false)
            {
                mkdir($exceptions_path);
            }

            file_put_contents($dump_file_path, $dump_json);

            if($log_event)
            {
                self::logGlobal(EventType::ERROR, $exception->getMessage() . " => '$dump_file_path' ($dump_id)", $module);
            }

            trigger_error("The exception has been dumped to $dump_file_path", E_USER_NOTICE);
            return $dump_id;
        }

        /**
         * @return bool|null
         */
        public static function getStdout(): ?bool
        {
            return self::$stdout;
        }

        /**
         * @param bool|null $stdout
         */
        public static function setStdout(?bool $stdout): void
        {
            self::$stdout = $stdout;
        }
    }