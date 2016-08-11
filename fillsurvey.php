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
 * Page that shows the survey to be filled.
 *
 * @package    block_samiesurvey
 * @copyright  2015 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

defined('MOODLE_INTERNAL') || die();

require_login();

global $CFG, $USER, $DB;
require_once($CFG->libdir.'/blocklib.php');

$afgidlms = required_param('afg_id_lms', PARAM_INT);
$courseid = required_param('courseid', PARAM_INT);

$instance = new stdClass();
$coursecontext = context_course::instance($courseid);

$PAGE->set_url("/blocks/samiesurvey/fillsurvey.php?afg_id_lms=$afgidlms");
$PAGE->set_pagelayout('standard');
$PAGE->set_context($coursecontext);
$PAGE->set_title(get_string('title', 'block_samiesurvey'));

$course = $DB->get_record('course', array('id' => $courseid));
$PAGE->navbar->add($course->fullname, new moodle_url('/course/view.php', array('id' => $courseid)));

$PAGE->requires->js_call_amd('block_samiesurvey/samiesurvey', 'init');

$samieconfig = get_config('package_samie');
$baseurl = $samieconfig->baseurl;

if (substr($baseurl, -1, 1) != '/') {
    $baseurl .= '/';
}
$url = $baseurl.'survey.php';

echo $OUTPUT->header();
echo html_writer::start_tag('div', array('class' => 'block'));
echo html_writer::start_tag('div', array('class' => 'header'));
echo html_writer::start_tag('div', array('class' => 'title'));
echo html_writer::tag('h2', get_string('title', 'block_samiesurvey'));
echo html_writer::end_tag('div');
echo html_writer::end_tag('div');
echo html_writer::tag('iframe', '', array('id' => 'samiesurveyframe', 'name' => 'samiesurveyframe', 'scrolling' => 'no',
    'width' => '100%', 'height' => '100%', 'src' => 'about:blank', 'style' => 'border: 0px solid blue;'));
echo html_writer::end_tag('div');
echo html_writer::start_tag('form', array('style' => 'display: none', 'id' => 'redirectToIframe', 'name' => 'redirectToIframe',
    'action' => $url, 'target' => 'samiesurveyframe', 'method' => 'POST'));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'alu_id_lms', 'value' => $USER->id));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'afg_id_lms', 'value' => $afgidlms));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'name' => 'es_cncp', 'value' => $afgidlms != $courseid));
echo html_writer::empty_tag('input', array('type' => 'hidden', 'id' => 'stylesheets', 'name' => 'stylesheets', 'value' => null));
echo html_writer::end_tag('form');
echo $OUTPUT->footer();