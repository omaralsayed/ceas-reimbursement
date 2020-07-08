# CEAS Reimbursement
The reimbursement form allows students to submit reimbursement requests to the [College of Engineering and Applied Science Tribunal](https://tribunal.uc.edu), an engineering student council organization at University of Cincinnati. This form streamlines the reimbursement process and can be completed within minutes from any computer or mobile device.

## Use Case
### Data Flow
The form's data such as the student's information, expenditure information, and supporting files will be sent to the treasurer's email. The data will be stored in a database table along with other information such as the date of the request.

## Getting Started 🚀
### Initial Set Up
Prerequisites:
- MAMP
- VS Code
- Node.js

1. `cd` into the MAMP folder.
2. `git clone https://github.com/omaralsayed/ceas-reimbursement.git` and click enter to clone repo (alternatively, you can use the SSH clone if you have that set up).
3. `cd` into htdocs.
4. Run `npm install` to install all of the needed modules for the project.
5. Run `npm install gulp-cli -g` to install Gulp globally.
6. Next, run either `gulp dev` or `gulp watch`. Gulp watch will compile the javascript file and CSS into a minified cross-browser compatible code while gulp dev will not. It is recommended to use gulp dev for development to make debugging easier and reserve gulp watch for the final production code.
7. Start the MAMP Server and click on "Open WebStart page". 
8. On the newly opened MAMP webpage, go to Tools -> phpMyAdmin.
9. Create a database called "tribunal" by clicking on "new" from the left-hand-side panel. Then, add in the name "tribunal" for the database name and select "utf8_general_ci" as the collation, and click "create".
10. Click on the newly created tribunal database from the left-hand-side panel and click on import from the top toolbar. Import all the files from the schema folder (htdocs/schema). This will create the necessary tables for you.
11. Now create a PHP file which will allow you to connect to the MAMP database. `cd` into api/includes and create a mysqli.php file. For development, the contents of the PHP file can like look like this:
```
<?php
//mysqli database connection

// Development
DEFINE('DB_USERNAME_DEV', 'root');
DEFINE('DB_PASSWORD_DEV', 'root');
DEFINE('DB_HOST_DEV', 'localhost');
DEFINE('DB_DATABASE_DEV', 'tribunal');

// Production
DEFINE('DB_USERNAME_PROD', '');
DEFINE('DB_PASSWORD_PROD', '');
DEFINE('DB_HOST_PROD', '');
DEFINE('DB_DATABASE_PROD', '');

$mysqli = new mysqli(DB_HOST_DEV, DB_USERNAME_DEV, DB_PASSWORD_DEV, DB_DATABASE_DEV);

if (mysqli_connect_error()) {
    die('Connect Error ('.mysqli_connect_errno().') '.mysqli_connect_error());
}
?>
```
For production, you will need to provide the missing constants and update the database connection to use those.

12. To set up linting, open VS Code and install the ESLint extension. Reload VS Code afterwards.
13. Open up the htdocs directory in VS Code to start developing.
14. To see the webpage, click on "Open WebStart page" from the MAMP Server and click on "My Website".

### Making Changes
1. Make your desired change.
2. Make sure to either run `gulp dev` or `gulp watch`.
3. Visit the webpage and clear browser cache (<kbd>⌘</kbd>+<kbd>Shift</kbd>+<kbd>r</kbd> or <kbd>Ctrl</kbd>+<kbd>Shift</kbd>+<kbd>r</kbd>).
