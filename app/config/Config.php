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
 * @version $Revision: 0.2.1 $ $Date: 02/11/2021 $
 * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
 */

try {
	// Start dotEnv instance.
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../../');
	$dotenv->load();

	// Sets debug mode.
	define('DEBUG', $_ENV['MODE_DEBUG'] === 'true');

	// Sets the Domain consntant.
	define('DOMAIN', $_ENV['DOMAIN']);

	// Sets the Company constant.
	define('COMPANY', $_ENV['COMPANY']);

	// If you want to run phpunit, uncomment this
	// define('CLI_SCRIPT', true);

	// Gets Moodle Config.
	require_once($_ENV['MDL_CONFIG']);

	global $CFG;

	// Sets database config.
	define('DB_HOST', $CFG->dbhost);
	define('DB_USER', $CFG->dbuser);
	define('DB_PASS', $CFG->dbpass);
	define('DB_NAME', $CFG->dbname);
} catch (\Throwable $e) {
	header_remove();
	http_response_code(500);
	header('HTTP/1.1 500 Not Found');
	echo '<pre>' . $e->getTraceAsString() . '</pre>';
	echo PHP_EOL;
	echo $e->getMessage();
}
