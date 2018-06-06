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
 *
 */
interface TableCloner {
    const PRIMARY_KEY = 'id';

    /**
     * @return array List of database table names without the $CFG->prefix.
     * Returns an empty array when nothing to get.
     */
    public function getTablesToSkip();

    /**
     * Clones the records related to the user given in $data,
     * updating/appending the list of $errorMessages and $actionLog.
     *
     * @param array $data array with the necessary data for cloning.
     * @param array $errorMessages list of error messages.
     * @param array $actionLog list of action performed.
     */
    public function cloneUser($data, &$errorMessages, &$actionLog);
}