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

class BURDShell_network extends BURDShell_Plugin
{
    public $commands = array('ip' => 'ipa', 
                             'status', 
                             'help',
                             'dynamic',
                             'n_static');   // TODO: Need to make alias system for command to right as 'static'

    /*
     * @access public
     *
	 * @return	object
     */        
    public function help() 
    {
    	$out = "";

		if (Config::$virtual_machine == TRUE)
		{
			$out .= "network n_static: Set network interface to be static IP\n";
			$out .= "network dynamic : Set network interface to be dynamic IP\n";
			$out .= "network restart : Restart network\n";
		}    	
	    $out .= "network ip      : Show IP Address\n";
	    $out .= "network status  : Show network status\n";
	
        return $out;            
    }
    
    
	public function ipa() {
    	$out = "";
    	switch (Config::$shell_os)
    	{
        	case 'Ubuntu':
        		 $out = exec("/sbin/ifconfig " . Config::$network_interface . " | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");		 
        	    break;
            case 'OSx':
        		 $out = exec("ipconfig getifaddr ". Config::$network_interface);
                break;        	
    	}

		 return $out;
	}
	
	public function status() {
    	$out = "";
    	switch (Config::$shell_os)
    	{
        	case 'Ubuntu':
        	case 'OSx':
        		$out = exec("/sbin/ifconfig");
                break;
        }
		 return $out;
	}

	public function dynamic() 
	{
    	$out = "";
    	
		if (Config::$virtual_machine == TRUE)
		{		      
        	switch (Config::$shell_os)
        	{            	
            	case 'Ubuntu':
            	    $cmd = "cp /etc/network/interfaces.dynamic /etc/network/interfaces";
            	    break;
            	case 'OSx':
            	    $cmd = "";
            	    break;
            }    	
        	    		
    		if (!$cmd)
    		{
        		$this->print_line("Command disabled for ".$this->os_version, "i");
    		}
    		else
    		{
    			$out = $this->admin_exec($cmd);	 			   	 
			}			
			
		    $this->network_restart();   
	    }
	    else
	    {
			$this->print_line("Shell is not in virtual machine environment.", "i");	
	    }
	    return $out;
	}	

	public function n_static() 
	{
    	$out = "";

//!BUG: "restart" does not exist 
//!TODO: Must correct networking restart as it does not exist - Need to create a sub wrapper to run "stop" and "start" seperately.  	
    	
		if (Config::$virtual_machine == TRUE)
		{			
        	switch (Config::$shell_os)
        	{            	
            	case 'Ubuntu':
            	    $cmd = "cp /etc/network/interfaces.static /etc/network/interfaces";
            	    break;
            	case 'OSx':
            	    $cmd = "";
            	    break;
            }    	
        	
        	    		
    		if (!$cmd)
    		{
        		$this->print_line("Command disabled for ".$this->os_version, "i");
    		}
    		else
    		{
    			$out = $this->admin_exec($cmd);	 			   	 
			}
		    $this->restart();   
	    }
	    else
	    {
			$this->print_line("Shell is not in virtual machine environment.", "i");	
	    }
	    
	}	
	
	public function restart() {		
    	$out = "";
		if (Config::$virtual_machine == TRUE)
		{
			$out = "\nRestarting network:";
			

        	switch (Config::$shell_os)
        	{
            	case 'Ubuntu':
            	    $cmd = "/etc/init.d/networking restart";
            	    break;
            	case 'OSx':
            	    $cmd = "";
            	    break;
            }    				
			

    		if (!$cmd)
    		{
        		$out .= "Command disabled for ".$this->os_version;
    		}
    		else
    		{
    			$out = $this->admin_exec($cmd);	 			   	 
			}
	    }
	    else
	    {
			$this->print_line("Shell is not in virtual machine environment.", "i");	
	    }
	    
	    return $out;		    
	}
	

}