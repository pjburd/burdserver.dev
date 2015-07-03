<?php
/*
    BURDShell: Developer platform shell
    Copyright (C) 2015  Paul Burden

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
class BURDShell_Ubuntu {

	public $os_version = "Ubuntu 12.04.3 LTS";

	public function write_check()
	{
		$errors = array();		

		$files = array();

		// Folders that will be required to be writable
		$files[] = Config::$virtualhost_dir; 			
		$files[] = Config::$shell_folder."/svn/";
		$files[] = Config::$site_dir;
		$files[] = Config::$backup_folder."/";

		foreach($files as $file) 
		{
		    if (!is_writable($file)) 
		    {
			    $errors[] = "[WARNING] ".$file." is not writable.";
		    }
	    }
	    
		return $errors;
	}

	public function check_shell_env($debug=FALSE) 
	{

		$errors = array();		
		$files = array();
		$write_chk = array();
		
		//System environment requirements
		$files[] = "/etc/apache2/";
		$files[] = Config::$virtualhost_dir;	
		
		if (Config::$virtual_machine == TRUE)
		{
			$files[] = "/etc/network/interfaces";
			$files[] = "/etc/network/interfaces.static";
			$files[] = "/etc/network/interfaces.original";
			$files[] = "/etc/network/interfaces.dynamic";	
		}
		
		//Folders
		$files[] = Config::$shell_folder."/svn/";
		$files[] = Config::$site_dir;
		$files[] = Config::$backup_folder."/";
		$files[] = Config::$shell_folder."/BURDShell/apps/";
		$files[] = Config::$shell_folder."/BURDShell/templates/";		
				
		//BURDShell templates
		$files[] = Config::$shell_folder."/BURDShell/templates/apache2-virtualhost.txt";
		$files[] = Config::$shell_folder."/BURDShell/templates/site-index-file.txt";

		

		if ($debug)
		{
			echo "Files n folders checked are:";
			print_r($files);
		}
						
				
		foreach($files as $file) 
		{
		    if (!file_exists($file)) 
		    {
			    $errors[] = $file." does not exist.";
			}

		}
		if (count($errors)) 
		{
			$this->errors_found = TRUE;
		}
		return $errors;
	}
}