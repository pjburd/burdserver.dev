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

BURDShell: Installation of shell only
=====================================

IMPORTANT
=========
You should only run shell commands as 'root'.
Recommended set up is 'network static' on a 'bridged network' to begin with.

SHELL INSTALLATION
==================	

1) Extract and copy /BURDShell/ folder into /home/sysadmin/

2) Make shell.php executable

	cd /home/sysadmin/BURDShell
	chmod +x shell.php

2) Create symbolic link to shell command

	cd /usr/local/bin/
	ln -s /home/sysadmin/BURDShell/shell.php

3) Set up rlwrap, this allows for history and detecting 'backspace' and other control keys

	sudo apt-get install rlwrap
	
	//!!!IMPROTANT make sure you are 'sysadmin' and not 'root'!!!!!!
	cd ~
	vi .bash_aliases
		--Add the following line----
		alias shell.php='rlwrap -a shell.php'
		--Add the following line----		
	:wq

4)SVN port open [Optional] (WIP) Currently cannot commmit to server yet

	//Any one can acceess the repo
	sudo ufw allow 3690
	
	OR
	
    // Allow private network on svn(port)
    sudo ufw allow from 192.168.1.0/24 to any port 3690

USUAGE
======
Typical senrio for setting your first project.

1. Start the shell
	//No project set
	shell.php

	OR
	//To set a desired project enviroment	
	shell.php projectname.dev
	
2. Create a site project	
	site create
		[Then type site domain name e.g. 'sandbox.dev']

3. Create repo for site project
	svn create
		[Then type site domain name e.g. 'sandbox.dev']
		
5. Backup site repo 	
	backup svn
		[Then type site domain name e.g. 'sandbox.dev']
	
Restore repo
------------

1. Start the shell
	shell.php
	
2. Make sure you have uploaded for the backup into /home/sysadmin/backups/

3. Restore backup


	
SVN
----
//[From server]Start svn deamon
svnserve -d

//[From client]check out a repo
cd into/workspace/folder
svn co svn://burdserver.dev/home/sysadmin/svn/sandbox.dev/trunk sandbox.dev --username sysadmin

//[From client] export a repo
cd into/workspace/folder
svn export svn://burdserver.dev/home/sysadmin/svn/sandbox.dev/trunk sandbox.dev

//[From client]add files
svn add index.php

//[From client]To commit a change
svn commit -m "Added index file"


TROUBLESHOOTING
===============

Network unreachable via ping or ssh
------------------------------------
The network setting on the virtual machine must be visible to other machines.
On VMWARE: Network Adaptor should be 'Bridged networking' set to  'auto detect' to pick up 'static' details(need to verify this).

Unauthorisd for SVN
-------------------
Make sure you have
	run shell command 'svn security' for the chosen site project.

Opened up port 

You see "sh: 0: getcwd() failed: No such file or directory"
-----------------------------------------------------------
You are running the shell.php within the working directory that has been deleted.
Exit the shell.php and change to another folder.
	
	e.g. #>cd /home/sysadmin/
		 #>shell.php
		  

Connection refused for svn client
----------------------------------
You recieve an error from your SVN client that it cannot find.

	svn: E000061: Unable to connect to a repository at URL 'svn://burdserver.dev/home/sysadmin/svn/burdserver.dev/trunk'
	svn: E000061: Can't connect to host 'burdserver.dev': Connection refused


To resolve this, you need to start the SVN server but running the following BURDShell command:

	SHELL>svn serve
	