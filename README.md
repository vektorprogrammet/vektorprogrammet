<img src="https://github.com/vektorprogrammet/vektorprogrammet/blob/master/app/Resources/assets/images/vektor_stor.png" alt="alt text" width="400" height="auto">

![Build Status](https://travis-ci.org/vektorprogrammet/vektorprogrammet.svg?branch=master)



# Set up development environment
## Requirements:
- [PHP](http://php.net/downloads.php) version >= 7.1
- [Node](https://nodejs.org/en/) version >= 8
- [Git](https://git-scm.com/)

##### Required PHP-dependencies:
* ext-pdo_sqlite
* ext-gd2
* ext-mbstring
* ext-curl
* ext-xml

#### How to install the php-dependencies

Please find the file `php.ini`. (On Linux it is located at `/etc/php/version/cli/php.ini`).

Uncomment all lines with the required PHP-dependencies.

Example for dependency `mbstring`:

`;extension=mbstring`       ---> `extension=mbstring`


To install the php-dependencies on Ubuntu 
(Example with Ubuntu as operating system and a php-version of 7.2)
```
sudo apt-get install php7.2-mbstring
sudo apt-get install php7.2-sqlite
sudo apt-get install php7.2-gd
sudo apt-get install php7.2-curl
sudo apt-get install php7.2-xml
```




## Setup:

### Clone files:
`git clone https://github.com/vektorprogrammet/vektorprogrammet.git`

#### UNIX:
`npm run setup`
#### Windows:
`npm run setup:win`

### Start server on http://localhost:8000
`npm start`


### Build static files
When adding new images or other non-code files, you can run

`npm run build`

so that the files are put in the correct places. (this is automatically
done when doing `npm start`)

## Users
| Position     | Username   | Password |        Role        |
| :----------: | :--------: |:--------:|:------------------:|
| Assistent    | assistent  |   1234   |      ROLE_USER     |
| Teammedlem   | teammember |   1234   |  ROLE_TEAM_MEMBER  |
| Teamleder    | teamleader |   1234   |  ROLE_TEAM_LEADER  |
| Admin        | admin      |   1234   |      ROLE_ADMIN    |


## Code style
Code style should follow a certain set of rules. Make sure your code 
adheres to these rules before opening a PR. 

### Fix style
##### UNIX/LINUX:
`npm run -s cs`
##### Windows:
`npm run -s cs:win`

## Testing
Tests should be run before opening a PR.
##### UNIX/LINUX:
`npm run test`

File specific test: 

`npm run test "tests/PATH_TO_TEST" `

##### Windows:
`npm run test:win`

#### File specific test: 

`npm run test "tests/PATH_TO_TEST" `



## Database

### Add new entities to the database and reload fixtures
`npm run db:update`

### Reload database
`npm run db:reload`
