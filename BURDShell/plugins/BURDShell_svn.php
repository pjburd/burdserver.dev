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

class BURDShell_svn extends BURDShell_Plugin
{
    public $commands = array('help' => 'shelp',
                             'list' => 'slist',
                             'security',
                             'serve',
                             'history',
                             'log' => 'slog',
                             'users',
                             'permissions',
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

        $out .= "svn list        : List known svn repos\n";
        $out .= "svn create      : Create a new repo for a project environment\n";
        $out .= "svn delete      : Delete a repo for a project environment\n";
        $out .= "svn history     : View repo history for a project environment\n";
        $out .= "svn revision    : View repo revision for a project environment\n";
        $out .= "svn log         : View repo log for a project environment\n";
        //Hidden as we automatically do this when  shell commands 'svn create' and 'restore svn' are executed
        //				$out .= "svn security : Set up security for a site repo project environment\n";
        $out .= "svn permissions : List user permissions for a project environment\n";
        $out .= "svn users       : List users for a project environment\n";
        $out .= "svn grants      : List permissions for a project environment\n";
        
        $out .= "svn serve       : Start SVN service deamon\n";

        return $out;            
    }

    public function shelp() 
    {
		if ($user_input = $this->validate_project("help")) 
		{
    		$out .= "cd /in/to/work/folder/ and then run either following:";							
    		$out .= "\n\n## Checkout project ##\nsvn co svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/trunk ".$user_input." --username ".Config::$shell_user;
    		$out .= "\n\n## Export project ##\nsvn export svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/trunk ".$user_input." --username ".Config::$shell_user;
    		$out .= "\n\n## Show history ##\nsvn log svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input." --username ".Config::$shell_user." --no-auth-cache";
    		$out .= "\n\n## Tag release 1.0 sample ##\nsvn copy svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/trunk svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/tags/1.0 --username ".Config::$shell_user." -m \"Release 1.0\"";						
    		$out .= "\n\n## Delete tag sample ##\nsvn delete svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/tags/1.0 --username ".Config::$shell_user." -m \"Deleted release 1.0\"";						
    		$out .= "\n\n## Create branch 'prototype' sample ##\nsvn copy svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/trunk svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/branches/prototype --username ".Config::$shell_user." -m \"Created branch 'prototype'\"";						
    		$out .= "\n\n## Delete branch 'prototype' sample ##\nsvn delete svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/branches/prototype --username ".Config::$shell_user." -m \"Deleted branch 'prototype'\"";
    		$out .= "\n\n## Commit changes ##\nsvn commit";
    		$out .= "\n\n## Show status ##\nsvn status";
    		$out .= "\n\n";				

            return $out;            
    		
        }                
    }
    
    public function serve() {
	    exec(Config::$svn_bin_path."svnserve -d", $out_lines);	    
		$this->print_output($out_lines);	 	
    }

	public function slist() 
	{		 
	    exec("ls -l ".Config::$shell_folder."/svn/", $out_lines);	  
	    
        if (!empty($out_lines)) 
        {
            $this->print_line($out_lines);
        }
        else
        {
            $this->print_line("No sites found","i");
        }
	}	
	
