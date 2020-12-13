<?php

    require("ppm");

    /** @noinspection PhpUnhandledExceptionInspection */
    ppm_import("net.intellivoid.verbose_adventure");

    $logger = new \VerboseAdventure\VerboseAdventure("Intellivoid");
    $logger->log(\VerboseAdventure\Abstracts\EventType::INFO, "Hello!");