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
==========
README.TXT
==========

About
=====
Thank you for choosing 'BURDShell' to manage your development.

BURDShell is a developer platform shell to help manage your private projects in-house before going public via a command line.

Once the platform shell is setup you can easily creating websites, projects, svn repos, and even backups via a command.
via a command line.

Usuage
======
Shell works in Ubuntu and Mac

shell.php 					: Runs the BURDShell without a project selected.  All commands will ask for your desired project.

shell.php [project name]    : Sets up your project environment so all commands will not ask which desired project.


Commands
--------
There are a few commands that utlize a 3rd parameter.   "svn revision" is one of them.

help      : Brings up help

site      : Show site management commands
webserver : Show webserver management commands
network   : Show network management commands
svn       : Show svn management commands
database  : Show database management commands
app       : Show app management commands
backup    : Show backup management commands
restore   : Show restore backup management commands
shell     : Show shell management commands
version   : Print version of BURDShell
quit      : Exit BURDShell

Software requirements
=====================
The shell can function in the following OS environments:

virtual on Ubuntu Server 12.04.3 LTS
natively on Ubuntu Desktop 12.04 LTS
natively on Mac OSx 10.9.2

All environments require the following minimum primary software to be installed:

Apache2
MySQL
SVN
rlwrap (Optional - This assists in remembering the shell commands, making development feel smoother)


Installation
============
There are two types of installation, dedicated server mirroring within a virtual machine(hard to setup), and native (easier to setup) 
		
Quick installation (Easy)
-------------------------
The following files are provided to help with quick installation

	native-install-ubuntu.txt : Quick installation of BURDShell on Ubuntu.
	native-install-osx.txt    : Quick installation of BURDShell on OSx.
	
	Both have a 'shell tour' where you create a simple site with repo.

Long installation (Hard)
------------------------
These are steps to setup a dedicate environment.  Ideal for dedicated mirroring of project system.

	server-install-steps.txt : All the main steps to creating the perfect shell environment. 
									This will set up FTP, SSL, SSH, Apache, MySQL, PHP
									There are optional extras (some not fully tested, you have been warned)
	
	shell-readme.txt		  :	Follow 'SHELL INSTALLATION' steps in 'shell-readme.txt'
	
	server-user-guide.txt     : Managing the server guide (DRAFT rough guide).

Support
=======
Documentation can be found in __documents/  folder

More documentation can be helpful.

enhancement-notes.txt	  : Thoughts and notes about future enhancements
installation.txt          : Additional installation notes for dedicated installation
shell-readme.txt          : Only steps to setting up BURDShell environment only
switching-networks.txt    : A brief guide to understanding the various ways to broadcasting your projects.

Future documentation will be also available in public/ folder

KNOWN ISSUES
============

Major security issue 
--------------------
If you run the shell not in SUDO mode, then the shell can reveal passwords. (You have been warned)

DB Root password is currently required and is not encrypted. (You have been warned)

Minor issues
------------
Mac does not have command history support as rlwrap on Macports has not been tested yet.
			// May utilze Readline in PHP.
			



