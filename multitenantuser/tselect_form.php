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
require_once($CFG->libdir.'/formslib.php');

class tselect_form extends moodleform {

    /** @var TenantSelectTable Table to select tenants */
    protected $tst;

    public function __construct(TenantSelectTable $tst = NULL) {
        $this->tst = $tst;
        parent::__construct();
    }

    public function definition() {
        global $CFG;

        $mform =& $this->_form;

        //header
        $mform->addElement('header', 'selecttenant', get_string('tenantselecttable_legend', 'tool_multitenantuser'));

        // table content - list tenants relevant to search
        $mform->addElement('static', 'selectenantlist', '', html_writer::table($this->tst));

        // hidden elements
        $mform->addElement('hidden', 'option', 'savetselection');
        $mform->setType('option', PARAM_RAW);
        $mform->addElement('hidden', 'selectedtenant', '');
        $mform->setType('selectedtenant', PARAM_RAW);

        $this->add_action_buttons(false, get_string('savetselection_submit', 'tool_multitenantuser'));
    }
}