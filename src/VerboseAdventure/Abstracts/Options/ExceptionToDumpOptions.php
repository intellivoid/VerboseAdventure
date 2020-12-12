<?php


    namespace VerboseAdventure\Abstracts\Options;

    /**
     * Class ExceptionToDumpOptions
     * @package VerboseAdventure\Abstracts\Options
     */
    abstract class ExceptionToDumpOptions
    {
        const IncludeCoreConstants = 0x001;

        const IncludeDefinedVariables = 0x002;

        const IncludeDefinedFunctions = 0x002;
    }