![Build Status](https://travis-ci.org/vektorprogrammet/vektorprogrammet.svg?branch=master)
## Set up development environment
### Requirements
- [PHP](http://php.net/downloads.php) version >= 7.0
- [Node](https://nodejs.org/en/) version >= 4

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
| Username   | Password |        Role        |
| ---------- |:--------:|:------------------:|
| assistent  |   1234   |      ROLE_USER     |
| team       |   1234   |     ROLE_ADMIN     |
| admin      |   1234   |  ROLE_SUPER_ADMIN  |
| superadmin |   1234   | ROLE_HIGHEST_ADMIN |


## Code style
Code style should follow a certain set of rules. Make sure your code 
adheres to these rules before opening a PR. 
### Check style
##### UNIX/LINUX:
`npm run -s code-style`
##### Windows:
`npm run -s code-style:win`

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
### Reload database
`npm run db:reload`

### Add new entities to the database
`npm run db:update`

