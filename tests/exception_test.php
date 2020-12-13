<?php
    require("ppm");

    /** @noinspection PhpUnhandledExceptionInspection */
    ppm_import("net.intellivoid.verbose_adventure");

    \VerboseAdventure\Classes\ErrorHandler::registerHandlers();
    \VerboseAdventure\VerboseAdventure::logGlobal(\VerboseAdventure\Abstracts\EventType::INFO, "Hello!");
    $test->test;