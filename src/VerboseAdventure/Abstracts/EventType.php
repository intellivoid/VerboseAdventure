<?php


    namespace VerboseAdventure\Abstracts;

    /**
     * Class EventType
     * @package VerboseAdventure\Abstracts
     */
    abstract class EventType
    {
        const UNKNOWN = 0x000;

        const INFO = 0x001;

        const VERBOSE = 0x002;

        const WARNING = 0x003;

        const ERROR = 0x004;
    }