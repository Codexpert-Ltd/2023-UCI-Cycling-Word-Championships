# 2023-UCI-Cycling-Word-Championships


# Quick setup


After cloning the repository in a folder in the htdocs section of the xampp software (in the example, the uci_software folder)

In the first step, import the `database/uci_api.sql` file through phpmyadmin

After this step, a database with the name `uci_api` and table `uci_data` is created, which has a line of test data on it.

After this, create the `.env` file similar to the `.env.example` file and enter the database information and username and password in it.

Then enter the htdocs folder and then the uci_software folder through the shell option in the xampp control panel.

Run the `composer install command`
And then run the command `php -S localhost:3000 -t api`

Now the API is ready to use

## API usage 
|   Method             |API                          |BODY                         |Response|
|----------------|-------------------------------|-----------------------------|-----------------------------|
|GET             |`http://localhost:3000/api`            |NONE            |list of all data|
|GET             |`http://localhost:3000/api/id`            |NONE           |List data by id|
|POST            |`http://localhost:3000/api`|`{"Corridor":"sample", "time":"sample", "data":"sample"}`|`{"message": "Corridor Post Created"}`|
|DELETE          |`http://localhost:3000/api/id`|NONE|`{"message": "Corridor Post Deleted!"}`
|PUT             |`http://localhost:3000/api/id`|`{"Corridor":"sample", "time":"sample", "data":"sample"}`|`{"message": "Corridor Post Updated!"}`|
