#################
Simple Task Board
#################

- Version: 1.3.3
- Link: http://oscardias.com/projects/simple-task-board/
- Author: Oscar Dias
- Website: http://oscardias.com

***********
Description
***********

A simple task board to help managing software projects. It is developed in PHP and uses the CodeIgniter Framework.
It includes multiple users/projects/tasks.

************
Installation
************

Create the database using db/create.sql, place the files in your server and update the following:

- application/config/config.php: update $config['base_url'] with your url.
- application/config/database.php: update your database information.
- .htaccess in your root folder: update /task_board/index.php with your URL.
- enable mod_rewrite in your Apache server.

Run <server>/install (where <server> should be your localhost or domain/folder).

If you have any problems let me know.

************
Next updates
************

I plan to update some portions of this application in the following months. Currently these are the features to be developed:

- User profile: create a user profile with more information about the users.
- Tasks estimation: add an estimation field to enable a comparison between estimated and realised.
- ... if you have any ideas, let me know ...
