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
require('../../../config.php');

global $CFG;
global $DB;
global $PAGE;
global $SESSION;

// Report all PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 'On');

require_once($CFG->libdir . '/blocklib.php');
require_once($CFG->libdir . '/adminlib.php');
require_once($CFG->libdir . '/accesslib.php');
require_once($CFG->libdir . '/weblib.php');

require_once('./index_form.php');

require_login();
require_capability('tool/multitenantuser:addtenant', context_system::instance());


//Get possible posted parameters
$option = optional_param('option', NULL, PARAM_TEXT);
if(!$option) {
    if(optional_param('clearselection', false, PARAM_TEXT)) {
        $option = 'clearselection';
    } else if(optional_param('addtenants', false, PARAM_TEXT)) {
        $option = 'addtenants';
    }
}

//Define form
$multitenantform = new multitenantform();
$renderer = $PAGE->get_renderer('tool_multitenantuser');

$data = $multitenantform->get_data();

$mtt = new MultiTenantTool();
$mts = new MultiTenantSearch();

if(!empty($option)) {
    switch ($option) {
        // user is selected: save session.
        case 'saveselection':

            list($user, $umessage) = $mts->verify_user(optional_param('user', NULL, PARAM_INT), 'id');
            list($tenant, $tmessage) = $mts->verify_tenant(optional_param('company', NULL, PARAM_INT), 'id');

            if($user === NULL && $tenant === NULL) {
                $renderer->mu_error(get_string('no_saveselection', 'tool_multitenantuser'));
                exit();
            }

            if(empty($SESSION->mtt)) {
                $SESSION->mtt = new stdClass();
            }

            // if session selected user already has a user and we have a "new" user, replace the session's user
            if(empty($SESSION->mtt->user) || !empty($user)) {
                $SESSION->mtt->user = $user;
            }

            // if session selected tenant already has a tenant and we have a "new" tenant, replace the session's tenant
            if(empty($SESSION->mtt->tenant) || !empty($tenant)) {
                $SESSION->mtt->tenant = $tenant;
            }

            $step = (!empty($SESSION->mtt->user) && !empty($SESSION->mtt->tenant)) ?
                $renderer::INDEX_PAGE_CONFIRMATION_STEP :
                $renderer::INDEX_PAGE_SEARCH_STEP;

            echo $renderer->index_page($multitenantform, $step);
            break;

        // remove any selected user/tenant and search for them again
        case 'clearselection':
            $SESSION->mtt = NULL;

            // redirect back to index page for new selections or review selections
            $redirecturl = new moodle_url('/admin/tool/multitenantuser/index.php');
            redirect($redirecturl, NULL, 0);
            break;

        // proceed with cloning and show results
        case 'cloneusers':
            // verify user and tenant once more just to be sure
            list($user, $umessage) = $mts->verify_user($SESSION->mtt->user->id, 'id');
            list($tenant, $tmessage) = $mts->verify_tenant($SESSION->mtt->tenant->id, 'id');
            if ($user === NULL || $tenant === NULL) {
                $renderer->mu_error($umessage . '<br />' . $tmessage);
                break;
            }

            // clone the user
            $log = array();
            $success = true;
            list($success, $log, $logid) = $mtt->cloneUser($user->id, $tenant->id);

            // reset mtt session
            $SESSION->mtt = NULL;

            // show results page
            echo $renerer->results_page($user, $tenant, $sucess, $log, $logid);
            break;

        // we have the user and tenant, but we want to change at least one
        case 'searchusers':
            echo $renderer->index_page($multitenantform, $renderer::INDEX_PAGE_SEARCH_STEP);
            break;

        // we have the user and tenant selected, and in the search step,
        // we want to proceed with the cloning of the user.
        case 'continueselection':
            echo $renderer->index_page($multitenantform, $renderer::INDEX_PAGE_CONFIRMATION_STEP);
            break;

        // an option is given but its not a valid option, something broke
        default:
            $renderer->mu_error(get_string('invalid_option', 'tool_multitenantuser'));
            break;
    }
} else if ($data) {
    // if there is a search argument, use that instead of advanced form
    if (!empty($data->searchgroup['searcharg'])) {

        $search_users = $mts->search_users($data->searchgroup['searcharg'], $data->searchgroup['searchfield']);
        $user_select_table = new UserSelectTable($search_users, $renderer);

        echo $renderer->index_page($multitenantform, $renderer::INDEX_PAGE_SEARCH_AND_SELECT_STEP, $user_select_table);

        // only run this step if there are both a userid and tenantid
    } else if (!empty($data->usergroup['userid']) && !empty($data->tenantgroup['tenantid'])) {
        // get and verify the ids from the selection form
        list($user, $umessage) = $mts->verify_user($data->usergroup['userid'], $data->usergroup['useridtype']);
        list($tenant, $tmessage) = $mts->verify_tenant($data->tenantgroup['tenantid'], $data->tenantgroup['tenantidtype']);

        if ($user === NULL || $tenant === NULL) {
            $renderer->mu_error($umessage . '<br />' . $tmessage);
            exit();
        }
        // add user and tenant to session for review step
        if(empty($SESSION->mtt)) {
            $SESSION->mtt = new stdClass();
        }
        $SESSION->mtt->user = $user;
        $SESSION->mtt->tenant = $tenant;

        echo $renderer->index_page($multitenantform, $renderer::INDEX_PAGE_SEARCH_AND_SELECT_STEP);
    } else {
        // show search form as default
        echo $renderer->index_page($multitenantform, $renderer::INDEX_PAGE_SEARCH_STEP);
    }
} else {
    // no data submitted, default to search form
    echo $renderer->index_page($multitenantform, $renderer::INDEX_PAGE_SEARCH_STEP);
}

$sql = 'SELECT * FROM mdl_user WHERE id = 96';
$result = $DB->get_records_sql($sql);

echo '<pre>';
print_r($result[96]->username);
echo '</pre>';
var_dump($result);
//ADD TOOL CLASS
//ADD SEARCH CLASS
