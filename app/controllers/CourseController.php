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
 * CourseController class
 *
 * @extends BaseController
 */
class CourseController extends BaseController
{

	/**
	 * This method load the 'list-courses' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListCourses()
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
		return $this->render('/courses/list-courses.mustache', $params);
	}

	/**
	 * This method load the 'count-editingteachers' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getCountEditingteachers()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count editingteachers.
		$sql = "SELECT		{course}.id,
							{course}.fullname AS course,
							COUNT({course}.id) AS editingteachers
				FROM		{role_assignments}
				JOIN		{context}
					ON		{role_assignments}.contextid = {context}.id
					AND		{context}.contextlevel = 50
				JOIN		{user}
					ON		{user}.id = {role_assignments}.userid
				JOIN		{course}
					ON		{context}.instanceid = {course}.id
				WHERE		{role_assignments}.roleid = 3
				GROUP BY	{course}.id
				ORDER BY	editingteachers ASC;";

		// Execute the query.
		$records = $DB->get_records_sql($sql);

		// Parsing the records.
		$items = addslashes(json_encode($records, JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'items' => $items
		);

		// Render template.
		return $this->render('/courses/count-editingteachers.mustache', $params);
	}

	/**
	 * This method load the 'count-teachers' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getCountTeachers()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count teachers.
		$sql = "SELECT		{course}.id,
							{course}.fullname AS course,
							COUNT({course}.id) AS teachers
				FROM		{role_assignments}
				JOIN		{context}
					ON		{role_assignments}.contextid = {context}.id
					AND		{context}.contextlevel = 50
				JOIN		{user}
					ON		{user}.id = {role_assignments}.userid
				JOIN		{course}
					ON		{context}.instanceid = {course}.id
				WHERE		{role_assignments}.roleid = 4
				GROUP BY	{course}.id
				ORDER BY	teachers ASC;";

		// Execute the query.
		$records = $DB->get_records_sql($sql);

		// Parsing the records.
		$items = addslashes(json_encode($records, JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'items' => $items
		);

		// Render template.
		return $this->render('/courses/count-teachers.mustache', $params);
	}

	/**
	 * This method load the 'count-students' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getCountStudents()
	{
		// Imports Config, Database and Current User.
		global $CFG, $DB, $USER;

		// SQL Query for count students.
		$sql = "SELECT		{course}.id,
							{course}.fullname AS course,
							COUNT({course}.id) AS students
				FROM		{role_assignments}
				JOIN		{context}
					ON		{role_assignments}.contextid = {context}.id
					AND		{context}.contextlevel = 50
				JOIN		{user}
					ON		{user}.id = {role_assignments}.userid
				JOIN		{course}
					ON		{context}.instanceid = {course}.id
				WHERE		{role_assignments}.roleid = 5
				GROUP BY	{course}.id
				ORDER BY	students ASC;";

		// Execute the query.
		$records = $DB->get_records_sql($sql);

		// Parsing the records.
		$items = addslashes(json_encode($records, JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'items' => $items
		);

		// Render template.
		return $this->render('/courses/count-students.mustache', $params);
	}
}
