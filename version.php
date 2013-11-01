<?php
/**
 * Elgg version number.
 * This file defines the current version of the core Elgg code being used.
 * This is compared against the values stored in the database to determine
 * whether upgrades should be performed.
 *
 * @package Elgg
 * @subpackage Core
 */

// YYYYMMDD = Elgg Date
// XX = Interim incrementer
$version = 2013093101;

// Human-friendly version name
$minds_release = 'Minds-1.0';
$release = '1.8.15'; // Can't use "Minds-1.0" without screwing up plugin dependancies 