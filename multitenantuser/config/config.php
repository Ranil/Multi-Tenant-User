<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Version information
 *
 * @package     tool_multitenantuser
 * @copyright   2018 Owen Tolman <owen@accenagroup.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined("MOODLE_INTERNAL") || die();

/**
 * This is the default settings file for the behaviour of the plugin.
 *
 * Your local Moodle instance may need additional adjustments. Please do not modify this file.
 * Instead, create or edit a file in this same directory named
 * "config.local.php" to change elements of the default configuration.
 */
return array(

    //gathering tool
    'gathering' => 'CLIGathering',
);