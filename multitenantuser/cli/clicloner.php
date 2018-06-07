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
define("CLI_SCRIPT", true);

require_once __DIR__ . '/../../../../config.php';

ini_set('display_errors', true);
ini_set('error_reporting', E_ALL | E_STRICT);

global $CFG;

require_once $CFG->dirroot . '/lib/clilib.php';
require_once __DIR__ . '/../lib/autoload.php';

list($options, $unrecognized) = cli_get_params(
    array(
        'debugdb' => false,
        'alwaysRollback' => false,
        'help' => false,
    )
);

if ($unrecognized) {
    $unrecognized = implode("\n ", $unrecognized);
    cli_error(get_string('cliunknownoption', 'admin', $unrecognized), 2);
}

if ($options['help']) {
    $help =
        "Command Line user cloner. These are the available options:

        Options:
        --help            Print out this help
        --debugdb         Output all db statements used to do the merge
        --alwaysRollback  Do the full merge but rollback the transaction at the last opportunity
        ";

    echo $help;
    exit(0);
}

// load current config
$config = tool_multitenantuser_config::instance();

$config->debugdb = !empty($options['debugdb']);
$config->alwaysRollback = !empty($options['alwaysRollback']);

// init cloning tool
$mtt = new MultiTenantTool($config);
$cloner = new Cloner($mtt);

// init gathering instance
$gatheringname = $config->gathering;
$gathering = new $gatheringname;

// collect and perform cloning
$cloner->cloneUser($gathering);

exit(0);