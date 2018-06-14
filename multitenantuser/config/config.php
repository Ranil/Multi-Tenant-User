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
defined("MOODLE_INTERNAL") || die();

/**
 * This is the default settings file for the behaviour of the plugin.
 *
 * Your local Moodle instance may need additional adjustments. Please do not modify this file.
 * Instead, create or edit a file in this same directory named
 * "config.local.php" to change elements of the default configuration.
 */
return array(

    //gathering tool
    'gathering' => 'CLIGathering',

    //Database tables to be excluded from normal processing.
    'exceptions' => array(
        'user_preferences',
        'user_private_key',
        'user_info_data',
        'my_pages',
    ),

    // List of compound indexes.
    // This list may vary from Moodle instance to another, given that the Moodle version,
    // local changes and non-core plugins may add new special cases to be processed.
    // Put in 'userfield' all column names related to a user (i.e., user.id), and all the rest column names
    // into 'otherfields'.
    // See README.txt for details on special cases.
    // Table names must be without $CFG->prefix.
    'compoundindexes' => array(
        'grade_grades' => array(
            'userfield' => array('userid'),
            'otherfields' => array('itemid'),
        ),
        'course_completions' => array(
            'userfield' => array('userid'),
            'otherfields' => array('course', 'timeenrolled', 'timestarted', 'timecompleted'),
        ),
        'course_modules_completion' => array( // mdl_courmoducomp_usecou_uix (unique)
            'userfield' => array('userid'),
            'otherfields' => array('coursemoduleid', 'completionstate', 'viewed', 'timemodified'),
        ),
        'badge_issued' => array( // unique key mdl_badgissu_baduse_uix
            'userfield' => array('userid'),
            'otherfields' => array('badgeid', 'dateissued'),
        ),
        'badge_criteria_met' => array(
            'userfield' => array('userid'),
            'otherfields' => array('issueid', 'critid', 'datemet'),
        ),
        'company_users' => array(
            'userfield' => array('userid'),
            'otherfields' => array('companyid', 'managertype', 'departmentid'),
        ),
        'course_completion_crit_compl' => array(
            'userfield' => array('userid'),
            'otherfields' => array('course', 'criteriaid', 'timecompleted'),
        ),
        'quiz_grades' => array(
            'userfield' => array('userid'),
            'otherfields' => array('quiz', 'grade', 'timemodified'),
        ),
    ),

    // List of column names per table, where their content is a user.id.
    // These are necessary for matching passed by userids in these column names.
    // In other words, only column names given below will be search for matching user ids.
    // The key 'default' will be applied for any non matching table name.
    'userfieldnames' => array(
        'message_contacts' => array('userid', 'contactid'), //compound index
        'message' => array('useridfrom', 'useridto'),
        'message_read' => array('useridfrom', 'useridto'),
        'question' => array('createdby', 'modifiedby'),
        'default' => array('authorid', 'reviewerid', 'userid', 'user_id', 'id_user', 'user'), //may appear compound index
    ),

    // TableCloners to process each database table.
    // 'default' is applied when no specific TableCloner is specified.
    'tablecloners' => array(
        'default' => 'GenericTableCloner',
    ),

    'alwaysRollback' => false,
    'debugdb' => false,
);