	public function create()
	{
		if ($user_input = $this->validate_project("create"))
		{

    	    exec("mkdir ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
    		$this->print_output($out_lines);
    		
    		//Create repo
    	    exec(Config::$svn_bin_path."svnadmin create ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
    		$this->print_output($out_lines);	 											     
    
    		exec("svn mkdir -m\"Created basic directory structure\" file:///".Config::$shell_folder."/svn/".$user_input."/trunk file:///".Config::$shell_folder."/svn/".$user_input."/branches file:///".Config::$shell_folder."/svn/".$user_input."/tags", $out_lines);						
    		$this->print_output($out_lines);	 											     
    
    		$this->security($user_input);
    
    		$this->print_line("Site repo created.","i");
    		
    		$this->print_line("Checkout project:\nsvn co svn://".Config::$shell_host_domain.Config::$shell_folder."/svn/".$user_input."/trunk ".$user_input." --username ".Config::$shell_user,"i");
        }        
	}
	
	public function security($override_user_input="")
	{
		if ($user_input = $this->validate_project("security", $override_user_input))
		{
    		/*
    			[/svn/PROJECTFOLDER/conf/svnserve.conf]
    			anon-access = none
    			auth-access = write
    			password-db = passwd
    		*/
    		exec("sed -i .bk 's/^# auth\-access \= write/anon\-access \= none\\nauth\-access \= write/' ".Config::$shell_folder."/svn/".$user_input."/conf/svnserve.conf");
    		exec("sed -i .bk 's/^# password\-db \= passwd/password\-db \= passwd/' ".Config::$shell_folder."/svn/".$user_input."/conf/svnserve.conf");
    
    		/*
    			[/svn/PROJECTFOLDER/conf/passwd]
    			sysadmin = Password1
    		*/
    		$security_lines = Config::$shell_user." = ".Config::$shell_pass."\n";
    		file_put_contents(Config::$shell_folder."/svn/".$user_input."/conf/passwd", $security_lines, FILE_APPEND | LOCK_EX);
    		
    		/*
    			[/svn/PROJECTFOLDER/conf/authz]
    			allaccess = sysadmin
    			[/]
    			@allaccess = rw
    		*/							
    		$security_lines = "allaccess = ".Config::$shell_user."\n[/]\n@allaccess = rw\n";
    		file_put_contents(Config::$shell_folder."/svn/".$user_input."/conf/authz", $security_lines, FILE_APPEND | LOCK_EX);
    		
    		$this->print_line("Site repo security setup successfully.","i");

        }
	}
	
	
	public function sdelete()
	{
		if ($user_input = $this->validate_project("delete"))
		{
            $this->print_line("Deleting site repo...");
    	    exec("rm -Rf ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
    		$this->print_output($out_lines);	 
    		$this->print_line("Site repo deleted.","i");	
        }
	}

	public function history()
	{
		if ($user_input = $this->validate_project("history"))
		{
            exec(Config::$svn_bin_path."svnlook history ".Config::$shell_folder."/svn/".$user_input." --show-ids", $out_lines);	   
    		$this->print_output($out_lines);	
        }
	}

	public function slog()
	{
		if ($user_input = $this->validate_project("log"))
		{
            $revision = "";

    		if (count($this->directive['args']) == 3)
    		{	
    			$revision = " -r ".$this->directive['args'][2];
    		}
    		
    		if (empty($revision))
    		{
    		    exec(Config::$svn_bin_path."svnlook youngest ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
    			
    			$this->print_line("svn log");
    			$this->print_line("=======");
    			$this->print_line("Youngest revision:".$out_lines[0]."\n");
    		}						
    		$out_lines = array();			
    		
    		if ($revision) 
    		{			
    			$this->print_line("Revision:".$this->directive['args'][2]."\n");
    		}
    	    exec(Config::$svn_bin_path."svnlook log".$revision." ".Config::$shell_folder."/svn/".$user_input, $out_lines);	   
    		$this->print_output($out_lines);	 
        }
	}

	public function revision()
	{
		if ($user_input = $this->validate_project("revision")) {
            if (count($this->directive['args']) == 3)
    		{
    			$revision = $this->directive['args'][2];
    		}
    		else
    		{					
    			echo "Which revision do you wish to view? (shell hint:'svn history')\n";
    			$revision = trim(fgets(STDIN));	
    		}
    		
    		exec(Config::$svn_bin_path."svnlook changed ".Config::$shell_folder."/svn/".$user_input." --revision ".$revision, $out_lines);	   
    		if (count($out_lines))
    	    {
    			$this->print_line("--- FILES CHANGED ---");	
    	    }
    		$this->print_output($out_lines);	 
    		
    		$out_lines = array();
    	    exec(Config::$svn_bin_path."svnlook log ".Config::$shell_folder."/svn/".$user_input." --revision ".$revision, $out_lines);	   
    	    
    	    if (count($out_lines))
    	    {
    			$this->print_line("--- LOG MESSAGE ---");	
    	    }
    		$this->print_output($out_lines);			
        }
	}

	public function users()
	{
		if ($user_input = $this->validate_project("users")) 
		{
    	    exec("cat ".Config::$shell_folder."/svn/".$user_input."/conf/passwd", $out_lines);
    		$this->print_output($out_lines, TRUE);
        }
    }
    
	public function permissions()
	{
		if ($user_input = $this->validate_project("permissions")) 
		{
            if (count($this->directive['args']) == 3)
    		{
    			$svn_user = $this->directive['args'][2];
    		}
    		else
    		{					
    			echo "Which user to look up permissions? (shell hint:'svn users')\n";
    			$svn_user = trim(fgets(STDIN));	
    		}
    		//Read in authz
    		
    	    exec("cat ".Config::$shell_folder."/svn/".$user_input."/conf/authz", $out_lines);
    	    
    	    $tidy_output = $this->tidy_flat_output($out_lines);
    	    
    	    
    	    // Search for 'username entries'
    	    $access_roles = array();					// aka 'alias' in svn conf
    	    foreach($tidy_output as $out_line)
    	    {				    	
    		    if (preg_match("/[\=]*".quotemeta($svn_user)."/", $out_line))
    		    {
    				$elements = explode("=", $out_line);
    				$access_roles[] = trim($elements[0]);
    				// Now saerch for find directory they allowed
    				
    		    }
    	    }
    	    if (count($access_roles)) 
    	    {
    			    				    
    		    // Find directories and rw
    		    $permissions = array();					// aka 'grants' in svn conf
    		    $repo_folders = array();
    		    
    		    foreach($access_roles as $access_role)
    		    {
    		    	$ctr = 0;
    		    	foreach($tidy_output as $out_line)
    				{
    					if (preg_match("/\@".quotemeta($access_role)."/", $out_line))
    				    {
    						$elements = explode("=", $out_line);
    						$permissions[] = trim($elements[1]);
    						
    						
    						// Now locate rep folder
    						$ctr_tmp = $ctr - 1;			// Mark current index of output
    						while ($ctr_tmp > 0) 
    						{
    							if (!preg_match("/\[*\]/", $tidy_output[$ctr_tmp]))
    							{
    								$ctr_tmp--;
    							}
    							else
    							{
    								$repo_folders[] = trim($tidy_output[$ctr_tmp]);	// Get the line above found grant above
    								break;		// Lets exit this 'locate repo folder' loop 
    							}
    						}
    				    }
    				    $ctr++;
    				}
    		    }
    		    
    		    //Print out permissions
    		    $permissions_output = array();
    			$ctr = 0;
    			if (is_array($repo_folders))
    			{
    				foreach ($repo_folders  as $repo_folder)
    				{
    					$permissions_output[] = $access_roles[$ctr]." ".$repo_folder." ".$permissions[$ctr];
    					$ctr++;
    				}
    			}					    
    			$this->print_output($permissions_output);	 
    		
    	    }	
    	    else
    	    {
    		    $this->print_line('[ERROR] SVN user not found.');
    	    }

        }
    }

	public function grants()
	{
		if ($user_input = $this->validate_project("grants")) 
		{
    	    exec("cat ".Config::$shell_folder."/svn/".$user_input."/conf/authz", $out_lines);	
			$this->print_output($out_lines);	     		
		}
    }    
    

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
		$check_config_verbs = array('delete',
		                            'help',
		                            'security',
		                            'history',
		                            'revision',
		                            'users',
		                            'permissions',
		                            'grants',
		                            'create',
		                            'log');
		                            
        $verb_text = "";
        switch($verb) {
            case 'help':
            case 'history':
            case 'revision':
            case 'users':
            case 'grants':
            case 'log':            
            case 'permissions':
                $verb_text = " to show ".$verb;
                break;
            case 'security':              
                $verb_text = " to set ".$verb;
                break;    
            case 'delete':                
                $verb_text = " to ".$verb;
                break;
            case 'create':                
                $verb_text = " to ".$verb." repo";
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
					if ($check_config == TRUE && !file_exists(Config::$shell_folder."/svn/".$user_input)) 
					{ 
						$this->print_line("site project repo does not exist.","e");	
					}
					elseif ($verb == "create" && file_exists(Config::$shell_folder."/svn/".$user_input)) 
					{ 
						$this->print_line("site project repo does exits exist.","i");	
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