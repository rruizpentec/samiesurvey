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
 * Page that shows the users that have filled the surveys or not.
 *
 * @package    SAMIE
 * @copyright  2015 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir.'/blocklib.php');

$afgidlms = required_param('afg_id_lms', PARAM_INT);
$filled = required_param('filled', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);
$iscncp = ($afgidlms != $courseid);

global $CFG, $PAGE, $OUTPUT, $DB;
$systemcontext = context_system::instance();
require_login();
$PAGE->set_url('/blocks/samiesurvey/userslist.php?afg_id_lms='.$afgidlms.'&filled=0'.'&courseid='.$courseid);
$PAGE->set_pagelayout('standard');
$PAGE->set_context($systemcontext);
$PAGE->set_title(get_string('title', 'block_samiesurvey'));

$course = $DB->get_record('course', array('id' => $courseid));
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', array('id' => $courseid)));
$PAGE->requires->css(new moodle_url($CFG->wwwroot.'/blocks/samiesurvey/styles.css'));
echo $OUTPUT->header();

$content  = html_writer::start_tag('div', array('id' => 'samiesurveycontent', 'class' => 'block'));
$content .= html_writer::start_tag('div', array('class' => 'header'));
$content .= html_writer::start_tag('div', array('class' => 'title'));
$content .= html_writer::tag('h2', get_string('title', 'block_samiesurvey'));
$content .= html_writer::end_tag('div');
$content .= html_writer::end_tag('div');

$coursecontext = context_course::instance($courseid);
if (has_capability('block/samiesurvey:viewlistofsurveysfilled', $coursecontext, $USER, true)) {
    $samieconfig = get_config('package_samie');
    $baseurl = $samieconfig->baseurl;
    if (substr($baseurl, -1, 1) != '/') {
        $baseurl .= '/';
    }

    $result = file_get_contents($baseurl.'inc/surveyrequests.php?afg_id_lms='.$afgidlms.'&es_cncp='.$iscncp);
    $queryparams = array('courseid' => $courseid);
    $content .= html_writer::start_tag('div');
    if ($filled == 0) {
        $content .= html_writer::start_tag('h3').get_string('listofuserswithoutfilledsurvey', 'block_samiesurvey');
        $content .= html_writer::tag('a', get_string('listofuserswithfilledsurvey', 'block_samiesurvey'),
                array(
                    'href' => $CFG->wwwroot."/blocks/samiesurvey/userslist.php?afg_id_lms=$afgidlms&filled=1&courseid=$courseid",
                    'class' => 'btn btn-default',
                    'style' => 'float: right')
        );
        list($insql, $inparams) = $DB->get_in_or_equal(explode(',', $result), SQL_PARAMS_NAMED, 'param', false);
        $content .= html_writer::end_tag('h3');
    } else {
        $content .= html_writer::start_tag('h3').get_string('listofuserswithfilledsurvey', 'block_samiesurvey');
        $content .= html_writer::tag('a', get_string('listofuserswithoutfilledsurvey', 'block_samiesurvey'),
                array(
                    'href' => $CFG->wwwroot."/blocks/samiesurvey/userslist.php?afg_id_lms=$afgidlms&filled=0&courseid=$courseid",
                    'class' => 'btn btn-default',
                    'style' => 'float: right')
        );
        list($insql, $inparams) = $DB->get_in_or_equal(explode(',', $result), SQL_PARAMS_NAMED);
        $content .= html_writer::end_tag('h3');
    }
    $content .= html_writer::end_tag('div');

    $sql = " SELECT DISTINCT u.id, u.firstname, u.lastname
               FROM {user} u, {role_assignments} r, {context} cx, {course} c
              WHERE u.id = r.userid
                    AND r.contextid = cx.id
                    AND cx.instanceid = c.id
                    AND r.roleid = 5
                    AND cx.contextlevel = 50
                    AND c.id = :courseid
                    AND u.id {$insql}";

    $params = array_merge($queryparams, $inparams);
    $studentsrecords = $DB->get_recordset_sql($sql, $params);
    $content .= html_writer::start_tag('table', array('class' => 'table'));
    $content .= html_writer::start_tag('tr');
    $content .= html_writer::tag('th', get_string('firstname', 'block_samiesurvey'));
    $content .= html_writer::tag('th', get_string('lastname', 'block_samiesurvey'));
    $content .= html_writer::end_tag('tr');
    if (count($studentsrecords) > 0) {
        foreach ($studentsrecords as $studentrecord) {
            $content .= html_writer::start_tag('tr');
            $content .= html_writer::tag('td', $studentrecord->firstname);
            $content .= html_writer::tag('td', $studentrecord->lastname);
            $content .= html_writer::end_tag('tr');
        }
    } else {
        $content .= html_writer::tag('td', get_string('nodatafound', 'block_samiesurvey'),
                array('colspan' => '100%', 'class' => 'text-center block_samiesurvey_nodatafound'));
    }
    $content .= html_writer::end_tag('table');
} else {
    $content .= html_writer::tag('h3', get_string('permission_denied', 'block_samiesurvey'));
}
$content .= html_writer::end_tag('div');
echo $content;
echo $OUTPUT->footer();