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
 * @version $Revision: 0.3.0 $ $Date: 02/15/2021 $
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

namespace App\Controllers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * CourseController class
 *
 * @extends BaseController
 */
class CourseController extends BaseController
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
	 * This method load the 'list-courses' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListCourses()
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
		return $this->render('/courses/list-courses.mustache', $params);
	}

	/**
	 * This method load the 'count-courses' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getCountCourses()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count courses by category.
		$sql = "SELECT		{course_categories}.id,
							{course_categories}.name,
							COUNT({course}.id) AS courses
				FROM		{course_categories}
				LEFT JOIN	{course}
					ON		{course_categories}.id = {course}.category
					AND		{course}.visible = 1
				GROUP BY	{course_categories}.id;";

		// Execute the query.
		$records = $DB->get_records_sql($sql);

		// Parsing the records.
		$items = addslashes(
			json_encode(
				$records,
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'items' => $items
		);

		// Render template.
		return $this->render('/courses/count-courses.mustache', $params);
	}

	/**
	 * This method load the 'count-role' route. <br/>
	 * <b>post: </b>access to GET method. <br/>
	 * <b>post: </b>AJAX request.
	 *
	 * @param string|null $roleid - the role id
	 */
	public function getCountRole(?string $roleid = '')
	{
		// Imports Database.
		global $DB;

		// SQL Query for count role.
		$sql = "SELECT		{course}.id,
							{course}.fullname AS course,
							COUNT({course}.id) AS users
				FROM		{role_assignments}
				JOIN		{context}
					ON		{role_assignments}.contextid = {context}.id
					AND		{context}.contextlevel = 50
				JOIN		{user}
					ON		{user}.id = {role_assignments}.userid
				JOIN		{course}
					ON		{context}.instanceid = {course}.id
				WHERE		{role_assignments}.roleid = :roleid
				GROUP BY	{course}.id
				ORDER BY	users ASC;";

		// Create a log channel.
		$log = new Logger('App');
		$log->pushHandler(new StreamHandler(__DIR__ . '/../../logs/error.log', Logger::ERROR));

		try {
			header_remove();
			http_response_code(200);
			header('HTTP/1.1 200 OK');
			header('Content-Type: application/json');

			// Execute and parse the query.
			return json_encode(
				$DB->get_records_sql(
					$sql,
					[
						'roleid' => (float) $roleid
					]
				)
			);
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
	 * This method load the 'count-with-role' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getCountWithRole()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

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
			'roles' => $roles
		);

		// Render template.
		return $this->render('/courses/count-with-role.mustache', $params);
	}

	/**
	 * This method load the 'non-role-course' route. <br/>
	 * <b>post: </b>access to GET method. <br/>
	 * <b>post: </b>AJAX request.
	 *
	 * @param string|null $roleid - the role id
	 */
	public function getNonRoleCourse(?string $roleid = '')
	{
		// Imports Database.
		global $DB;

		// SQL Query for count role.
		$sql = "SELECT		{course}.id,
				    		{course}.fullname
				FROM 		{course}
				LEFT JOIN 	{context}
					ON 		{course}.id = {context}.instanceid
				    AND		{context}.contextlevel = 50
				LEFT JOIN	{role_assignments}
					ON		{context}.id = {role_assignments}.contextid
				    AND 	{role_assignments}.roleid = :roleid
				GROUP BY 	{course}.id
				HAVING 		COUNT({role_assignments}.id) = 0;";

		// Create a log channel.
		$log = new Logger('App');
		$log->pushHandler(new StreamHandler(__DIR__ . '/../../logs/error.log', Logger::ERROR));

		try {
			header_remove();
			http_response_code(200);
			header('HTTP/1.1 200 OK');
			header('Content-Type: application/json');

			// Execute and parse the query.
			return json_encode(
				$DB->get_records_sql(
					$sql,
					[
						'roleid' => (float) $roleid
					]
				)
			);
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
	 * This method load the 'course-without-role' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getCourseWithoutRole()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

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
			'roles' => $roles
		);

		// Render template.
		return $this->render('/courses/course-without-role.mustache', $params);
	}

	/**
	 * This method load the 'list-users' route. <br/>
	 * <b>post: </b>access to GET method. <br/>
	 * <b>post: </b>AJAX request.
	 *
	 * @param string|null $courseid - the course id
	 */
	public function getListUsers(?string $courseid = '')
	{
		// Imports Database.
		global $DB;

		// Gets roles.
		$sql = "SELECT      {user}.id,
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
			return json_encode(
				$DB->get_records_sql(
					$sql,
					[
						'courseid' => (float) $courseid
					]
				)
			);
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
	 * This method load the 'list-users-course' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListUsersCourse()
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
		return $this->render('/courses/list-users-course.mustache', $params);
	}

	/**
	 * This method load the 'bulk-course-creation' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getBulkCourseCreation()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// Parsing the categories.
		$categories = addslashes(
			json_encode(
				$DB->get_records('course_categories'),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'categories' => $categories
		);

		// Render template.
		return $this->render('/courses/bulk-course-creation.mustache', $params);
	}

	/**
	 * This method load the 'bulk-course-creation' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postBulkCourseCreation()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		require_once("{$CFG->dirroot}/course/lib.php");

		// Parsing the post params.
		$category = $_POST['category'] ?? "";
		$fullname = $_POST['fullname'] ?? "";
		$shortname = $_POST['shortname'] ?? "";
		$separator = $_POST['separator'] ?? "";
		$start = $_POST['start'] ?? 0;
		$count = $_POST['count'] ?? 0;

		// Define the count variables.
		$successes = 0;
		$failures = 0;

		// The users has been show in a table component.
		$result = "<div class=\"table-responsive\">
                        <table id=\"table\" class=\"table table-striped table-hover table-condensed\">
                            <thead>
                                <tr>
                                    <th>id</th>
                                    <th>fullname</th>
                                    <th>count</th>
                                </tr>
                            </thead>
							<tbody>";

		// Loop through the users.
		for ($i = 0; $i < $count; $i++) {
			// If username exist launch an error.
			try {
				$index = $start + $i;

				$data = new \stdClass();

				// Set name.
				$full = "{$fullname} {$index}";
				$short = "{$shortname}{$separator}{$index}";

				// Set category
				$data->category = $category;
				$data->fullname = $full;
				$data->shortname = $short;

				$course = create_course($data);

				// Add the new user into the table.
				$result .= "<tr>
								<td>{$course->id}</td>
								<td>{$course->fullname}</td>
								<td>{$course->shortname}</td>
							</tr>";

				// Add one user to the count.
				$successes++;
			} catch (\Throwable $e) {
				// Add one fault to the count.
				$failures++;
			}
		}

		// Close the table of users.
		$result .= "</tbody></table></div>";

		$message = "";

		// Add a message with the number of hits.
		if ($successes > 0) {
			$message .= "<div class=\"alert alert-success\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Well done!</strong> {$successes} courses were created.
        				</div>";
		}

		// Add a message with the number of failures.
		if ($failures > 0) {
			$message .= "<div class=\"alert alert-danger\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Heads up!</strong> {$failures} courses could not be created.
        				</div>";
		}

		// Add the result.
		$message .= "<div class=\"alert alert-info\" role=\"alert\">
        			      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        			        <span aria-hidden=\"true\">&times;</span>
        			      </button>
						  <strong>Oh snap!</strong> The following courses were created:<br><br>
						  {$result}
        			</div>";

		// Parsing the categories.
		$categories = addslashes(
			json_encode(
				$DB->get_records('course_categories'),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'categories' => $categories,
			'message' => $message
		);

		// Render template.
		return $this->render('/courses/bulk-course-creation.mustache', $params);
	}
}
