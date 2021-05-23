###Task 1

The poster location is :

`/app/public/task-1/task-1.html`

###Task 2

######Running the application

The ATM CLI application can be run with this command :

`php artisan atm:process-and-output {filename}`

The command takes a .json file as input from the /storage/json folder, so

`php artisan atm:process-and-output test-meets-spec`

will run the command with the file `/storage/json/test-meets-spec.json`

######Testing the application

To test the application using phpunit, run the following command :

`vendor/bin/phpunit tests/Feature/AtmTest.php`

There are 3 feature tests in this file.

`testMeetsSpecification()` 

The input file matches the input file in the specification, and the output file also matches
the file in the specification.

`testAtmErr()`

An attempt to take more money than in available in the ATM results in "ATM_ERR".

`testAccountErr()`

An attempt to use the ATM with the wrong PIN number results in "ACCOUNT_ERR".

###Task 3

User Stories can be found in /jira/userStories.md


