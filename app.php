<?php

require 'vendor/autoload.php';

use Symfony\Component\Console\Application;
use Wolfish\Command\NumToTextCommand;

$application = new Application();

$application->add(new NumToTextCommand());

$application->run();
