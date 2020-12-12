<?php

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
         * @var string|null
         */
        private ?string $log_archive_path;

        /**
         * Indicates if logging events should print to stdout
         *
         * @var bool
         */
        public bool $stdout;

        /**
         * The Unix Timestamp for when this class checked the structure of the file path
         *
         * @var int
         */
        private int $last_check_timestamp;

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
            $this->log_archive_path = $this->logging_path . DIRECTORY_SEPARATOR . "archives";

            if(file_exists($this->logging_path) == false)
            {
                mkdir($this->logging_path);
            }

            if(file_exists($this->exception_dumps_path) == false)
            {
                mkdir($this->exception_dumps_path);
            }

            if(file_exists($this->log_archive_path) == false)
            {
                mkdir($this->log_archive_path);
            }

            return true;
        }

        /**
         * @return string|null
         * @noinspection PhpUnused
         */
        public function getLogArchivePath(): ?string
        {
            return $this->log_archive_path;
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
         */
        private function prepareLoggingStream()
        {
            // To speed up the process, this will only be processed every 10 minutes ASSUMING if the
            // the program is running for that long
            if(($this->last_check_timestamp + 600) > time())
            {
                return;
            }

            $date_file_path = $this->logging_path . DIRECTORY_SEPARATOR . ".date";
            $stream_file_path = $this->logging_path . DIRECTORY_SEPARATOR . "main.log";

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
                    $archive_path = $this->log_archive_path . DIRECTORY_SEPARATOR . $stated_date . ".log";

                    if(file_exists($archive_path) == false)
                    {
                        copy($stream_file_path, $archive_path); // Archive the file
                        unlink($stream_file_path); // Delete the current stream
                        file_put_contents($stream_file_path, (string)null); // Re-create the stream
                    }
                }
            }

            $this->last_check_timestamp = (int)time();
        }

        /**
         * Writes the current input to the stream
         *
         * @param string $input
         */
        private function writeToStream(string $input)
        {
            $this->prepareLoggingStream();
            $stream_file_path = $this->logging_path . DIRECTORY_SEPARATOR . "main.log";
            file_put_contents($stream_file_path, $input, FILE_APPEND);
        }

        /**
         * @param string $event_type
         * @param string $message
         * @param string|null $module
         */
        public function log(string $event_type, string $message, ?string $module)
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


            $this->writeToStream($EventObject->__toString([
                EventToStringOptions::IncludeVendor,
                EventToStringOptions::IncludeModule,
                EventToStringOptions::IncludeTimestamp,
                EventToStringOptions::IncludeEventType,
                EventToStringOptions::IncludeInfoEventType
            ]));
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
            $dump_json = json_encode($dump);
            $dump_id = hash("sha256", $dump_json . (int)time());
            $dump_file_path = $this->exception_dumps_path . DIRECTORY_SEPARATOR . $dump_id . ".json";

            file_put_contents($dump_file_path, $dump_json);

            if($log_event)
            {
                $this->log(EventType::ERROR, $exception->getMessage() . " => '$dump_file_path' ($dump_id)", $module);
            }

            return $dump_id;
        }
    }