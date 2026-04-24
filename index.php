<?php

/**
 * Root entry point for shared hosting.
 *
 * Allows running the app even when the domain is pointed to the project root
 * (common in cPanel shared hosting setups).
 */

require __DIR__.'/public/index.php';
