=== Accu Auto Backup ===
Contributors: dhanashreeinc
Tags: Accu auto backup,auto backup,Database backup,db backup, backup,database, WordPress Database Backup, WP db backup,wp database backup,wp backup,wordpress backup, mysql backup,automatically database backup,website backup,website database backup
Requires at least: 4.0
Tested up to: 5.9
Requires PHP: 7.0
Stable tag: 1.0.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Accu auto backup plugin takes wordpress database backup automatically with define specific schedule. Manual or automated backups option available.

== Description ==

* Accu auto backup allow you to take backup as per schedule like ( Daily,Weekly,Monthly.. ).
* All taken backup is stored in zip format, you can download.
* It will allow you to take backup using two method in which one will be through automation and another have manual.

= Major Features =
<ul>
<li>Create Database Backup
Accu Auto Backup plugin helps you to create Database Backup easily on single click.</li>

<li>Create and Manage automation cron by plugin.
It will manage your linux cron automatically by adding cron to automate backup schedule.</li>

<li>You can also manage backup automation from other cron job providing web-sites like setcronjob.com etc.</li>

</ul>

= Requirements =
* WordPress 4.0 and PHP 7.X required!
* Linux based Hosting having shell_exec permission enabled.
* For windows based hosting you can have only manual option avaliable.


= Accu Auto Backup Premium =

<strong>Our free version is great, but we also have a more powerful Premium version with extra features that offer the ultimate flexibility:</strong>

* Instant Backup for database, files and folders.
* Backup your shop database, files and folders.
* Automatic backup.
* Set specific interval (ex. daily, weekly, and monthly) to start the backup.
* Backup creating in ZIP format so easily restored it.

Accu Auto Backup Premium is available for purchase <a href="https://codecanyon.net/item/accu-auto-backup-pro/22712190" rel="nofollow">here</a>

== Frequently Asked Questions == 

= Installation Instructions =

<ol>
<li>Install Accu-Auto-Backup either via the WordPress.org plugin directory, or by uploading the files to your server.</li>
<li>Activate the plugin.</li>
<li>Go To Tools > Accu Auto Backup > General Setting then prefer your settings and click on save settings.</li>
</ol>

The plugin will try to use the zip and shell_exec commnads via shell if they are available, using these will greatly improve the time it takes to back up your site and setting the cron.

= What is the format or extension of database backup file? =

It will have .zip extension in which .sql file kept.

= Where does Accu-Auto-Backup store the backup files? =

Backups are stored on your server in /wp-content/uploads/accu_auto_backup.

Important: By default our plugin backs up your all database tables.


= What is CRON Job

Cron jobs are scheduled tasks that the system runs at predefined times or intervals. Typically, a cron job contains a series of simple tasks that the system runs from a script file. 

= How can I setup CRON in cPanel with manual mode selection from General Setting? = 


<ol>
<li>Log on to your cPanel Interface.</li>
<li>Go to 'Advanced' section.</li>
<li>Click on 'Cron Jobs'.</li>
<li>Select the specific time from the lists provided.</li>
<li>You should enter the command [Copy "Cron Path" line from General Setting of module] to run in the "Command" field.</li>
</ol>

== Changelog ==

= Version 1.0.4 =
* Compatible with Wordpress 5.9
* Design Improvements.
* Minor Bug fixing.

= Version 1.0.3 =
* Design Improvements.
* Minor Bug fixing.

= Version 1.0.2 =
* Wordpress code standard compatible.
* Performance improvement.
* Minor Bug fixing.

= Version 1.0.1 =
* Minor Bug fixing.

= Version 1.0.0 =
* First release.


== Upgrade Notice ==

== Screenshots == 
1. General Settings Section.
2. Auto Option selection in General Setting.
3. Manual Option selection in General Setting.
