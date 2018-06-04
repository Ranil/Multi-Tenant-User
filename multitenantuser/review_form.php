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
require_once($CFG->libdir . '/formslib.php'); /// forms library

/**
 * Define the form to confirm adding tenants to user
 */
class reviewuserform extends moodleform {

    /** @var UserSelectTable Table to select users. */
    protected $urt;

    /** @var renderer_base renderer */
    protected $output;

    /** @var bool if user is in the merge process step. */
    protected $review_step;

    public function __construct(UserReviewTable $urt, $renderer, $review_step)
    {
        //just before parent's construct
        $this->urt = $urt;
        $this->output = $renderer;
        $this->review_step = $review_step;
        parent::__construct();
    }

    public function definition(){
        global $CFG;

        // if there are no rows in the table, return.
        if (empty($this->urt->data)) {
            return '';
        }

        $mform = & $this->_form;

        //header
        $mform->addElement('header', 'reviewuser', get_string('userreviewtable_legend', 'tool_multitenantuser'));

        //table content
        $mform->addElement('static', 'reviewuserlist', '', html_writer::table($this->urt));

        //buttons
        $indexurl = new moodle_url('/admin/tool/multitenantuser/index.php');
        $buttonarray = array();
        if($this->review_step) {
            $indexurl->param('option', 'addtenants');
            $addtenantsbutton = new single_button($indexurl, get_string('addtenants', 'tool_multitenantuser'));
            $addtenantsbutton->add_confirm_action(get_string('addtenants_confirm', 'tool_multitenantuser'));
            $buttonarray[0][] = $this->output->render($addtenantsbutton);
        } else if (count($this->urt->data) === 2) {
            $indexurl->param('option', 'continueselection');
            $addtenantsbutton = new single_button($indexurl, get_string('saveselection_submit', 'tool_multitenantuser'));
            $buttonarray[0][] = $this->output->render($addtenantsbutton);
        }
        $indexurl->param('option', 'clearselection');
        $addtenantsbutton = new single_button($indexurl, get_string('clear_selection', 'tool_multitenantuser'));
        $buttonarray[0][] = $this->output->render($addtenantsbutton);

        if($this->review_step) {
            $indexurl->param('option', 'searchusers');
            $addtenantsbutton = new single_button($indexurl, get_string('cancel'));
            $buttonarray[0][] = $this->output->render($addtenantsbutton);
        }
        $htmltable = new html_table();
        $htmltable->attributes['class'] = 'clearfix';
        $htmltable->data = $buttonarray;

        $mform->addElement('static', 'buttonar', '', html_writer::table($htmltable));
        $mform->closeHeaderBefore('buttonar');
    }
}