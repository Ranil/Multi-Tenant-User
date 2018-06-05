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
require_once __DIR__ . '/../../../../config.php';

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';

/**
 * Class to manage logging actions.
 * General log table cannot be used for log.info field length restrictions.
 */
class tool_multitenantuser_logger {

    /**
     * @param int $userid user.id for the user to be cloned.
     * @param int $tenantid company.id for the company to be cloned to.
     * @param bool $success true if cloning action was ok; false otherwise.
     * @param array $log list of actions performed for a successful cloning;
     * or a problem description if failed.
     * @return
     */
    public function log($userid, $tenantid, $success, $log) {
        global $DB;

        $record = new stdClass();
        $record->userid = $userid;
        $record->tenantid = $tenantid;
        $record->timemodified = time();
        $record->success = (int)$success;
        $record->log = json_encode($log);

        try {
            return $DB->insert_record('tool_multitenantuser', $record, true);
        } catch (Exception $e) {
            $msg = __METHOD__ . ' : Cannot insert new record on log. Reason: "' . $DB->get_last_error() .
                '". Message: "' . $e->getMessage() . '" Trace' . $e->getTraceAsString();
            if(CLI_SCRIPT) {
                cli_error($msg);
            } else {
                print_error($msg, null, new moodle_url('/admin/tool/multitenantuser/index.php'));
            }
        }
    }

    /**
     * Gets the logs
     * @param array $filter associative array with conditions to match for getting results.
     * If empty, this will return all logs.
     * @param int $limitfrom starting number of record to get. 0 to get all.
     * @param int $limitnum maximum number of records to get. 0 to get all.
     * @param string $sort SQL ordering, defaults to "timemodified DESC"
     * @return
     */
    public function get($filter = null, $limitfrom = 0, $limitnum = 0, $sort = "timemodified DESC") {
        global $DB;
        $logs = $DB->get_records('tool_multitenantuser', $filter, $sort, 'id, userid, tenantid, success, timemodified', $limitfrom, $limitnum);
        if(!$logs) {
            return $logs;
        }
        foreach ($logs as $id=> &$log) {
            $log->user = $DB->get_record('user', array('id' => $log->userid));
            $log->tenant = $DB->get_record('company', array('id' => $log->tenantid));
        }
        return $logs;
    }

    /**
     * Get the whole detail of a log id.
     * @param int $logid
     * @return stdClass the whole record related to the $logid
     */
    public function getDetail($logid) {
        global $DB;
        $log = $DB->get_record('tool_multitenantuser', array('id' => $logid), '*', MUST_EXIST);
        $log->log = json_decode($log->log);
        return $log;
    }
}