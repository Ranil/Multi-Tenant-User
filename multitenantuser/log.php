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
require('../../../config.php');

global $CFG, $DB, $PAGE;

error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once($CFG->dirroot . '/lib/adminlib.php');
require_once('lib/autoload.php');

require_login();
require_capability('tool/multitenantuser:addtenant', context_system::instance());
admin_externalpage_setup('tool_multitenantuser_viewlog');
$id = required_param('id', PARAM_INT);

$renderer = $PAGE->get_renderer('tool_multitenantuser');
$logger = new tool_multitenantuser_logger();

$log = $logger->getDetail($id);

if( empty($log)) {
    print_error('wronglogid', 'tool_multitenantuser', new moodle_url('/admin/tool/multitenantuser/index.php'));
}

$user = $DB->get_record('user', array('id' => $log->userid), 'id, username');
if (!$user) {
    $user = new stdClass();
    $user->id = $log->userid;
    $user->username = get_string('TEMP LOG DATA, FIX ME IN LOG.PHP');
}

$tenant = $DB->get_record('company', array('id' => $log->tenantid), 'id, shortname');
if (!$tenant) {
    $tenant = new stdClass();
    $tenant->id = $log->tenantid;
    $tenant->shortname = get_string('TEMP LOG DATA, FIX ME IN LOG.PHP');
}

echo $renderer->results_page($user, $tenant, $log->success, $log->log, $log->id);
