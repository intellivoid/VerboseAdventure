<?php

    namespace VerboseAdventure\Abstracts;

    abstract class ErrorFilterPresets
    {
        /**
         * All general errors thrown by the runtime
         */
        const ALL = [
            BuiltinErrorLevels::E_ERROR,
            BuiltinErrorLevels::E_WARNING,
            BuiltinErrorLevels::E_PARSE,
            BuiltinErrorLevels::E_NOTICE,
            BuiltinErrorLevels::E_CORE_ERROR,
            BuiltinErrorLevels::E_CORE_WARNING,
            BuiltinErrorLevels::E_COMPILE_ERROR,
            BuiltinErrorLevels::E_COMPILE_WARNING,
            BuiltinErrorLevels::E_USER_ERROR,
            BuiltinErrorLevels::E_USER_WARNING,
            BuiltinErrorLevels::E_USER_NOTICE,
            BuiltinErrorLevels::E_STRICT,
            BuiltinErrorLevels::E_RECOVERABLE_ERROR,
            BuiltinErrorLevels::E_DEPRECATED,
            BuiltinErrorLevels::E_USER_DEPRECATED,
            BuiltinErrorLevels::E_ALL
        ];

        /**
         * Filter errors only thrown by th engine
         */
        const ENGINE = [
            BuiltinErrorLevels::E_CORE_ERROR,
            BuiltinErrorLevels::E_CORE_WARNING,
            BuiltinErrorLevels::E_COMPILE_ERROR,
            BuiltinErrorLevels::E_COMPILE_WARNING,
            BuiltinErrorLevels::E_RECOVERABLE_ERROR,
        ];

        /**
         * Filters general errors related to your code and execution of methods
         */
        const GENERAL = [
            BuiltinErrorLevels::E_ERROR,
            BuiltinErrorLevels::E_WARNING,
            BuiltinErrorLevels::E_PARSE,
            BuiltinErrorLevels::E_NOTICE,
            BuiltinErrorLevels::E_USER_ERROR,
            BuiltinErrorLevels::E_USER_WARNING,
            BuiltinErrorLevels::E_USER_NOTICE,
            BuiltinErrorLevels::E_DEPRECATED,
            BuiltinErrorLevels::E_USER_DEPRECATED,
            BuiltinErrorLevels::E_ALL
        ];

        /**
         * Filters errors only
         */
        const ERRORS_ONLY = [
            BuiltinErrorLevels::E_ERROR,
            BuiltinErrorLevels::E_CORE_ERROR,
            BuiltinErrorLevels::E_COMPILE_ERROR,
            BuiltinErrorLevels::E_USER_ERROR,
            BuiltinErrorLevels::E_RECOVERABLE_ERROR,
            BuiltinErrorLevels::E_ALL
        ];

        /**
         * Filters warnings only
         */
        const WARNINGS_ONLY = [
            BuiltinErrorLevels::E_WARNING,
            BuiltinErrorLevels::E_CORE_WARNING,
            BuiltinErrorLevels::E_COMPILE_WARNING,
            BuiltinErrorLevels::E_USER_WARNING,
        ];

        /**
         * Filters general errors appropriate for production use
         */
        const NOTICES_ONLY = [
            BuiltinErrorLevels::E_PARSE,
            BuiltinErrorLevels::E_NOTICE,
            BuiltinErrorLevels::E_USER_NOTICE,
            BuiltinErrorLevels::E_STRICT,
            BuiltinErrorLevels::E_DEPRECATED,
            BuiltinErrorLevels::E_USER_DEPRECATED,
        ];

    }