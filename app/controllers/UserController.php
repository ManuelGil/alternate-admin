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
 * @version $Revision: 0.0.1 $ $Date: 01/15/2020 $
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

namespace App\Controllers;

/**
 * UserController class
 *
 * @extends BaseController
 */
class UserController extends BaseController
{

	/**
	 * This method load the 'list-users' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getListUsers()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		// Parsing the users.
		$items = addslashes(json_encode(get_users(), JSON_HEX_AMP | JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT));

		$params = array(
			'COMPANY' => COMPANY,
			'BASE_URL' => BASE_URL,
			'wwwroot' => $CFG->wwwroot,
			'USER' => $USER,
			'items' => $items
		);

		// Render template.
		return $this->render('/users/list-users.mustache', $params);
	}

	/**
	 * This method load the 'mass-user-create' route. <br/>
	 * <b>post: </b>access to GET method.
	 */
	public function getMassUserCreate()
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
		return $this->render('/users/mass-user-create.mustache', $params);
	}

	/**
	 * This method load the 'mass-user-create' route. <br/>
	 * <b>post: </b>access to POST method.
	 */
	public function postMassUserCreate()
	{
		// Imports Config and Current User.
		global $CFG, $USER;

		$prefix = (string) $_POST['prefix'];
		$separator = (string) $_POST['separator'];
		$start = (int) $_POST['start'];
		$count = (int) $_POST['count'];

		$successes = 0;
		$fails = 0;

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

		for ($i = 0; $i < $count; $i++) {
			try {
				$index = $start + $i;

				$username = "{$prefix}{$separator}{$index}";

				$data = '1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZabcefghijklmnopqrstuvwxyz';
				$password = substr(str_shuffle($data), 0, 7);

				$user = create_user_record($username, $password);

				$result .= "<tr>
							<td>{$user->id}</td>
							<td>{$username}</td>
							<td>{$password}</td>
						</tr>";

				$successes++;
			} catch (\Throwable $e) {
				$fails++;
			}
		}

		$result .= "</tbody></table></div>";

		$message = "";

		if ($successes > 0) {
			$message .= "<div class=\"alert alert-success\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Well done!</strong> {$successes} users were created.
        				</div>";
		}

		if ($fails > 0) {
			$message .= "<div class=\"alert alert-danger\" role=\"alert\">
        				      <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">
        				        <span aria-hidden=\"true\">&times;</span>
        				      </button>
        				      <strong>Heads up!</strong> {$fails} users could not be created.
        				</div>";
		}

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
		return $this->render('/users/mass-user-create.mustache', $params);
	}
}
