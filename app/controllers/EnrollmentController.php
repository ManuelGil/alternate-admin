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
 * @version $Revision: 0.2.1 $ $Date: 01/25/2021 $
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

namespace App\Controllers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

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
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the courses.
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		// Parsing the users.
		$users = addslashes(
			json_encode(
				get_users_listing(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		// Parsing the records.
		$roles = addslashes(
			json_encode(
				role_fix_names(get_all_roles(), \context_system::instance(), ROLENAME_ORIGINAL, true),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

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
		// Imports Config and Current User.
		global $CFG, $USER;

		// Define the count variables.
		$successes = 0;
		$failures = 0;

		if (isset($_POST['courses']) && isset($_POST['users']) && isset($_POST['role'])) {
			// Loop through the courses.
			foreach ($_POST['courses'] as $courseid) {
				$context = \context_course::instance($courseid);

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
		$users = addslashes(
			json_encode(
				get_users_listing(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		// Parsing the courses.
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		// Parsing the records.
		$roles = addslashes(
			json_encode(
				role_fix_names(get_all_roles(), \context_system::instance(), ROLENAME_ORIGINAL, true),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

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
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

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

		if (isset($_POST['course']) && isset($_POST['users'])) {
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
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

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
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		// Parsing the users.
		$users = addslashes(
			json_encode(
				get_users_listing(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'users' => $users
		);

		// Render template.
		return $this->render('/enrollments/dynamic-unenrollment.mustache', $params);
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

		if (isset($_POST['search']) && $_POST['search'] == 'course') {
			if (isset($_POST['course']) && isset($_POST['users'])) {
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
		}

		if (isset($_POST['search']) && $_POST['search'] == 'user') {
			if (isset($_POST['courses']) && isset($_POST['user'])) {
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
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'message' => $message
		);

		// Render template.
		return $this->render('/enrollments/dynamic-unenrollment.mustache', $params);
	}

	/**
	 * This method load the 'list-assignments' route. <br/>
	 * <b>post: </b>access to GET method. <br/>
	 * <b>post: </b>AJAX request.
	 *
	 * @param int $courseid - the course id
	 */
	public function getListAssignments($courseid = 0)
	{
		// Imports Database.
		global $DB;

		// Gets roles.
		$sql = "SELECT      {role_assignments}.id,
							{user}.username,
							{user}.email,
							{user}.firstname,
							{user}.lastname,
							{role}.shortname AS role
				FROM		{role_assignments}
				JOIN		{context}
					ON		{role_assignments}.contextid = {context}.id
					AND		{context}.contextlevel = 50
				JOIN 		{role}
					ON 		{role_assignments}.roleid = {role}.id
				JOIN		{user}
					ON		{user}.id = {role_assignments}.userid
				JOIN		{course}
					ON		{context}.instanceid = {course}.id
                WHERE       {course}.id = :courseid";

		// Create a log channel.
		$log = new Logger('App');
		$log->pushHandler(new StreamHandler(__DIR__ . '/../../logs/error.log', Logger::ERROR));

		try {
			header_remove();
			http_response_code(200);
			header('HTTP/1.1 200 OK');
			header('Content-Type: application/json');

			// Execute and parse the query.
			return json_encode($DB->get_records_sql($sql, ['courseid' => $courseid]));
		} catch (\Throwable $e) {
			// When an error occurred.
			if (DEBUG) {
				header_remove();
				http_response_code(404);
				header('HTTP/1.1 404 Not Found');
				echo '<pre>' . $e->getTraceAsString() . '</pre>';
				echo PHP_EOL;
				echo $e->getMessage();
			} else {
				$log->error($e->getMessage(), $e->getTrace());
				header_remove();
				http_response_code(500);
				header('HTTP/1.1 500 Internal Server Error');
			}
			exit;
		}
	}

	/**
	 * This method load the 'switch-role' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getSwitchRole()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the courses.
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		// Parsing the records.
		$roles = addslashes(
			json_encode(
				role_fix_names(get_all_roles(), \context_system::instance(), ROLENAME_ORIGINAL, true),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'roles' => $roles
		);

		// Render template.
		return $this->render('/enrollments/switch-role.mustache', $params);
	}

	/**
	 * This method load the 'switch-role' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postSwitchRole()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// Define the count variables.
		$successes = 0;
		$failures = 0;

		if (isset($_POST['users']) && isset($_POST['role'])) {
			// Loop through the users.
			foreach ($_POST['users'] as $assignmentid) {
				try {
					$DB->set_field('role_assignments', 'roleid', $_POST['role'], ['id' => $assignmentid]);

					// Add one user to the count.
					$successes++;
				} catch (\Throwable $e) {
					// Add one fault to the count.
					$failures++;
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
        				      <strong>Well done!</strong> {$successes} users were updated.
        				</div>";
		}

		// Add a message with the number of failures.
		if ($failures > 0) {
			$message .= "<div class=\"alert alert-danger\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Heads up!</strong> {$failures} users could not be updated.
        				</div>";
		}

		// Parsing the courses.
		$courses = addslashes(
			json_encode(
				get_courses(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		// Parsing the records.
		$roles = addslashes(
			json_encode(
				role_fix_names(get_all_roles(), \context_system::instance(), ROLENAME_ORIGINAL, true),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'courses' => $courses,
			'roles' => $roles,
			'message' => $message
		);

		// Render template.
		return $this->render('/enrollments/switch-role.mustache', $params);
	}
}
