<?php


    namespace VerboseAdventure\Objects;

    use VerboseAdventure\Abstracts\EventType;
    use VerboseAdventure\Abstracts\Options\EventToStringOptions;
    use VerboseAdventure\Utilities\Converter;

    /**
     * Class LogEvent
     * @package VerboseAdventure\Objects
     */
    class Event
    {
        /**
         * The Unix Timestamp
         *
         * @var int|null
         */
        public ?int $timestamp;

        /**
         * The type of event that this is
         *
         * @var int|null
         */
        public ?int $event_type;

        /**
         * The message of the event
         *
         * @var string|null
         */
        public ?string $message;

        /**
         * The name of the vendor that reported this event
         *
         * @var string|null
         */
        public ?string $vendor_name;

        /**
         * The optional module name of the vendor that is reporting the event
         *
         * @var string|null
         */
        public ?string $module;

        /**
         * Returns a string formatted as a log event
         *
         * @param EventToStringOptions[] $options
         * @return string
         */
        public function toString(array $options): string
        {
            if(in_array(EventToStringOptions::PreserveDoubleQuotes, $options))
            {
                $results = "\"" . str_ireplace("\"", "'", $this->message) . "\"";
            }
            else
            {
                $results = "\"" . $this->message. "\"";
            }

            if(in_array(EventToStringOptions::IncludeTimestamp, $options))
            {
                if($this->timestamp !== null)
                {
                    $results = "[" . date("Y-m-d H:i:s", $this->timestamp) . "] " . $results;
                }
            }

            $vendor_data_appended = false;
            if(in_array(EventToStringOptions::IncludeVendor, $options) && in_array(EventToStringOptions::IncludeModule, $options))
            {
                if($this->vendor_name !== null && $this->module !== null)
                {
                    $results = $this->vendor_name . " - " . $this->module . " " . $results;
                    $vendor_data_appended = true;
                }
            }

            if($vendor_data_appended == false)
            {
                if(in_array(EventToStringOptions::IncludeVendor, $options) && $this->vendor_name !== null)
                {
                    $results = $this->vendor_name . " " . $results;
                }

                if(in_array(EventToStringOptions::IncludeModule, $options) && $this->module !== null)
                {
                    $results = $this->module . " " . $results;
                }
            }

            if(in_array(EventToStringOptions::IncludeEventType, $options))
            {
                if($this->event_type == EventType::INFO && in_array(EventToStringOptions::IncludeInfoEventType, $options))
                {
                    $results = "[" . Converter::eventTypeToString($this->event_type) . "] " . $results;
                }
                else
                {
                    $results = "[" . Converter::eventTypeToString($this->event_type) . "] " . $results;
                }
            }

            if(in_array(EventToStringOptions::IncludeNewLine, $options))
            {
                $results .= "\n";
            }

            return $results;
        }

        /**
         * Returns an array that represents this object's structure
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                "timestamp" => $this->timestamp,
                "event_type" => $this->event_type,
                "message" => $this->message,
                "vendor_name" => $this->vendor_name,
                "module" => $this->module
            );
        }

        /**
         * Constructs the object from an array
         *
         * @param array $data
         * @return Event
         */
        public static function fromArray(array $data): Event
        {
            $EventObject = new Event();

            if(isset($data["timestamp"]))
            {
                $EventObject->timestamp = (int)$data["timestamp"];
            }

            if(isset($data["event_type"]))
            {
                $EventObject->event_type = (int)$data["event_type"];
            }

            if(isset($data["message"]))
            {
                $EventObject->message = (string)$data["message"];
            }

            if(isset($data["vendor_name"]))
            {
                $EventObject->vendor_name = (string)$data["vendor_name"];
            }

            if(isset($data["module"]))
            {
                $EventObject->module = (string)$data["module"];
            }

            return $EventObject;
        }

    }