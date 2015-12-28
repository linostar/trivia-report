# trivia-report
Web interface for allowing users to report mistakes in Rizon's Trivia IRC bot.

## Requirements
- PHP 5+
- MySQL (or MariaDB) 5.1+
- php-mysqlnd (for mysql prepared statements)

## How to install
1. Clone this repo inside your desired www directory.

2. Copy `conf/config.sample.php` to `conf/config.php`, and modify the latter to your needs.

3. Create the database and user for **trivia-report** as you specified in the config file.

4. Import `conf/create_tables.sql` into your **trivia-report** database.

5. Make sure you have already upgraded the *trivia* database structure (i.e. you should have one `trivia_questions` table instead of multiple `trivia_questions_*`).

## Features
1. User interface:
  * Trivia mistake reporter
2. Admin interface:
  * Report manager:
    * Examine reports and change their states
    * Auto identify the question mentioned in report and allow editing it
    * Allow filtering reports based on states and mistake types
  * Question manager:
    * Examine questions and manage them (add/edit/delete)
    * Allow simple search for questions
  * Category Manager:
    * Examine question categories and manage them
