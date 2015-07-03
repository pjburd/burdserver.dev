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

class BURDShell_database extends BURDShell_Plugin
{
    public $commands = array('list' => 'slist',
                             'create',
                             'delete' => 'sdelete'); 
    

    /*
     * @access public
     *
	 * @return	object
     */        
    public function help() 
    {
    	$out = "";

		$out .= "database list   : List known databases\n";
		$out .= "database create : Create a database for a project environment\n";
		$out .= "database delete : Drop a database for a project environment\n";

        $out .= "\n";
        return $out;            
    }
    
	public function slist() 
	{		 	    
    	if (Config::$db_admin_pass) 
		{
			$pass = Config::$db_admin_pass;
		}
		else
		{
			$pass = "";
		}		

	    exec("echo \"SHOW DATABASES;\" | ".Config::$mysql_bin_path."mysql -u root -p".$pass." -t", $out_lines);	    
		$this->print_output($out_lines);					
		
	}

	public function create()
	{
		if ($user_input = $this->validate_project("create"))
		{

			if (in_array($user_input, array("information_schema","mysql","performance_schema","test"))) 
			{
				$this->print_line("[ERROR] You cannot use '".$user_input."', this is a default database system requirement.");
			} 
			else 
			{	
			    exec(Config::$mysql_bin_path."mysqladmin --verbose=TRUE -u root -p".Config::$db_admin_pass." create ".$user_input, $out_lines);	    
				$this->print_output($out_lines);						
			}

        }
    }	
	
	
	public function sdelete()
	{
		if ($user_input = $this->validate_project("create"))
		{

			if (in_array($user_input, array("information_schema","mysql","performance_schema","test"))) 
			{
				$this->print_line("[ERROR] You cannot use '".$user_input."', this is a default database system requirement.");
			} 
			else 
			{	
			    exec(Config::$mysql_bin_path."mysqladmin --verbose=TRUE -u root -p".Config::$db_admin_pass." drop ".$user_input." -f", $out_lines);	    
				$this->print_output($out_lines);					
			}			
        }
    }	
	
	
//!TODO: Need to verify if we really need to allow this feature (security).
/*
	public function security() {
  		if ($user_input = $this->validate_project("security"))
		{    	
            if (Config::$db_admin_pass) 
        	{
        		$pass = Config::$db_admin_pass;
        	}
        	else
        	{
        		$pass = "";
        	}		
        
            exec("echo \"GRANT SELECT , INSERT , UPDATE , DELETE ON  `sandbox.dev` . * TO  'dbuser'@'%';\" | mysql -u root -p".$pass." ".$user_input, $out_lines);	    
        	$this->print_output($out_lines);			    	
    	}
	}
*/	
    /*
     * Validate a project
     *
	 * @param	string $verb                       Type of action to format question text
	 * @param	string $override_user_input        Input previous user's input      
     *
	 * @return	object
     */         
	private function validate_project($verb, $override_user_input="") 
	{    	 
		
		$check_config = FALSE;
		$check_config_verbs = array('create');
		                            
        $verb_text = "";
        switch($verb) { 
            case 'create':                                
                $verb_text = " to ".$verb." database";
                break;
        }

		$user_input = $this->project_prompt("Which site" .$verb_text ."?", $override_user_input);
		
		if (in_array($verb, $check_config_verbs))
		{
    		$check_config = TRUE;
		}
		
		if (empty($user_input)) 
		{
			$this->print_line("Site domain name missing.","e");	
		} 
		else 
		{
			$this->print_line("Validating site...","i");
			
			if (!$this->valid_filename($user_input)) 
			{
				$this->print_line("The file name can only contain \"a-z\", \"0-9\", \".\" and \"-\" and must be lower case","e");
			}
			else
			{ 
				if (in_array($user_input, array("localhost","default","default-ssl"))) 
				{
					$this->print_line("You cannot use '".$user_input."', this is a default web server virtualhost."."e");
				} 
				else 
				{ 
    				//Make sure the file exists
					if ($config_test && file_exists( Config::$virtualhost_dir.$user_input.".conf")) 
					{
						$this->print_line("site domain '".$user_input."' already exists. Run 'webserver sites' for further info.","e");    					
    				}
    				else
    				{
        				return $user_input;        				
    				}

                }
            }
        }
        return FALSE;
	}        
}