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

        // Create a new filter for md5 encoding.
		$this->templateEngine->addHelper('md5', function (string $text) {
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
	protected function render($fileName, $data = [])
	{
        // Render the template.
		return $this->templateEngine->render($fileName, $data);
	}
}
