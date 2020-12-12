<?php


    namespace VerboseAdventure\Utilities;

    use VerboseAdventure\Abstracts\EventType;

    /**
     * Class Validator
     * @package VerboseAdventure\Utilities
     */
    class Validator
    {
        /**
         * Validates a Event Type
         *
         * @param int $event_type
         * @return bool
         */
        public static function eventType(int $event_type): bool
        {
            switch($event_type)
            {
                case EventType::INFO:
                case EventType::VERBOSE:
                case EventType::WARNING:
                case EventType::ERROR:
                    return True;

                default:
                    return false;
            }
        }
    }