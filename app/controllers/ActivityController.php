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

/**
 * ResourceController class
 *
 * @extends BaseController
 */
class ActivityController extends BaseController
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
	public function getListActivities()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count users.
		$sql = "SELECT		{course}.id,
							{course}.fullname,
				        	{modules}.name,
				        	COUNT({modules}.id) AS activities
				FROM		{course_modules}
				JOIN		{course}
					ON		{course_modules}.course = {course}.id
				JOIN		{modules}
					ON		{course_modules}.module = {modules}.id
				GROUP BY	{course}.id, {modules}.name";

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
		return $this->render('/activities/list-activities.mustache', $params);
	}
}
