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
 * @version $Revision: 0.0.8 $ $Date: 01/24/2021 $
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
	 * This method redirect to BASE URL when access to parent section. <br/>
	 * <b>post: </b>access to any method (POST, GET, DELETE, OPTIONS, HEAD etc...).
	 */
	public function anyIndex()
	{
		header('location: ' . BASE_URL);
	}

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

		// Parsing the records.
		$roles = addslashes(json_encode($DB->get_records('role'), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

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

		// Parsing the records.
		$roles = addslashes(json_encode($DB->get_records('role'), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

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

	/**
	 * This method load the 'bulk-user-unenrollment' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getBulkUserUnenrollment()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the courses.
		$courses = addslashes(json_encode(get_courses(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses
		);

		// Render template.
		return $this->render('/enrollments/bulk-user-unenrollment.mustache', $params);
	}

	/**
	 * This method load the 'bulk-user-unenrollment' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postBulkUserUnenrollment()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// The users has been show in a table component.
		$result = "<div class=\"table-responsive\">
                        <table id=\"table\" class=\"table table-striped table-hover table-condensed\">
                            <thead>
                                <tr>
									<th>user id</th>
                                    <th>plugin</th>
                                </tr>
                            </thead>
							<tbody>";

		// Get intances of enrol table.
		$instances = $DB->get_records('enrol', array('courseid' => $_POST['course']));

		foreach ($_POST['users'] as $userid) {
			foreach ($instances as $instance) {
				$plugin = enrol_get_plugin($instance->enrol);
				$plugin->unenrol_user($instance, $userid);

				// Add the new user into the table.
				$result .= "<tr>
								<td>{$userid}</td>
								<td>" . get_class($plugin) . "</td>
							</tr>";
			}
		}

		$result .= "</tbody></table></div>";

		$message = "";

		// Add the result.
		$message .= "<div class=\"alert alert-info\" role=\"alert\">
        			      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        			        <span aria-hidden=\"true\">&times;</span>
        			      </button>
						  <strong>Oh snap!</strong> The following users were unrolleds:<br><br>
						  {$result}
        			</div>";

		// Parsing the courses.
		$courses = addslashes(json_encode(get_courses(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'message' => $message
		);

		// Render template.
		return $this->render('/enrollments/bulk-user-unenrollment.mustache', $params);
	}

	/**
	 * This method load the 'dynamic-unenrollment' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getDynamicUnenrollment()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the courses.
		$courses = addslashes(json_encode(get_courses(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		// Parsing the users.
		$users = addslashes(json_encode(get_users_listing(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'users' => $users
		);

		// Render template.
		return $this->render('/enrollments/Dynamic-unenrollment.mustache', $params);
	}

	/**
	 * This method load the 'dynamic-unenrollment' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postDynamicUnenrollment()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// The users has been show in a table component.
		$result = "<div class=\"table-responsive\">
                        <table id=\"table\" class=\"table table-striped table-hover table-condensed\">
                            <thead>
                                <tr>
									<th>user id</th>
                                    <th>plugin</th>
                                </tr>
                            </thead>
							<tbody>";

		if ($_POST['search'] == 'course') {
			// Get intances of enrol table.
			$instances = $DB->get_records('enrol', array('courseid' => $_POST['course']));

			foreach ($_POST['users'] as $userid) {
				foreach ($instances as $instance) {
					$plugin = enrol_get_plugin($instance->enrol);
					$plugin->unenrol_user($instance, $userid);

					// Add the new user into the table.
					$result .= "<tr>
									<td>{$userid}</td>
									<td>" . get_class($plugin) . "</td>
								</tr>";
				}
			}
		}

		if ($_POST['search'] == 'user') {
			foreach ($_POST['courses'] as $courseid) {
				// Get intances of enrol table.
				$instances = $DB->get_records('enrol', array('courseid' => $courseid));

				foreach ($instances as $instance) {
					$plugin = enrol_get_plugin($instance->enrol);
					$plugin->unenrol_user($instance, $_POST['user']);

					// Add the new user into the table.
					$result .= "<tr>
									<td>{$_POST['user']}</td>
									<td>" . get_class($plugin) . "</td>
								</tr>";
				}
			}
		}

		$result .= "</tbody></table></div>";

		$message = "";

		// Add the result.
		$message .= "<div class=\"alert alert-info\" role=\"alert\">
        			      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        			        <span aria-hidden=\"true\">&times;</span>
        			      </button>
						  <strong>Oh snap!</strong> The following users were unrolleds:<br><br>
						  {$result}
        			</div>";

		// Parsing the courses.
		$courses = addslashes(json_encode(get_courses(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'message' => $message
		);

		// Render template.
		return $this->render('/enrollments/Dynamic-unenrollment.mustache', $params);
	}
}
