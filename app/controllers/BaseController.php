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

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

/**
 * BaseController class.
 */
class BaseController
{

	protected $templateEngine;

	/**
	 * This method construct a new Controller.
	 */
	public function __construct()
	{
        // Setting an intance for Mustache Engine.
		$this->templateEngine = new Mustache_Engine(
			array(
				'loader' => new Mustache_Loader_FilesystemLoader(
					__DIR__ . '/../views'
				),
			)
		);

        // Create a new filter for md5 decoding.
		$this->templateEngine->addHelper('md5', function ($text) {
			return md5(strtolower(trim($text)));
		});
	}

	/**
	 * This method render the template.
	 *
	 * @param string $filename - the filename of template.
	 * @param array $params - the data with context of the template.
	 * @return string the template rendered.
	 */
	public function render($fileName, $data = [])
	{
        // Render the template.
		return $this->templateEngine->render($fileName, $data);
	}
}
