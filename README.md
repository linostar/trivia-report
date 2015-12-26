# trivia-report
Web interface for allowing users to report mistakes in Rizon's Trivia IRC bot.

# How to install
1- Clone this repo inside your desired www directory.

2- Copy `conf/config.sample.php` to `conf/config.php`, and modify the latter to your needs.

3- Create the database and user for **trivia-report** as you specified in the config file.

4- Import `conf/create_tables.sql` into your **trivia-report** database.

5- Make sure you have already upgraded the *trivia* database structure (i.e. you should have one `trivia_questions` table instead of multiple `trivia_questions_*`).
