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

class multitenantform extends moodleform {

    public function definition(){
        global $CFG;

        $mform =& $this->_form;

        $searchfields = array(
            'idnumber' => get_string('idnumber'),
            ''          => get_string('all'),
            'id'        => 'Id',
            'username'  => get_string('username'),
            'firstname' => get_string('firstname'),
            'lastname'  => get_string('lastname'),
            'email'     => get_string('email'),
        );

        $mform->addElement('header', 'multitenantuser', get_string('header', 'tool_multitenantuser'));

        $searchuser = array();
        $searchuser[] = $mform->createElement('text', 'searchargs');
        $searchuser[] = $mform->createElement('select', 'searchfield', '', $searchfields, '');
        $mform->addGroup($searchuser, 'searchuser', get_string('searchuser', 'tool_multitenantuser'));
        $mform->setType('searchgroup[searcharg]', PARAM_TEXT);
        $mform->addHelpButton('searchgroup', 'searchuser', 'tool_multitenantuser');

        $this->add_action_buttons(false, get_string('search'));
    }
}