<?php
    require("ppm");

    /** @noinspection PhpUnhandledExceptionInspection */
    ppm_import("net.intellivoid.verbose_adventure");

    \VerboseAdventure\VerboseAdventure::setStdout(true);
    \VerboseAdventure\Classes\ErrorHandler::registerHandlers();
    \VerboseAdventure\VerboseAdventure::logGlobal(\VerboseAdventure\Abstracts\EventType::INFO, "Hello!");
    $test->test;