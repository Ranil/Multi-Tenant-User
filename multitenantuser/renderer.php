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

require_once __DIR__ . '/select_form.php';
require_once __DIR__ . '/review_form.php';
require_once __DIR__ . '/tselect_form.php';
require_once($CFG->dirroot . '/'.$CFG->admin.'/tool/multitenantuser/lib.php');

class tool_multitenantuser_renderer extends plugin_renderer_base {

    /** On index page, show only the user search form. */
    const INDEX_PAGE_SEARCH_USER = 1;
    /** On index page, show both user search and user select forms. */
    const INDEX_PAGE_SEARCH_AND_SELECT_USER = 2;
    /** On index page, show only tenant search form. */
    const INDEX_PAGE_SEARCH_TENANT = 3;
    /** On index page, show both tenant search and tenant select forms. */
    const INDEX_PAGE_SEARCH_AND_SELECT_TENANT = 4;
    /** On index page, show only the review list */
    const INDEX_PAGE_CONFIRMATION_STEP = 5;
    /** On index page, show the results. */
    const INDEX_PAGE_RESULTS_STEP = 6;

    /**
     * Renderers a progress bar.
     * @param array $items An array of items
     * @return string
     */
    public function progress_bar(array $items)
    {
        foreach ($items as &$item) {
            $text = $item['text'];
            unset($item['text']);
            if (array_key_exists('link', $item)) {
                $link = $item['link'];
                unset($item['link']);
                $item = html_writer::link($link, $text, $item);
            } else {
                $item = html_writer::tag('span', $text, $item);
            }
        }
        return html_writer::tag('div', join(get_separator(), $items), array('class' => 'progress clearfix'));
    }

    /**
     * Returns the HTML for the progress bar, according to the current step.
     * @param int $step current step
     * @return string HTML for the progress bar.
     */
    public function build_progress_bar($step)
    {
        $steps = array(
            array('text' => '1. ' . get_string('searchuser', 'tool_multitenantuser')),
            array('text' => '2. ' . get_string('chooseuser', 'tool_multitenantuser')),
            array('text' => '3. ' . get_string('searchtenant', 'tool_multitenantuser')),
            array('text' => '4. ' . get_string('choosetenant', 'tool_multitenantuser')),
            array('text' => '5. ' . get_string('review', 'tool_multitenantuser')),
            array('text' => '6. ' . get_string('results', 'tool_multitenantuser')),
        );

        switch ($step) {
            case self::INDEX_PAGE_SEARCH_USER:
                $steps[0]['class'] = 'bold';
                break;
            case self::INDEX_PAGE_SEARCH_AND_SELECT_USER:
                $steps[1]['class'] = 'bold';
                break;
            case self::INDEX_PAGE_SEARCH_TENANT:
                $steps[2]['class'] = 'bold';
                break;
            case self::INDEX_PAGE_SEARCH_AND_SELECT_TENANT:
                $steps[3]['class'] = 'bold';
                break;
            case self::INDEX_PAGE_CONFIRMATION_STEP:
                $steps[4]['class'] = 'bold';
                break;
            case self::INDEX_PAGE_RESULTS_STEP:
                $steps[5]['class'] = 'bold';
        }

        return $this->progress_bar($steps);
    }

