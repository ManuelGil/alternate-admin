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
 * @version $Revision: 0.2.0 $ $Date: 02/07/2021 $
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

namespace App\Controllers;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

/**
 * UserController class
 *
 * @extends BaseController
 */
class UserController extends BaseController
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
	 * This method load the 'list-users' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListUsers()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the users.
		$users = addslashes(
			json_encode(
				get_users(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'users' => $users
		);

		// Render template.
		return $this->render('/users/list-users.mustache', $params);
	}

	/**
	 * This method load the 'bulk-user-creation' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getBulkUserCreation()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER
		);

		// Render template.
		return $this->render('/users/bulk-user-creation.mustache', $params);
	}

	/**
	 * This method load the 'bulk-user-creation' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postBulkUserCreation()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the post params.
		$prefix = $_POST['prefix'] ?? "";
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
                                    <th>username</th>
                                    <th>password</th>
                                </tr>
                            </thead>
							<tbody>";

		// Loop through the users.
		for ($i = 0; $i < $count; $i++) {
			// If username exist launch an error.
			try {
				$index = $start + $i;

				// Set an username.
				$username = "{$prefix}{$separator}{$index}";

				// Set a password.
				$data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
				$password = substr(str_shuffle($data), 0, 7);

				// Create a new user.
				$user = create_user_record($username, $password);

				// Add the new user into the table.
				$result .= "<tr>
								<td>{$user->id}</td>
								<td>{$username}</td>
								<td>{$password}</td>
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
        				      <strong>Well done!</strong> {$successes} users were created.
        				</div>";
		}

		// Add a message with the number of failures.
		if ($failures > 0) {
			$message .= "<div class=\"alert alert-danger\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Heads up!</strong> {$failures} users could not be created.
        				</div>";
		}

		// Add the result.
		$message .= "<div class=\"alert alert-info\" role=\"alert\">
        			      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        			        <span aria-hidden=\"true\">&times;</span>
        			      </button>
						  <strong>Oh snap!</strong> The following users were created:<br><br>
						  {$result}
        			</div>";

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'message' => $message
		);

		// Render template.
		return $this->render('/users/bulk-user-creation.mustache', $params);
	}

	/**
	 * This method load the 'list-courses' route. <br/>
	 * <b>post: </b>access to GET method. <br/>
	 * <b>post: </b>AJAX request.
	 *
	 * @param int $userid - the user id
	 */
	public function getListCourses($userid = 0)
	{
		// Create a log channel.
		$log = new Logger('App');
		$log->pushHandler(new StreamHandler(__DIR__ . '/../../logs/error.log', Logger::ERROR));

		try {
			header_remove();
			http_response_code(200);
			header('HTTP/1.1 200 OK');
			header('Content-Type: application/json');

			// Execute and parse the query.
			return json_encode(enrol_get_users_courses($userid));
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
	 * This method load the 'list-courses-user' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListCoursesUser()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

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
			'users' => $users
		);

		// Render template.
		return $this->render('/users/list-courses-user.mustache', $params);
	}

	/**
	 * This method load the 'suspend-user' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getSuspendUser()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

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
			'users' => $users
		);

		// Render template.
		return $this->render('/users/suspend-user.mustache', $params);
	}

	/**
	 * This method load the 'suspend-user' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postSuspendUser()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// Define the count variables.
		$successes = 0;
		$failures = 0;

		if (isset($_POST['users'])) {
			// Loop through the users.
			foreach ($_POST['users'] as $userid) {
				try {
					$DB->set_field('user', 'suspended', '1', ['id' => $userid]);

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
        				      <strong>Well done!</strong> {$successes} users were suspended.
        				</div>";
		}

		// Add a message with the number of failures.
		if ($failures > 0) {
			$message .= "<div class=\"alert alert-danger\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Heads up!</strong> {$failures} users could not be suspended.
        				</div>";
		}

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
			'users' => $users,
			'message' => $message
		);

		// Render template.
		return $this->render('/users/suspend-user.mustache', $params);
	}
}
