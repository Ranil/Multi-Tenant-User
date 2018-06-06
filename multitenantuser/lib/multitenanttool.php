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

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/config.php';

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';
require_once __DIR__ . '/autoload.php';
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/mergeusers/lib.php');

class MultiTenantTool {

    /**
     * @var bool true if current database is supported; false otherwise.
     */
    protected $supportedDatabase;

    /**
     * @var array associative array showing the user-related fields per database table,
     * without the $CFG->prefix on each.
     */
    protected $userFieldsPerTable;

    /**
     * @var array string array with all known database table names to skip in analysis,
     * without the $CFG->prefix on each.
     */
    protected $tablesToSkip;

    /**
     * @var array string array with the current skipped tables with the $CFG->prefix on each.
     */
    protected $tablesSkipped;

    /**
     * @var array associative array with special cases for tables with compound indexes,
     * without the $CFG->prefix on each.
     */
    protected $tablesWithCompoundIndex;

    /**
     * @var string Database-specific SQL to get the list of database tables.
     */
    protected $sqlListTables;

    /**
     * @var array array with table names (without $CFG->prefix) and the list of field names
     * that are related to user.id. The key 'default' is the default for any non matching table name.
     */
    protected $userFieldNames;

    /**
     * @var tool_multitenantuser_logger logger for cloning users.
     */
    protected $logger;

    /**
     * @var array associative array (tablename => classname) with the
     * TableMerger tools to process all database tables.
     */
    protected $tableMergers;

    /**
     * @var array list of table names processed ny TableMerger's.
     */
    protected $tablesProcessedByTableMergers;

    /**
     * @var bool if true then never commit the transaction, used for testing.
     */
    protected $alwaysRollback;

    /**
     * @var bool if true then write out all sql, used for testing.
     */
    protected $debugdb;

    /**
     * Initializes
     * @param tool_multitenantuser_config $config local configuration.
     * @param tool_multitenantuser_logger $logger logger facility to save results
     */
    public function __construct(tool_multitenantuser_config $config = null, tool_multitenantuser_logger $logger = null) {
        global $CFG;

        $this->logger = (is_null($logger)) ? new tool_multitenantuser_logger() : $logger;
        $config = (is_null($config)) ? tool_multitenantuser_config::instance() : $config;
        $this->supportedDatabase = true;

        $this->checkTransactionSupport();

        switch($CFG->dbtype) {
            case 'sqlsrv':
            case 'mssql':
                $this->sqlListTables = "SELECT name FROM sys.Tables WHERE name LIKE '" .
                    $CFG->prefix . "%' AND table_schema = 'public'";
                break;
            case 'mysqli':
            case 'mariadb':
                $this->sqlListTables = 'SHOW TABLES like "' . $CFG->prefix . '%"';
                break;
            case 'pgsql':
                $this->sqlListTables = "SELECT table_name FROM information_schema.tables WHERE table_name LIKE '" .
                    $CFG->prefix . "%' AND table_schema = 'public'";
                break;
            default:
                $this->supportedDatabase = false;
                $this->sqlListTables = '';
        }

        //these are tables we don't want to modify due to logging or security reasons.
        //we flip key<-->value to accelerate lookups.
        $this->tablesToSkip = array_flip($config->exceptions);
        $excluded = explode(',', get_config('tool_multitenantuser', 'excluded_exceptions'));
        $excluded = array_flip($excluded);
        if (!isset($excluded['none'])) {
            foreach ($excluded as $exclude => $nonused) {
                unset($this->tablesToSkip[$exclude]);
            }
        }

        //these are special cases, corresponding to tables with compound indexes that
        //need special treatment.
        $this->tablesWithCompoundIndex = $config->compoundindexes;

        //Initializes user-related field names.
        $userFieldNames = array();
        foreach ($config->userfieldnames as $tablename => $fields) {
            $userFieldNames[$tablename] = "'" . implode("','", $fields . "'");
        }
        $this->userFieldNames = $userFieldNames;

        // Load available TableMerger tools.
        $tableMergers = array();
        $tablesProcessedByTableMergers = array();
        foreach ($config->tablemergers as $tableName => $class) {
            $tm = new $class();
            //ensure any provided class is a class of TableMerger
            if(!$tm instanceof TableMerger) {
                //aborts execution by showing an error.
                if(CLI_SCRIPT) {
                    cli_error('Error: ' . __METHOD__ . ':: ' . get_string('notablemergerclass', 'tool_multitenantuser',
                                    $class));
                } else {
                    print_error('notablemergerclass', 'tool_multitenantuser',
                        new moodle_url('/admin/tool/multitenantuser/index.php'), $class);
                }
            }
            //append any additional tables to skip.
            $tablesProcessedByTableMergers = array_merge($tablesProcessedByTableMergers, $tm->getTablesToSkip());
            $tableMergers[$tableName] = $tm;
        }
        $this->tableMergers = $tableMergers;
        $this->tablesProcessedByTableMergers = array_flip($tablesProcessedByTableMergers);

        $this->alwaysRollback = !empty($config->alwaysRollback);
        $this->debugdb = !empty($config->debugdb);

        //this will abort execution if local database is not supported.
        $this->checkDatabaseSupport();

        //initializes the list of fields and tables to check in the current database,
        //given the local configuration.
        $this->init();
    }

    /**
     * Clone a user under a new tenant. User-related data records are kept when cloned
     * @global object $CFG
     * @global moodle_database $DB
     * @param int $userid The user to clone
     * @param int $tenantid The tenant to clone to
     * @return array An array(bool, array, int) having the following cases:
     * if array(true, log, id) cloning was successful and log contains all actions done;
     * if array(false, errors, id) cloning was aborted and errors contain the list of errors.
     * The last id is the log id of the cloning action for later visual revision.
     * @throws dml_exception
     */
     public function cloneUser($userid, $tenantid) {
         list($success, $log) = $this->_cloneUser($userid, $tenantid);

         $eventpath = "\\tool_multitenantuser\\event\\";
         $eventpath .= ($success) ? "user_cloned_success" : "user_cloned_failure";

         $event = $eventpath::create(array(
             'context' => \context_system::instance(),
             'other' => array(
                 'entitiesinvolved' => array(
                     'userid' => $userid,
                     'tenantid' => $tenantid,
                 ),
                 'log' => $log,
             ),
         ));
         $event->trigger();
         $logid = $this->logger->log($userid, $tenantid, $success, $log);
         return array($success, $log, $logid);
     }

     /**
      * Real method that performs the cloning action.
      * @global object $CFG
      * @global moodle_database $DB
      * @param int $userid The user to clone
      * @param int $tenantid The tenant to clone to
      * @return array An array(bool, array) having the following cases:
      * if array(true, log) cloning was successful and log contains all actions done;
      * if array(false, errors) cloning was aborted and errors contains the list of errors.
      */
     private function _cloneUser($userid, $tenantid) {
         global $CFG, $DB;

         //initial checks.
         //database type supported?
         if (!$this->supportedDatabase) {
             return array(false, array(get_string('errordatabase', 'tool_multitenantuser', $CFG->dbtype)));
         }

         //initialization
         $errorMessages = array();
         $actionLog = array();

         if($this->debugdb) {
             $DB->set_debug(true);
         }

         $startTime = time();
         $startTimeString = get_string('starttime', 'tool_multitenantuser', userdate($startTime));
         $actionLog[] = $startTimeString;

         $transaction = $DB->start_delegated_transaction();

         try {

         }
     }
}
