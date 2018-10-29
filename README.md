test


![Build Status](https://travis-ci.org/vektorprogrammet/vektorprogrammet.svg?branch=master)
## Set up development environment
### Requirements
- [PHP](http://php.net/downloads.php) version >= 7.1
- [Node](https://nodejs.org/en/) version >= 8

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