    /**
     * Shows form.
     * @param moodleform $mform form for merging users.
     * @param int $step step to show in the index page.
     * @param UserSelectTable $ust table for user after searching
     * @param TenantSelectTable $tst table for tenant after searching
     * @return string html to show on index page.
     */
    public function index_page(moodleform $mform, $step, UserSelectTable $ust = NULL, TenantSelectTable $tst = NULL) {
        $output = $this->header();
        $output .= $this->heading_with_help(get_string('addtenants', 'tool_multitenantuser'), 'header', 'tool_multitenantuser');

        $output .= $this->build_progress_bar($step);
        switch ($step) {
            case self::INDEX_PAGE_SEARCH_USER:
                $output .= $this->moodleform($mform);
                break;
            case self::INDEX_PAGE_SEARCH_AND_SELECT_USER:
                $output .= $this->moodleform($mform);
                // render user select table if available
                if($ust !== NULL) {
                    $output .= $this->render_user_select_table($ust);
                }
                break;
            case self::INDEX_PAGE_SEARCH_TENANT:
                $output .= $this->moodleform($mform);
                // render tenant select table if available
                if ($tst !== NULL) {
                    $output .= $this->render_tenant_select_table($tst);
                }
                break;
            case self::INDEX_PAGE_SEARCH_AND_SELECT_TENANT:
                $output .= $this->moodleform($mform);
                // render tenant select table if available
                if ($tst !== NULL) {
                    $output .= $this->render_tenant_select_table($tst);
                }
                break;
            case self::INDEX_PAGE_CONFIRMATION_STEP:
                break;
        }

        $output .= $this->render_user_review_table($step);
        $output .= $this->footer();
        return $output;
    }

    /**
     * Renders user select table
     * @param UserSelectTable $ust the user select table
     *
     * @return string $tablehtml html string rendering
     */
    public function render_user_select_table(UserSelectTable $ust)
    {
        return $this->moodleform(new select_form($ust));
    }

    public function render_tenant_select_table(TenantSelectTable $tst) {
        return $this->moodleform(new tselect_form($tst));
    }

    /**
     * Builds and renders a user review table
     *
     * @return string $reviewtable HTML of the review table section
     */
    public function render_user_review_table($step)
    {
        return $this->moodleform(
            new reviewuserform(
                new UserReviewTable($this),
                $this,
                $step === self::INDEX_PAGE_CONFIRMATION_STEP));
    }

    /**
     * Displays merge users tool error message
     *
     * @param string $message The error message
     * @param bool $showreturn Shows a return button to the index page
     */
    public function mu_error($message, $showreturn = true) {
        $errorhtml = '';

        echo $this->header();

        $errorhtml .= $this->output->box($message, 'generalbox align-center');
        if($showreturn) {
            $returnurl = new moodle_url('/admin/tool/multitenantuser/index.php');
            $returnbutton = new single_button($returnurl, get_string('error_return', 'tool_multitenantuser'));

            $errorhtml .= $this->output->render($returnbutton);
        }

        echo $errorhtml;
        echo $this->footer();
    }

    /**
     * Show the result of the action.
     * @param object $to stdClass with at least id and username fields.
     * @param array $tenants lists tenants added to $to.
     * @param bool $success true if action succeeded; false otherwise.
     * @param array $data logs actions done if success, lists errors on failure.
     * @param id $logid id of the record with the whole detail of the action.
     * @return string html with the results.
     */
    public function results_page($to, array $tenants, $success, array $data, $logid) {
        if($success) {
            $resulttype = 'ok';
            $dbmessage = 'dbok';
            $notifytype = 'notifysuccess';
        } else {
            $transactions = (tool_multitenantuser_transactionssupported()) ?
                '_transactions' :
                '_no_transactions';

            $resulttype = 'ko';
            $dbmessage = 'dbko' . $transactions;
            $notifytype = 'notifyproblem';
        }

        $output = $this->header();
        $output .= $this->heading(get_string('addtenants', 'tool_multitenantuser'));
        $output .= $this->build_progress_bar(self::INDEX_PAGE_RESULTS_STEP);
        $output .= html_writer::empty_tag('br');
        $output .= html_writer::start_tag('div', array('class' => 'result'));
        $output .= html_writer::start_tag('div', array('class' => 'title'));
        $output .= get_string('adding', 'tool_multitenantuser');
        if(!is_null($to) && !is_null($tenants)) {
            $output .= ' ' . get_string('TEMP', 'tool_multitenantuser') . ' ' .
                get_string('into', 'tool_multitenantuser') . ' ' .
                get_string('useraddingheader', 'tool_multitenantuser', $to);
        }
        $output .= html_writer::empty_tag('br') . html_writer::empty_tag('br');
        $output .= get_string('logid', 'tool_multitenantuser', $logid);
        $output .= html_writer::empty_tag('br');
        $output .= get_string('log' . $resulttype, 'tool_multitenantuser');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::empty_tag('br');

        $output .= html_writer::start_tag('div', array('class' => 'resultset' . $resulttype));
        foreach ($data as $item) {
            $output .= $item . html_writer::empty_tag('br');
        }
        $output .= html_writer::end_tag('div');
        $output .= html_writer::end_tag('div');
        $output .= html_writer::tag('div', html_writer::empty_tag('br'));
        $output .= $this->notification(html_writer::tag('center', get_string($dbmessage, 'tool_multitenantuser')), $notifytype);
        $output .= html_writer::tag('center', $this->single_button(new moodle_url('/admin/tool/multitenantuser/index.php'), get_string('continue'), 'get'));
        $output .= $this->footer();

        return $output;
    }

