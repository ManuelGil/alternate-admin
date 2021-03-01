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
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 * $Id$
 * <p>Title: Alternate Admin for Moodle.</p>
 * <p>Description: This wrapper for Moodle adds a new interface to
 * 					streamline your administrative tasks.</p>
 *
 * @package		wrapper
 * @author 		$Author: 2021 Manuel Gil. <https://imgil.dev/> $
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

namespace App\Controllers;

/**
 * AdminController class
 *
 * @extends BaseController
 */
class AdminController extends BaseController
{

	/**
	 * This method redirect to BASE URL when access to parent section. <br/>
	 * <b>post: </b>access to any method (POST, GET, DELETE, OPTIONS, HEAD etc...).
	 */
	public function anyIndex()
	{
		redirect(BASE_URL);
	}

	/**
	 * This method load the 'list-admins' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListAdmins()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the admin users.
		$admins = addslashes(
			json_encode(
				get_admins(),
				JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT
			)
		);

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'admins' => $admins
		);

		// Render template.
		return $this->render('/admins/list-admins.mustache', $params);
	}

	/**
	 * This method load the 'users-created' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getUsersCreated()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count users.
		$sql = "SELECT		YEAR(FROM_UNIXTIME(firstaccess)) AS years,
    						COUNT(*) AS users
				FROM		{user}
				GROUP BY 	years;";

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
		return $this->render('/admins/users-created.mustache', $params);
	}

	/**
	 * This method load the 'logged-once' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getLoggedOnce()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count users.
		$sql = "SELECT		id,
        					username,
        					email,
        					firstname,
        					lastname
				FROM		{user}
				WHERE   	deleted = 0
    				AND 	lastlogin = 0
    				AND 	lastaccess > 0;";

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
		return $this->render('/admins/logged-once.mustache', $params);
	}

	/**
	 * This method load the 'logged-last-days' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getLoggedLastDays()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count users.
		$sql = "SELECT		id,
        					username,
        					email,
        					firstname,
        					lastname,
							lastlogin
				FROM		{user}
				WHERE   	DATEDIFF(NOW(), FROM_UNIXTIME(lastlogin)) < 120;";

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
		return $this->render('/admins/logged-last-days.mustache', $params);
	}

	/**
	 * This method load the 'list-suspended' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListSuspended()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count users.
		$sql = "SELECT      id,
							username,
							email,
							firstname,
							lastname
				FROM		{user}
				WHERE       suspended = 1;";

		// Execute the query.
		$records = $DB->get_records_sql($sql);

		// Parsing the users.
		$users = addslashes(
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
			'users' => $users
		);

		// Render template.
		return $this->render('/admins/list-suspended.mustache', $params);
	}
}
