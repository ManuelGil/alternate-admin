<?php
//    This file is part of Alternate Admin for Moodle
//    Alternate Admin Free GNU Application
//    Copyright (C) 2021 Manuel Gil.
//
//    This program is free software: you can redistribute it and/or modify
//    it under the terms of the GNU General Public License as published by
//    the Free Software Foundation, either version 3 of the License, or
//    (at your option) any later version.
//
//    This program is distributed in the hope that it will be useful,
//    but WITHOUT ANY WARRANTY; without even the implied warranty of
//    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
//    GNU General Public License for more details.
//
//    You should have received a copy of the GNU General Public License
//    along with this program.  If not, see <http://www.gnu.org/licenses/>.
//
/**
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $Id$
 * <p>Title: Alternate Admin for Moodle.</p>
 * <p>Description: This wrapper for Moodle adds a new interface to
 * 					streamline your administrative tasks.</p>
 * <p>Copyright: <a href="http://www.gnu.org/copyleft/gpl.html">GNU GPL v3 or later</a>.</p>
 * <p>Company: <a href="https://imgil.dev/">Manuel Gil</a></p>
 *
 * Problem: Add more function to tradiccional admin.
 * @author $Author: Manuel Gil. $
 * @version $Revision: 0.0.2 $ $Date: 01/17/2021 $
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

namespace App\Controllers;

/**
 * EnrollmentController class
 *
 * @extends BaseController
 */
class EnrollmentController extends BaseController
{

	/**
	 * This method load the 'bulk-user-enrollment' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getBulkUserEnrollment()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// Parsing the courses.
		$courses = addslashes(json_encode(get_courses(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		// Parsing the users.
		$users = addslashes(json_encode(get_users_listing(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		// Gets roles.
		$sql = "SELECT		*
				FROM		{role}
				ORDER BY	sortorder ASC;";

		// Execute the query.
		$records = $DB->get_records_sql($sql);

		// Parsing the records.
		$roles = addslashes(json_encode($records, JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'users' => $users,
			'roles' => $roles
		);

		// Render template.
		return $this->render('/enrollments/bulk-user-enrollment.mustache', $params);
	}


	/**
	 * This method load the 'bulk-user-enrollment' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postBulkUserEnrollment()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// Define the count variables.
		$successes = 0;
		$failures = 0;

		// Loop through the courses.
		foreach ($_POST['courses'] as $courseid) {
			$context = get_context_instance(CONTEXT_COURSE, $courseid);
			// context_course::instance($courseid);

			// Loop through the users.
			foreach ($_POST['users'] as $userid) {
				if (!is_enrolled($context, $userid)) {
					try {
						enrol_try_internal_enrol($courseid, $userid, $_POST['role'], time());

						$successes++;
					} catch (\Throwable $e) {
						// Add one fault to the count.
						$failures++;
					}
				}
			}
		}

		$message = "";

		// Add a message with the number of hits.
		if ($successes > 0) {
			$message .= "<div class=\"alert alert-success\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Well done!</strong> {$successes} users were enrolled.
        				</div>";
		}

		// Add a message with the number of failures.
		if ($failures > 0) {
			$message .= "<div class=\"alert alert-danger\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Heads up!</strong> {$failures} users could not be enrolled.
        				</div>";
		}

		// Parsing the users.
		$users = addslashes(json_encode(get_users_listing(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		// Parsing the courses.
		$courses = addslashes(json_encode(get_courses(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		// Gets roles.
		$sql = "SELECT		*
				FROM		{role}
				ORDER BY	sortorder ASC;";

		// Execute the query.
		$records = $DB->get_records_sql($sql);

		// Parsing the records.
		$roles = addslashes(json_encode($records, JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'users' => $users,
			'roles' => $roles,
			'message' => $message
		);

		// Render template.
		return $this->render('/enrollments/bulk-user-enrollment.mustache', $params);
	}
}
