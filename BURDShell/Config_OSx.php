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
							
class Config {

	/******************
	 * Shell settings *
	 ******************/   
	 
	public static $shell_os = 'OSx';					 // Currently only 'Ubuntu' or 'OSx' avialable.

    public static $shell_host_domain = 'burdserver.dev'; // Helps with spitting out helpful info and calling servers
    public static $shell_user = 'sysadmin';			  	 // Main shell user that has 'root' priviledges.  This is the SVN user also    
    public static $shell_group = 'staff';				 // Main shell user's group priviledge
    public static $shell_pass = 'Password1';			 // This is for SVN password.

	public static $shell_admin_check = TRUE;					 // If TRUE, then check user has sudo bash'd before allowing shell prompt
	
	public static $debug = FALSE;						 // Shell debug.  Verbose commadns, and all main commands are disabled other than help.

	/*************
	 * locations *
	 *************/    
    public static $shell_folder = '/Users/sysadmin/burdserver.dev';				 // Location to shell files and folders. Don't include leading forward slash.        
    public static $app_folder = '/Users/sysadmin/burdserver.dev/BURDShell/apps'; // App folder location. Don't include leading forward slash.  
    public static $backup_folder = '/Users/sysadmin/burdserver.dev/backups';	 // Backup folder location. Don't include leading forward slash.

	public static $virtualhost_dir = '/Users/sysadmin/burdserver.dev/vhosts/'; 	// Must include forward slash

	public static $svn_bin_path = '/usr/bin/';									 // Must include forward slash
	public static $mysql_bin_path = '/usr/local/mysql/bin/';					 // Must include forward slash	


	/*******************
	 * Network support *
	 *******************/
	public static $virtual_machine = FALSE;		// If TRUE then network switching commands are enabled 'network static' and 'network dynamic'	 
	public static $network_interface = 'en0';	// Primary network device


	/********************
	 * Database support *
	 ********************/
	public static $database_on = FALSE;		// If TRUE then database commands is allowed.   If enabled, then you neet set your db admin pass
    public static $db_admin_pass = '';   	// Database user password


}