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
defined('MOODLE_INTERNAL') || die();

require_once(dirname(dirname(dirname(dirname(__DIR__)))) . '/config.php');

global $CFG;

// require needed library files
require_once($CFG->dirroot . '/lib/clilib.php');
require_once(__DIR__ . '/autoload.php');
require_once($CFG->dirroot . '/lib/outputcomponents.php');

class UserReviewTable extends html_table implements renderable {

    /** @var stdClass $user The user db object */
    protected $user;

    /** @var stdClass $tenants The tenants db object */
    protected $tenants;

    /** @var bool $showaddbutton Whether or not to show the add tenants button on rendering */
    protected $showaddbutton = false;

    /** @var tool_multitenantuser_renderer Render to help showing user info. */
    protected $renderer;

    /**
     * Call parent construct and then build table
     * @param tool_multitenantuser_renderer $renderer
     */
    public function __construct($renderer)
    {
        global $SESSION;

        $this->renderer = $renderer;

        // Call parent constructor
        parent::__construct();

        if (!empty($SESSION->mtt)) {
            if (!empty($SESSION->mtt->user)) {
                $this->user = $SESSION->mtt->user;
            }
            if (!empty($SESSION->mtt->tenants)) {
                $this->tenants = $SESSION->mtt->tenants;
            }
        }
        $this->buildtable();
    }

    /**
     * Build the user select table using the extension of html_table
     */
    protected function buildtable() {
        // Reset any existing data
        $this->data = array();

        if(!empty($this->user) || !empty($this->tenants)) {
            $this->id = 'multitenant_user_tool_user_review_table';
            $this->attrubutes['class'] = 'generaltable boxaligncenter';

            if((isset($this->user->idnumber) && !empty($this->user->idnumber))) {
                $extrafield = 'idnumber';
            } else {
                $extrafield = 'description';
            }
            $columns = array(
                'col_label' => '',
                'col_userid' => 'Id',
                'col_username' => get_string('user'),
                'col_email' => get_string('email'),
                'col_extra' => get_string($extrafield)
            );
            $this->head = array_values($columns);
            $this->colclasses = array_keys($columns);

            $userrow = array();
            $userrow[] = get_string('user', 'tool_multitenantuser');
            if(!empty($this->user)) {
                $userrow[] = $this->user->id;
                $userrow[] = $this->renderer->show_user($this->user->id, $this->user);
                $userrow[] = $this->user->email;
                $userrow[] = $this->user->$extrafield;
            } else {
                $userrow[] = '';
                $userrow[] = '';
                $userrow[] = '';
                $userrow[] = '';
            }
            $this->data[] = $userrow;

            $tenantsrow = array();
            $tenantsrow[] = get_string('tenants', 'tool_multitenantsuser');
            if(!empty($this->tenants)) {
                // TODO: Iterate through tenants array
            }
        }
    }
}