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
 * Version details.
 *
 * @package    SAMIE
 * @copyright  2015 Planificacion de Entornos Tecnologicos SL
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * @module local_samiesurvey/samiesurvey
 */
define(['jquery'], function($) {
    /**
     * @constructor
     * @alias module:local_samiesurvey/samiesurvey
     */
    return {
        init: function() {
            $(function () {
                // Create IE + others compatible event handler.
                var eventMethod = window.addEventListener ? 'addEventListener' : 'attachEvent';
                var eventer = window[eventMethod];
                var messageEvent = eventMethod == 'attachEvent' ? 'onmessage' : 'message';
                var stylesheets = '';
                // Listen to message from child window.
                eventer(messageEvent, function(e) {
                    $('#samiesurveyframe').css('height', e.data + 'px');
                }, false);
                // Compile all stylesheets definitions to use them in the iframe content.
                $('link[rel="stylesheet"]').each(function() {
                    if (stylesheets !== '') {
                        stylesheets += ';';
                    }
                    stylesheets += $(this).attr('href');
                });
                $('#stylesheets').val(stylesheets);
                // Loads the iframe content.
                $('#redirectToIframe').submit();
            });
        }
    };
});
