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
/**
 * Abstraction layer to use to get the list of cloning actions to perform.
 *
 * For simplicity sake, we're forced to implement Iterator interface.
 *
 * For complex gathering implementations, like obtaining the list from external databases,
 * loading CSV files, or command line scripts, you can initialize all the necessary stuff
 * in it's constructor (__construct()). It is highly recommended to check that everything
 * is ok in method rewind() to start iteration.
 */
interface Gathering extends Iterator {

}