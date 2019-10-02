![alt text](https://github.com/vektorprogrammet/vektorprogrammet/blob/readme/logo_readme.png)

![Build Status](https://travis-ci.org/vektorprogrammet/vektorprogrammet.svg?branch=master)
## Set up development environment
### Requirements
- [PHP](http://php.net/downloads.php) version >= 7.1
- [Node](https://nodejs.org/en/) version >= 8
- [Git](https://git-scm.com/)

#### Required PHP-dependencies
*ext-pdo_sqlite
*ext-gd2
*ext-mbstring
*ext-curl
*ext-xml
*ext-dom

Please find file /etc/php/**VERSION**/cli/php.ini
Uncomment all lines with the required PHP-dependencies

Example:

`;extension=mbstring`       ---> `extension=mbstring`

-----
`;extension=ext-pdo_sqlite` ---> `extension=ext-pdo_sqlite`

`;extension=ext-gd2`         ---> `extension=ext-gd` 

`;extension=ext-mbstring`   ---> `extension=ext-mbstring`

`;extension=ext-curl`       ---> `extension=ext-curl`   

`;extension=ext-xml`        ---> `extension=ext-xml` 

`;extension=ext-dom`        ---> `extension=ext-dom`  

-----

##### Install dependencies (with php version 7.2. (php --version to see which version you have)
`sudo apt-get install php7.2-mbstring`

`sudo apt-get install php7.2-sqlite`

`sudo apt-get install php7.2-gd`

`sudo apt-get install php7.2-curl`

`sudo apt-get install php7.2-xml`




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
##### Windows:
`npm run test:win`


## Database

### Add new entities to the database and reload fixtures
`npm run db:update`

### Reload database
`npm run db:update`
