var gulp = require('gulp'),
    plumber = require('gulp-plumber'),
    autoprefixer = require('gulp-autoprefixer'),
    imagemin = require('gulp-imagemin'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    cssnano = require('gulp-cssnano'),
    rename = require('gulp-rename'),
    sass = require('gulp-sass'),
    changed = require('gulp-changed');

var exec = require('child_process').exec;

var path = {
    dist: 'www/',
    src: 'app/Resources/assets/',
    frontEnd: 'client/build',
    scheduling: {
        src: 'src/AppBundle/AssistantScheduling/Webapp'
    }
};

gulp.task('stylesProd', function () {
  var dest = path.dist + 'css/';
  gulp.src(path.src + 'scss/**/*.scss')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(sass())
      .pipe(autoprefixer())
      .pipe(cssnano())
      .pipe(gulp.dest(dest))
});

gulp.task('scriptsProd', ['scriptsVendor'], function () {
  var dest = path.dist + 'js/';
  gulp.src(path.src + 'js/**/*.js')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(uglify())
      .pipe(gulp.dest(dest))
});

gulp.task('imagesProd', function () {
  var dest = path.dist + 'images/';
  gulp.src(path.src + 'images/**/*')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(imagemin({
        progressive: false,
        interlaced: false,
        optimizationLevel: 1
      }))
      .pipe(gulp.dest(dest))
});

gulp.task('stylesDev', function () {
  var dest = path.dist + 'css/';
  gulp.src(path.src + 'scss/**/*.scss')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(sass())
      .pipe(autoprefixer())
      .pipe(gulp.dest(dest))
});

gulp.task('scriptsDev', ['scriptsVendor'], function () {
  var dest = path.dist + 'js/';
  gulp.src(path.src + 'js/**/*.js')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(gulp.dest(dest))
});

gulp.task('scriptsVendor', function () {
  var dest = path.dist + 'js/';
  return gulp.src([
    'node_modules/modernizr/bin/modernizr.js',
    'node_modules/jquery/dist/jquery.min.js',
    'node_modules/foundation-sites/js/foundation.min.js'])
      .pipe(concat('vendor.js'))
      .pipe(uglify())
      .pipe(gulp.dest(dest));
});

gulp.task('imagesDev', function () {
  var dest = path.dist + 'images/';
  gulp.src(path.src + 'images/**/*')
      .pipe(plumber())
      .pipe(changed(dest))
      .pipe(gulp.dest(dest))
});

gulp.task('compressImages', function () {
  var dest = 'www/images/';
  gulp.src('www/images/**/*')
      .pipe(plumber())
      .pipe(imagemin({
        progressive: false,
        interlaced: false,
        optimizationLevel: 1
      }))
      .pipe(gulp.dest(dest))
});

gulp.task('icons', function () {
  return gulp.src('node_modules/font-awesome/fonts/**.*')
      .pipe(gulp.dest('www/fonts/'));
});

gulp.task('files', function () {
  gulp.src(path.src + 'files/*')
      .pipe(changed('www/files/'))
      .pipe(gulp.dest('www/files/'))
});

gulp.task('vendor', function () {
  gulp.src('node_modules/slick-carousel/slick/**/*')
      .pipe(gulp.dest('www/vendor/slick/'));

  gulp.src('node_modules/dropzone/**/*')
      .pipe(gulp.dest('www/vendor/dropzone/'));

  gulp.src(['node_modules/ckeditor/**/*', path.src + 'js/ckeditor/**/*'])
      .pipe(gulp.dest('www/vendor/ckeditor/'));

  gulp.src('node_modules/material-design-lite/material.min.js')
      .pipe(rename('material.js'))
      .pipe(gulp.dest('www/js'));

  gulp.src('node_modules/getmdl-select/getmdl-select.min.js')
      .pipe(gulp.dest('www/js'));

  gulp.src('node_modules/getmdl-select/getmdl-select.min.css')
      .pipe(gulp.dest('www/css'));

  gulp.src('node_modules/flexboxgrid/dist/flexboxgrid.min.css')
      .pipe(gulp.dest('www/css'));

  gulp.src('node_modules/material-components-web/dist/material-components-web.min.css')
      .pipe(gulp.dest('www/css'));

  gulp.src('node_modules/material-components-web/dist/material-components-web.min.js')
      .pipe(gulp.dest('www/js'));
});

gulp.task('buildAssistantSchedulingApp', function (cb) {
    exec('cd '+path.scheduling.src+' && npm run build:dev', function (err, stdout, stderr) {
        console.log(stdout);
        console.log(stderr);
        cb(err);
    })
});

gulp.task('assistantSchedulingStaticFiles', ['buildAssistantSchedulingApp'], function () {
    gulp.src(path.scheduling.src + '/dist/build.js')
        .pipe(gulp.dest('www/js/scheduling'));
    gulp.src(path.scheduling.src + '/dist/build.js.map')
        .pipe(gulp.dest('www/js/scheduling'));
});

gulp.task('frontEnd', function () {
  gulp.src(path.frontEnd + '/**/*')
      .pipe(gulp.dest('www/'));
});


gulp.task('watch', function () {
    gulp.watch(path.src + 'scss/**/*.scss', ['stylesDev']);
    gulp.watch(path.src + 'js/**/*.js', ['scriptsDev']);
    gulp.watch(path.scheduling.src + '/**/*.vue', ['assistantSchedulingStaticFiles']);
    gulp.watch(path.src + 'images/*', ['imagesDev']);
});

gulp.task('watch:scheduling', function () {
    gulp.watch(path.scheduling.src + '/**/*.vue', ['assistantSchedulingStaticFiles']);
    gulp.watch(path.scheduling.src + '/src/**/*.js', ['assistantSchedulingStaticFiles']);
});


gulp.task('build:prod', ['stylesProd', 'scriptsProd', 'imagesProd', 'files', 'icons', 'vendor']);
gulp.task('build:dev', ['stylesDev', 'scriptsDev', 'imagesDev', 'files', 'icons', 'vendor']);
gulp.task('default', ['build:dev', 'watch']);
