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
 * Plugin strings are defined here.
 *
 * @package     tool_multitenantuser
 * @category    string
 * @copyright   2018 Owen Tolman <owen@accenagroup.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['TEMP'] = 'TEMPORARY PLACEHOLDER STRING';

$string['pluginname'] = 'multitenantuser';
$string['header'] = 'List a user under multiple tenants';
$string['header_help'] =
    '<p>Given a user and a tenant, this plugin will attempt to clone the user under the tenant. 
    This is done to allow managers in multiple tenants to see course the completion status of 
    a single user.</p>';
$string['addtenants'] = 'Add tenants to user.';
$string['addtenants_confirm'] = 'User will be added to tenants.<br />Are you sure you want to continue?';
$string['privacy:metadata'] = 'The Multi Tenant User plugin does not store any personal data.';
$string['searchuser'] = 'Search for User';
$string['searchuser_help'] = 'Enter a username, first/last name, email address
    or user id to search for potential users. You may also specify if you only
    want to search through a particular field.';
$string['userselecttable_legend'] = '<b>Select User to add tenants</b>';
$string['saveselection_submit'] = 'Save selection';
$string['userreviewtable_legend'] = '<b>Review User and Tenants</b>';
$string['saveselection_submit'] = 'Save selection';
$string['clear_selection'] = 'Clear current selection';
$string['user'] = 'User';
$string['tenants'] = 'Tenants';
$string['cligathering:description'] = "Introduce pairs of user's id to merge the first one into the\n
second one. The first user id (fromid) will 'lose' all its data to be 'migrated'\n
into the second one (toid). The user 'toid' will include data from both users.";
$string['cligathering:stopping'] = 'To stop merging, Ctrl+C or type -1 either on fromid or toid fields.';
$string['cligathering:fromid'] = 'User id (fromid):';
$string['cligathering:tenantid'] =   'Tenant id   (tenantid):';
$string['adding'] = 'Tenants added.';
$string['useraddingheader'] = '&laquo;{$a->username}&raquo; (user ID = {$a->id})';
$string['into'] = 'tenants added to';
$string['logid'] = 'For further reference, these results are recorded in the log id {$a}.';
$string['tableok'] = 'Table {$a} : update OK';
$string['tableko'] = 'Table {$a} : update NOT OK!';
$string['logok'] = 'Here are the queries that have been sent to the DB:';
$string['logko'] = 'Some error occurred:';
$string['dbok'] = 'Merge successful';
$string['dbko_transactions'] = '<strong>Merge failed!</strong> <br/>Your database engine
    supports transactions. Therefore, the whole current transaction has been rolled back
    and <strong>no modification has been made to your database</strong>.';
$string['dbko_no_transactions'] = '<strong>Merge failed!</strong> <br/>Your database engine
    does not support transactions. Therefore, your database <strong>has been updated</strong>.
    Your database status may be inconsistent.';
$string['errordatabase'] = 'Error: Database type {$a} not supported.';

// Progress bar
$string['choose_users'] = 'Choose user';
$string['review_users'] = 'Confirm user';
$string['results'] = 'Results and log';

// Error string
$string['error_return'] = 'Return to search form';
$string['no_saveselection'] = 'You did not select a user.';
$string['invalid_option'] = 'Invalid form option';
$string['invaliduser'] = "User doesn't exist!";

$string['viewlog'] = 'See merging logs';
$string['loglist'] = 'All these records are merging actions done, showing if they went ok:';
$string['newuseridonlog'] = 'User kept';
$string['olduseridonlog'] = 'User removed';
$string['nologs'] = 'There is no merging logs yet. Good for you!';
$string['wronglogid'] = 'The log you are asking for does not exist.';
$string['deleted'] = 'User with ID {$a} was deleted';
$string['errortransactionsonly'] = 'Error: transactions are required, but your database type {$a}
    does not support them. If needed, you can allow merging users without transactions.
    Please, review plugin settings to set up them accordingly.';
$string['eventuserclonedsuccess'] = 'Cloning success';
$string['eventuserclonedfailure'] = 'Cloning failed';

$string['starttime'] = 'Started at {$a}';
$string['finishtime'] = 'Finished at {$a}';
$string['timetaken'] = 'Took {$a} seconds';
$string['uniquekeynewidtomaintain'] = 'Keep new user\'s data';
$string['uniquekeynewidtomaintain_desc'] = 'In case of conflict, 
    like when the user.id related column is a unique key, this plugin will keep 
    data from new user (by default). This also means that data from old user is 
    deleted to keep the consistence. Otherwise, if you uncheck this option, 
    data from old user will be kept.';