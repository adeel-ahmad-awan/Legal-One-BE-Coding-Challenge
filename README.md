# Legal-One-BE-Coding-Challenge

## Project setup
crete 2 files in the home directory 
- `.env.local`
- `.env.test`

All the database credentials in those files according to your system

## How to run project

### Command
Run the command `app:fetch-logs` to fetch all the logs

- This command expect the file path in its argument
- The command with fhe file name will be 
    -  `php bin/console app:fetch-logs /home/ad/dev/php/be-coding-challenge-main/logs.txt`

- The file given in the command expect the logs in the following format
    - `SERVICE-NAME - - [date and time] "HTTP-Verb Endpoint HTTP-protocol" status-code`

- The type of each log file is as following
    - `SERVICE-NAME`: String
    - `date and time`: String in valid date or dateTime format
    - `HTTP-Verb`: String
    - `Endpoint`: String
    - `HTTP-protocol`: String
    - `status-code`: Number

example
`USER-SERVICE - - [18/Aug/2021:10:32:56 +0000] "POST /users HTTP/1.1" 201`

### TestCase

For testing you will need to create the test database and test schema by the following commands

- `php bin/console --env=test doctrine:database:create`
- `php bin/console --env=test doctrine:schema:create`

After creation of test database run the following command to run all the test cases
- `php bin/phpunit`

### Server

Run the server by the following command
- `symfony server:start`

You can browse to the following URL to see the count api in action
- `http://127.0.0.1:8000/count`

The count api expect 4 parameter which are all optional with the following types
- `serviceNames`: String
- `startDate`: String in valid date or dateTime format
- `endDate`: String in valid date or dateTime format
- `statusCode`: Number


The api will return a json response with count of logs according to the parameter but if the parameter are in invalid format the api will return `400` error

