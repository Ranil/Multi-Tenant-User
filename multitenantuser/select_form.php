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

require_once($CFG->libdir.'/formslib.php'); /// forms library

class select_form extends moodleform {

    /** @var UserSelectTable Table to select users. */
    protected $ust;

    public function __construct(UserSelectTable $ust = NULL)
    {
        //just before parent's construct
        $this->ust = $ust;
        parent::__construct();
    }

    public function definition() {

        $mform =& $this->_form;

        // header
        $mform->addElement('header', 'selectuser', get_string('userselecttable_legend', 'tool_multitenantuser'));

        // table content - list users relevant to search
        $mform->addElement('static', 'selectuserlist', '', html_writer::table($this->ust));

        // hidden elements
        $mform->addElement('hidden', 'option', 'saveuselection');
        $mform->setType('option', PARAM_RAW);
        $mform->addElement('hidden', 'selecteduser', '');
        $mform->setType('selecteduser', PARAM_RAW);

        $this->add_action_buttons(false, get_string('saveuselection_submit', 'tool_multitenantuser'));
    }
}