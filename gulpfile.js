var gulp = require('gulp'),
    plumber = require('gulp-plumber'),
    autoprefixer = require('gulp-autoprefixer'),
    imagemin = require('gulp-imagemin'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    cssnano = require('gulp-cssnano'),
    sass = require('gulp-sass'),
    changed = require('gulp-changed'),
    babel = require('gulp-babel');

var exec = require('child_process').exec;

var path = {
    dist: 'web/',
    src: 'app/Resources/assets/',
    client: {
        src: 'client'
    },
    scheduling: {
        src: 'src/AppBundle/AssistantScheduling/Webapp'
    }
};

function stylesProd() {
  var dest = path.dist + 'css/';
  return gulp.src(path.src + 'scss/**/*.scss')
    .pipe(plumber())
    .pipe(changed(dest))
    .pipe(sass())
    .pipe(autoprefixer())
    .pipe(cssnano())
    .pipe(gulp.dest(dest))
}

function scriptsProd () {
  var dest = path.dist + 'js/';
  return gulp.src(path.src + 'js/**/*.js')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(babel({
        presets: ['env']
      }))
      .pipe(uglify())
      .pipe(gulp.dest(dest))
}

function imagesProd () {
  var dest = path.dist + 'images/';
  return gulp.src(path.src + 'images/**/*')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(imagemin({
        progressive: false,
        interlaced: false,
        optimizationLevel: 1
      }))
      .pipe(gulp.dest(dest))
}

function stylesDev () {
  var dest = path.dist + 'css/';
  return gulp.src(path.src + 'scss/**/*.scss')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(sass())
      .pipe(autoprefixer())
      .pipe(gulp.dest(dest))
}

function scriptsDev () {
  var dest = path.dist + 'js/';
  return gulp.src(path.src + 'js/**/*.js')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(babel({
        presets: ['env']
      }))
      .pipe(gulp.dest(dest))
}

function imagesDev () {
  var dest = path.dist + 'images/';
  return gulp.src(path.src + 'images/**/*')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(gulp.dest(dest))
}

function files () {
  return gulp.src(path.src + 'files/*')
      .pipe(changed('web/files/'))
      .pipe(gulp.dest('web/files/'))
}

function vendor () {

  var r = gulp.src('node_modules/dropzone/**/*')
      .pipe(gulp.dest('web/vendor/dropzone/'));

  r = r && gulp.src('node_modules/cropperjs/dist/*')
    .pipe(gulp.dest('web/vendor/cropperjs/'));

  r = r && gulp.src(['node_modules/ckeditor/**/*', path.src + 'js/ckeditor/**/*'])
      .pipe(gulp.dest('web/vendor/ckeditor/'));

  r = r && gulp.src(path.src + '/js/coreui.js')
    .pipe(gulp.dest('web/vendor/'));

  r = r && gulp.src('node_modules/@coreui/coreui/dist/js/coreui.min.js')
    .pipe(gulp.dest('web/vendor/'));

  r = r && gulp.src('node_modules/bootstrap/dist/js/bootstrap.min.js')
    .pipe(gulp.dest('web/js'));

  r = r && gulp.src('node_modules/jquery/dist/jquery.min.js')
    .pipe(gulp.dest('web/js'));

  return r && gulp.src([
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/popper.js/dist/umd/popper.min.js',
    'node_modules/bootstrap/dist/js/bootstrap.min.js',
    'node_modules/moment/min/moment.min.js'
  ])
    .pipe(concat('vendor.js'))
    .pipe(gulp.dest(path.dist + 'js/'));
}

function assistantSchedulingStaticFiles () {
  var r = gulp.src(path.scheduling.src + '/dist/build.js')
        .pipe(gulp.dest('web/js/scheduling'));
    return r && gulp.src(path.scheduling.src + '/dist/build.js.map')
        .pipe(gulp.dest('web/js/scheduling'));
}

function buildClientApp (cb) {
  exec('cd '+path.client.src+' && npm run build', function (err, stdout, stderr) {
    console.log(stdout);
    console.log(stderr);
    cb(err);
  })
}

function clientStaticFiles () {
  return gulp.src(path.client.src + '/dist/app.js')
    .pipe(gulp.dest('web/js/client'));
}

function clientStaticFilesProd () {
  var r = gulp.src(path.client.src + '/dist/js/**/*.js')
    .pipe(concat('app.js'))
    .pipe(gulp.dest('web/js/client'));
  return r && gulp.src(path.client.src + '/dist/css/**/*.css')
    .pipe(concat('app.css'))
    .pipe(gulp.dest('web/css/client'));
}

gulp.task('frontEnd', function () {
  gulp.src(path.frontEnd + '/**/*')
      .pipe(gulp.dest('web/'));
});

function watch () {
    gulp.watch(path.src + 'scss/**/*.scss', stylesDev);
    gulp.watch(path.src + 'js/**/*.js', scriptsDev);
    gulp.watch(path.scheduling.src + '/**/*.vue', assistantSchedulingStaticFiles);
    gulp.watch(path.scheduling.src + '/src/**/*.js', assistantSchedulingStaticFiles);
    gulp.watch(path.src + 'images/*', imagesDev);
}



gulp.task('build:prod', gulp.parallel([stylesProd, scriptsProd, imagesProd, files,vendor]));
gulp.task('build:dev', gulp.parallel([stylesDev, scriptsDev, imagesDev, files, vendor]));
gulp.task('default', gulp.series(['build:dev', watch]));
gulp.task('build:client', gulp.series([buildClientApp, clientStaticFilesProd]));
gulp.task('build:scheduling', gulp.series([assistantSchedulingStaticFiles]));
