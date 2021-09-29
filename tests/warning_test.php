<?php

    require("ppm");

    /** @noinspection PhpUnhandledExceptionInspection */
    ppm_import("net.intellivoid.verbose_adventure");

    \VerboseAdventure\VerboseAdventure::setStdout(true);
    \VerboseAdventure\Classes\ErrorHandler::registerHandlers();

    trigger_error("test", E_USER_WARNING);
    trigger_error("test2", E_USER_WARNING);
    trigger_error("test2", E_USER_WARNING);
    trigger_error("test2", E_USER_WARNING);
    trigger_error("test3", E_USER_WARNING);
