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

class TenantSelectTable extends html_table implements renderable {

    /** @var tool_multitenantuser_renderer $renderer */
    protected $renderer;

    /**
     * Call parent constructor
     *
     * @param array $tenants
     * @param tool_multitenantuser_renderer $renderer
     * @throws coding_exception
     * @throws moodle_exception
     */
    public function __construct($tenants, $renderer) {
        parent::__construct();
        $this->renderer = $renderer;
        $this->buildtable($tenants);
    }

    /**
     * Build the tenant select table using the extension of html_table
     *
     * @param array $tenants array of tenant results
     * @throws coding_exception
     * @throws moodle_exception
     */
    protected function buildtable($tenants) {
        // reset any existing data
        $this->data = array();

        $this->id = 'multitenant_user_tool_tenant_select_table';
        $this->attributes['class'] = 'generaltable boxaligncenter';

        $columns = array(
            'col_select_tenant' => get_string('tenants', 'tool_multitenantuser'),
            'col_tenantid' => 'ID',
            'col_name' => 'Company',
        );

        $this->head = array_values($columns);
        $this->colclasses = array_keys($columns);

        foreach ($tenants as $tenantid => $tenant) {
            $row = array();
            $row[] = html_writer::empty_tag('input', array('type' => 'radio', 'name' => 'tenant', 'value' => $tenantid, 'id' => 'tenant' . $tenantid));
            $row[] = html_writer::tag('span', $tenantid->id);
            $row[] = html_writer::tag('span', $tenant);
        }
    }
}