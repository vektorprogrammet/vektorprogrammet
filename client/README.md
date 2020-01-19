# client

## Project setup
```
npm install
```

### Compiles and hot-reloads for development
```
npm run serve
```

### Compiles and minifies for production
```
npm run build
```

### Lints and fixes files
```
npm run lint
```

### Updates in production
```
cd client && npm run build -- --mode development
```
The build script is run in developer mode because the production script is not functioning as of yet.
The script wil generate a `/dist` folder whose contents must be moved to the correct locations:
```
client/dist/app.js -> web/js/client
client/dist/media/* -> web/media
client/dist/img/* -> web/img
```
The previously existing folders `/media` and `/img` may be deleted before moving the newly generated content to their appropriate locations.
