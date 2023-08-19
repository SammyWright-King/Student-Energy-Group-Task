
## About The Task

- I have used Jquery(ajax) to send most of the form data back to the backend
- I created a meter_type table to hold the different types of energy category i.e electric/gas. 
- I also created a seeder class in order to input the default categories which are mpan and mprn with their description as these are already known and constant
- I included a job process to run in the background to read the csv file and execute the insertion of valid rows to the meter reading table
- To Ensure the SOLID principle is in place, the code is broken down into components and injected where needed using Dependency Injection and Inheritance and others.

## Commands You should run
- "php artisan migrate" to set up the tables needed for this project in your database
- "php artisan db:seed" to seed the meter type table
- "php artisan queue:work" to start the worker to run the queued job


