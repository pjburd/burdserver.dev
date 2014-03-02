#! /usr/bin/php
<?php
/*
    BURDShell: Developer platform shell
    Copyright (C) 2014  Paul Burden

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// Require shell files

if (file_exists("/Users/") && file_exists("/Applications/"))		// quick check if we in an OSx environment
{
	require_once("Config_OSx.php");
}
else
{
	require_once("Config_Ubuntu.php");					// Include the standard 'Ubuntu' config
}

require_once("BURDShell.php");
require_once("BURDShell_interface.php");
require_once("BURDShell_".Config::$shell_os.".php");

//Detect project
$project = "";
if (is_array($argv) && isset($argv[1]))
{
	$project = $argv[1];
}
	
//Start shell
$shell = new BURDShell($project, Config::$shell_os);
