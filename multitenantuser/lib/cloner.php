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
require_once __DIR__ . '/autoload.php';

class Cloner {
    /**
     * @var MultiTenantTool instance of the tool.
     */
    protected $mtt;

    /**
     * Initializes the MultiTenantTool to process any incoming cloning action
     * through any Gathering instance.
     */
    public function __construct(MultiTenantTool $mtt) {
        $this->mtt = $mtt;
        $this->logger = new tool_multitenantuser_logger();

        declare(ticks = 1);

        if (extension_loaded('pcntl')) {
            pcntl_signal(SIGINT, array(
                $this,
                'aborting'
            ));
        }
    }

    /**
     * Called when aborting from command-line on Ctrl+C interruption.
     * @param int $signo only SIGINT.
     * @throws coding_exception
     */
    public function aborting($signo) {
        if (defined("CLI_SCRIPT")) {
            echo "\n\n" . get_string('ok') . ", exit!\n\n";
        }
        exit(0);
    }

    /**
     * This iterates over all cloning actions from the given Gathering instance and tries
     * to perform it. The result of every action is logged internally for future checking.
     * @param Gathering $gathering List of cloning actions.
     * @throws coding_exception
     * @throws dml_exception
     */
    public function cloneUser(Gathering $gathering) {
        foreach($gathering as $action) {
            list($success, $log, $id) = $this->mtt->cloneUser($action->userid, $action->tenantid);

            if (defined("CLI_SCRIPT")) {
                echo (($success)?get_string('success'):get_string('error')) . ". Log id: " . $id . "\n\n";
            }
        }
        if (defined("CLI_SCRIPT")) {
            echo get_string('ok') . ", exit!\n\n";
        }
    }
}