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
 * @category    string
 * @copyright   2018 Owen Tolman <owen@accenagroup.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
require('../../../config.php');

global $CFG;
global $PAGE;
global $SESSION;

// Report all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->libdir . '/weblib.php');

require_login();
require_cabability('tool/multitenantuser:addtenant', context_system::instance());

//Get possible posted parameters
$option = optional_param('option', NULL, PARAM_TEXT);
if(!$option) {
    if(optional_param('clearselection', false, PARAM_TEXT)) {
        $option = 'clearselection';
    } else if(optional_param('addtenants', false, PARAM_TEXT)) {
        $option = 'addtenants';
    }
}

//Define form
$multitenantform = new multitenantform();
$renderer = $PAGE->get_renderer('tool_multitenantuser');

$data = $multitenantform->get_data();

//ADD TOOL CLASS
//ADD SEARCH CLASS
