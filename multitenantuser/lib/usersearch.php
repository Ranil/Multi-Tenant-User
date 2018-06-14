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
defined('MOODLE_INTERNAL') || die();

require_once dirname(dirname(dirname(dirname(__DIR__)))) . '/config.php';

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';
require_once __DIR__ . '/autoload.php';

class MultiTenantSearch {

    /**
     * Search for users from user table based on input.
     *
     * @param mixed $input
     * @param string $searchfield
     * @return array $results
     */
    public function search_users($input, $searchfield) {
        global $DB;
        echo "SEARCHING FOR USERS WITH INPUT " . $input . " AND FIELD " . $searchfield;
        switch ($searchfield) {
            case 'id':
                $params = array(
                    'userid' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM `mdl_user` WHERE id LIKE ' . $input;
                break;
            case 'username':
                $params = array(
                    'username' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE username LIKE :username';
                break;
            case 'firstname':
                $params = array(
                    'firstname' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE firstname LIKE :firstname';
                break;
            case 'lastname':
                $params = array(
                    'lastname' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE lastname LIKE :lastname';
                break;
            case 'email':
                $params = array(
                    'email' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {user} WHERE email LIKE :email';
                break;
            default:
                $params = array(
                    'userid'    => '%' . $input . '%',
                    'username'  => '%' . $input . '%',
                    'firstname' => '%' . $input . '%',
                    'lastname'  => '%' . $input . '%',
                    'email'     => '%' . $input . '%',
                    'idnumber'  => '%' . $input . '%',
                );
                $sql =
                    'SELECT *
                    FROM {user}
                    WHERE
                        id LIKE :userid OR 
                        username LIKE :username OR 
                        firstname LIKE :firstname OR 
                        lastname LIKE :lastname OR 
                        email LIKE :email OR 
                        idnumber LIKE :idnumber';

                break;
        }

        $ordering = ' ORDER BY lastname, firstname';

        $results = $DB->get_records_sql($sql . $ordering, $params);
        return $results;
    }

    /**
     * Search for tenants from company table based on input.
     *
     * @param mixed $input
     * @param string $searchfield
     * @return array $results
     */
    public function search_tenants($input, $searchfield) {
        global $DB;

        switch ($searchfield) {
            case 'id':
                $params = array(
                    'id' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {company} WHERE id LIKE :id';
                break;
            case 'name':
                $params = array(
                    'name' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {company} WHERE name LIKE :name';
                break;
            case 'shortname':
                $params = array(
                    'shortname' => '%' . $input . '%',
                );
                $sql = 'SELECT * FROM {company} WHERE shortname LIKE :shortname';
            default:
                $params = array(
                    'id'        => '%' . $input . '%',
                    'name'      => '%' . $input . '%',
                    'shortname' => '%' . $input . '%',
                );
                $sql =
                    "SELECT *
                    FROM {company}
                    WHERE
                        id LIKE :id OR 
                        name LIKE :name OR 
                        shortname LIKE :shortname";

                break;
        }

        $ordering = ' ORDER BY name';

        $results = $DB->get_records_sql($sql . $ordering, $params);
        return $results;
    }

    /**
     * @param $uinfo
     * @param $column
     * @return array
     */
    public function verify_user($uinfo, $column) {
        global $DB;
        $message = '';
        try {
            $user = $DB->get_record('user', array($column => $uinfo), '*', MUST_EXIST);
        } catch (Exception $e) {
            $message = get_string('invaliduser', 'tool_multitenantuser') . '('.$column.'=>'.$uinfo.'): '. $e->getMessage();
            $user = NULL;
        }
        return array($user, $message);
    }

    /**
     * @param $tinfo
     * @param $column
     * @return array
     */
    public function verify_tenant($tinfo, $column) {
        global $DB;
        $message = '';
        try {
            $tenant = $DB->get_record('company', array($column => $tinfo), '*', MUST_EXIST);
        } catch (Exception $e) {
            $message = get_string('invalidtenant', 'tool_multitenantuser') . '('.$column.'=>'.$tinfo.'): '. $e->getMessage();
            $tenant = NULL;
        }
        return array($tenant, $message);
    }
}