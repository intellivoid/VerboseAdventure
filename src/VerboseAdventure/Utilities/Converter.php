<?php


    namespace VerboseAdventure\Utilities;


    use Exception;
    use VerboseAdventure\Abstracts\EventType;
    use VerboseAdventure\Abstracts\Options\ExceptionToDumpOptions;

    /**
     * Class Converter
     * @package VerboseAdventure\Utilities
     */
    class Converter
    {
        /**
         * Converts the name of a vendor or module to a string safe input
         *
         * @param string $input
         * @return string
         */
        public static function nameSafe(string $input): string
        {
            return str_ireplace(" ", "_", strtolower($input));
        }

        /**
         * Converts an EventType to a string
         *
         * @param int $input
         * @return string
         */
        public static function eventTypeToString(int $input): string
        {
            switch($input)
            {
                case EventType::INFO:
                    return "INFO";

                case EventType::VERBOSE:
                    return "VERBOSE";

                case EventType::WARNING:
                    return "WARNING";

                case EventType::ERROR:
                    return "ERROR";

                case EventType::UNKNOWN:
                default:
                    return "UNKNOWN";
            }
        }

        /**
         * Creates a detailed serializable array that contains information about the exception that can be used
         * for debugging
         *
         * @param Exception $exception
         * @param string|null $vendor_name
         * @param string|null $module_name
         * @param array $options
         * @return array
         */
        public static function exceptionToDump(Exception $exception, ?string $vendor_name, ?string $module_name=null, array $options=[]): array
        {
            $Results = array(
                "vendor_name" => null,
                "module_name" => null,
                "timestamp" => (int)time(),
                "exceptions" => []
            );

            if($vendor_name != null)
                $Results["vendor_name"] = Converter::nameSafe($vendor_name);

            if($module_name != null)
                $Results["module_name"] = Converter::nameSafe($module_name);

            $current_exception = $exception;

            while(true)
            {
                $exception_array = [];

                $exception_array["file"] = $current_exception->getFile();
                $exception_array["line"] = $current_exception->getLine();
                $exception_array["code"] = $current_exception->getCode();
                $exception_array["message"] = $current_exception->getMessage();
                $exception_array["trace"] = $current_exception->getTrace();
                $exception_array["trace_string"] = $current_exception->getTraceAsString();
                $Results["exceptions"][] = $exception_array;

                if($current_exception->getPrevious() == null)
                {
                    break;
                }
                else
                {
                    $current_exception = $current_exception->getPrevious();
                }
            }

            if(in_array(ExceptionToDumpOptions::IncludeCoreConstants, $options))
            {
                $DefinedConstants = get_defined_constants(true);
                if(isset($DefinedConstants["Core"]))
                {
                    $Results["core_constants"] = $DefinedConstants["Core"];
                }
            }

            if(in_array(ExceptionToDumpOptions::IncludeDefinedVariables, $options))
            {
                $Results["defined_variables"] = get_defined_vars();
            }

            if(in_array(ExceptionToDumpOptions::IncludeDefinedFunctions, $options))
            {
                $Results["defined_functions"] = get_defined_functions();
            }

            return $Results;
        }

        /**
         * Turns a captured exception to a dump
         *
         * @param array $data
         * @param array $options
         * @return array
         */
        public static function captureToDump(array $data, array $options=[]): array
        {
            $Results = array(
                "timestamp" => (int)time(),
                "exception" => $data
            );

            if(in_array(ExceptionToDumpOptions::IncludeCoreConstants, $options))
            {
                $DefinedConstants = get_defined_constants(true);
                if(isset($DefinedConstants["Core"]))
                {
                    $Results["core_constants"] = $DefinedConstants["Core"];
                }
            }

            if(in_array(ExceptionToDumpOptions::IncludeDefinedVariables, $options))
            {
                $Results["defined_variables"] = get_defined_vars();
            }

            if(in_array(ExceptionToDumpOptions::IncludeDefinedFunctions, $options))
            {
                $Results["defined_functions"] = get_defined_functions();
            }

            return $Results;
        }

        /**
         * Attempts to calculate the trace ID of the exception to
         *
         * @param Exception $e
         * @return string
         */
        public static function exceptionToTraceId(Exception $e): string
        {
            if($e->getTraceAsString() == null)
                return hash('crc32', (string)null);

            return hash('crc32', $e->getTraceAsString());
        }
    }