<?php

/**
 * CLI mode runner works the same
 */

require_once '/path/to/vendor/autoload.php';

echo \Light\Front::getInstance(require_once 'config.php')
    ->bootstrap()
    ->run();
