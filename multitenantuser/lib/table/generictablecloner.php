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

/**
 * Generic implementation of TableCloner
 */
class GenericTableCloner implements TableCloner {
    /**
     * Sets that, in case of conflict, data related to the new user is kept.
     * When false, data remains unchanged.
     * @var int
     */
    protected $newidtomaintain;

    public function __construct() {
        $this->newidtomaintain = get_config('uniquekeynewidtomaintain', 'tool_multitenantuser');
    }

    /**
     * @return array List of database table names without the $CFG->prefix.
     * Returns an empty array when nothing to get.
     */
    public function getTablesToSkip() {
        return array();
    }

    /**
     * Clones the records related to the user given in $data,
     * updating/appending the list of $errorMessages and $actionLog.
     *
     * @param array $data array with the necessary data for cloning.
     * @param array $errorMessages list of error messages.
     * @param array $actionLog list of action performed.
     * @throws dml_exception
     */
    public function cloneUser($data, &$errorMessages, &$actionLog) {
        global $CFG, $DB;

        foreach ($data['userFields'] as $fieldName) {
            $recordsToUpdate = $DB->get_records_sql("SELECT " . self::PRIMARY_KEY .
                    " FROM " . $CFG->prefix . $data['tableName'] . " WHERE " .
                    $fieldName . " = '" . $data['fromid'] . "'");
            if (count($recordsToUpdate) == 0) {
                //this userid is not present in these table and field names
                continue;
            }

            $keys = array_keys($recordsToUpdate); //get the 'id' field from the result set
            $recordsToModify = array_combine($keys, $keys);

            if (isset($data['compoundIndex'])) {
                $this->mergeCompoundIndex($data, $fieldName,
                    $this->getOtherFieldsOnCompoundIndex($fieldName, $data['compoundIndex']), $recordsToModify,
                    $actionLog, $errorMessages);
            }

            $this->updateRecords($data, $recordsToModify, $fieldName, $actionLog, $errorMessages);
        }
    }

    /**
     * This function extracts the records' ids that have to be updated to the $newId
     *
     * @global object $CFG
     * @global moodle_database $DB
     * @param array $data array with the details of cloning.
     * @param string $userfield table's field name that refers to the user id.
     * @param array $otherfields table's field names that refer to the other info in the compound index.
     * @param array $recordsToModify array with the current $table's id to update.
     * @param array $actionLog array where to append the list of actions done.
     * @param array $errorMessages array where to append any error messages.
     * @throws dml_exception
     */
    protected function mergeCompoundIndex($data, $userfield, $otherfields, &$recordsToModify, &$actionLog,
                &$errorMessages) {
        global $CFG, $DB;

        $otherfieldsstr = implode(', ', $otherfields);
        // IN CASE OF tableName = mdl_badge_issued
        // SELECT * FROM mdl_badge_issued WHERE userid in (user id number)
        $sql = 'SELECT * FROM ' . $CFG->prefix . $data['tableName'] .
                ' WHERE ' . $userfield . ' = ' . $data['userid'];
        $result = $DB->get_records_sql($sql);
    }

    /**
     * Gets the values of all columns for the table.
     *
     * @param array $data array with the details of cloning
     * @param string $userfield column name that refers to the user id.
     * @return array with values of all columns
     * @throws dml_exception
     */
    protected function getRecordValues($data, $userfield) {
        global $CFG, $DB;

        $sql = 'SELECT * FROM ' . $CFG->prefix . $data['tableName'] .
                ' WHERE ' . $userfield . ' = ' . $data['userid'];
        return $DB->get_records_sql($sql);
    }

    /**
     * Updates the table, adding a new user.id with all records
     * specified by the ids on $recordsToModify.
     *
     * @global object $CFG
     * @global moodle_database $DB
     *
     * @param array $data array with the details of cloning.
     * @param array $actionLog list of performed actions.
     * @param array $errorMessages list of error messages.
     */
    protected function createNewRecords($data, &$actionLog, &$errorMessages) {
        global $CFG, $DB;

        $fields = implode(', ', $data['otherFields']);

        $newRecords = "INSERT INTO " . $CFG->prefix . $data['tableName'] .
            " (" . $fields . ") VALUES (" . //imploded list of values from getRecordValues
            ")";
    }


}