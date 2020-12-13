<?php


    namespace VerboseAdventure\Abstracts\Options;

    /**
     * Class EventToStringOptions
     * @package VerboseAdventure\Abstracts\Options
     */
    abstract class EventToStringOptions
    {
        /**
         * This option will include the vendor name in the event string
         */
        const IncludeVendor = 0x000;

        /**
         * This option will include the module name of the vendor in the event string
         */
        const IncludeModule = 0x001;

        /**
         * This option will display the event's timestamp
         */
        const IncludeTimestamp = 0x002;

        /**
         * This option will display the event type, except for "INFO"
         */
        const IncludeEventType = 0x003;

        /**
         * This will display "INFO" when displaying the event type, this requires IncludeEventType
         */
        const IncludeInfoEventType = 0x004;

        /**
         * Preserves double quotes and won't automatically convert them to single quotes
         */
        const PreserveDoubleQuotes = 0x005;

        /**
         * Includes a new line at the end of the string
         */
        const IncludeNewLine = 0x006;
    }