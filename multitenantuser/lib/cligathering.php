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
(defined("MOODLE_INTERNAL") && defined("CLI_SCRIPT")) || die();

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';

/**
 * Abstraction layer used to get the list of actions to perform asked from the command line.
 */
class CLIGathering implements Gathering {

    /**
     * @var stdClass object with userid and tenantid fields.
     */
    protected $current;
    /**
     * @var bool true if user chose to conclude action, false otherwise.
     */
    protected $end;
    /**
     * @var int zero-based index of the number of asked merging operations.
     */
    protected $index;

    /**
     * Initialization, also for capturing Ctrl+Cc interruptions.
     */
    public function __construct() {
        $this->index = -1;
        $this->end = false;
    }

    /**
     * Asks via command line for user to clone and where to, with a header telling what to do.
     */
    public function rewind() {
        cli_heading(get_string('pluginname', 'tool_multitenantuser'));
        echo get_string('cligathering:description', 'tool_multitenantuser') . "\n\n";
        echo get_string('cligathering:stopping', 'tool_multitenantuser') . "\n\n";
        $this->next();
    }

    /**
     * Asks for the next set of data to use.
     * Also detects when anything but a number is introduced, to re-ask for any id's.
     */
    public function next() {
        $record = new stdClass();

        //ask for the source user id.
        $record->userid = 0;
        while ($record->userid <= 0 && $record->userid != -1) {
            $record->userid = intval(cli_input(get_string('cligathering:fromid', 'tool_multitenantuser')));
        }

        //if we have to exit, do it now
        if ($record->userid == -1) {
            $this->end = true;
            return;
        }

        //otherwise, ask for the tenant id
        $record->tenantid = 0;
        while ($record->tenantid <= 0 && $record->tenantid != -1) {
            $record->tenant = intval(get_string('cligathering:tenantid', 'tool_multitenantuser'));
        }

        //updates related to iterator fields.
        $this->end = $record->tenantid == -1;
        $this->current = $record;
        $this->index++;
    }

    /**
     * Tells whether to conclude iteration.
     * @return bool true if continuing iteration, false to conclude
     */
    public function valid() {
        return !$this->end;
    }

    /**
     * Gets the current processed user.
     * @return stdClass object with userid and tenantid fields
     */
    public function current() {
        return $this->current;
    }

    /**
     * Gets current int zero-based index.
     * @return int zero-based index value
     */
    public function key() {
        return $this->index;
    }
}