    /**
     * Helper method dealing with the fact we can not just fetch the output of moodleforms
     *
     * @param moodleform $mform
     * @return string HTML
     */
    protected function moodleform(moodleform $mform) {
        ob_start();
        $mform->display();
        $o = ob_get_contents();
        ob_end_clean();

        return $o;
    }

    /**
     * This method produces the HTML to show the details of a user.
     * @param int $userid user.id
     * @param object $user an object with firstname and lastname attributes.
     * @return string the corresponding HTML.
     */
    public function show_user($userid, $user)
    {
        return html_writer::link(
            new moodle_url('/user/view.php',
                array('id' => $userid, 'sesskey' => sesskey())),
            fullname($user) .
            ' (' . $user->username . ') ' .
            ' &lt;' . $user->email . '&gt;' .
            ' ' . $user->idnumber);
    }

    public function show_tenant($tenantid) {
        return html_writer::link(
            new moodle_url('/local/report_companies/index.php',
                array('companyid' => $tenantid)),
            company::get_companyname_byid($tenantid));
    }

    /**
     * Produces the page with the list of logs.
     * TODO: make pagination.
     * @param array $logs array of logs.
     * @return string the corresponding HTML.
     * @global type $CFG
     */
    public function logs_page($logs)
    {
        global $CFG;

        $output = $this->header();
        $output .= $this->heading(get_string('viewlog', 'tool_multitenantuser'));
        $output .= html_writer::start_tag('div', array('class' => 'result'));
        if (empty($logs)) {
            $output .= get_string('nologs', 'tool_multitenantuser');
        } else {
            $output .= html_writer::tag('div', get_string('loglist', 'tool_multitenantuser'), array('class' => 'title'));

            $flags = array();
            $flags[] = $this->pix_icon('i/invalid', get_string('eventusermergedfailure', 'tool_multitenantuser')); //failure icon
            $flags[] = $this->pix_icon('i/valid', get_string('eventusermergedsuccess', 'tool_multitenantuser')); //ok icon

            $table = new html_table();
            $table->align = array('center', 'center', 'center', 'center', 'center', 'center');
            $table->head = array(get_string('olduseridonlog', 'tool_multitenantuser'), get_string('newuseridonlog', 'tool_multitenantuser'), get_string('date'), get_string('status'), '');

            $rows = array();
            foreach ($logs as $i => $log) {
                $row = new html_table_row();
                $row->cells = array(
                    ($log->from)
                        ? $this->show_user($log->fromuserid, $log->from)
                        : get_string('deleted', 'tool_multitenantuser', $log->fromuserid),
                    ($log->to)
                        ? $this->show_user($log->touserid, $log->to)
                        : get_string('deleted', 'tool_multitenantuser', $log->touserid),
                    userdate($log->timemodified, get_string('strftimedaydatetime', 'langconfig')),
                    $flags[$log->success],
                    html_writer::link(
                        new moodle_url('/' . $CFG->admin . '/tool/multitenantuser/log.php',
                            array('id' => $log->id, 'sesskey' => sesskey())),
                        get_string('more'),
                        array('target' => '_blank')),
                );
                $rows[] = $row;
            }

            $table->data = $rows;
            $output .= html_writer::table($table);
        }

        $output .= html_writer::end_tag('div');
        $output .= $this->footer();

        return $output;
    }
}