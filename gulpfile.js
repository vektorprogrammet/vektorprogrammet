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

var path = {
    dist: 'www/',
    src: 'app/Resources/assets/'
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
    var dest = path.dist + 'img/';
    gulp.src(path.src + 'img/**/*')
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
    var dest = path.dist + 'img/';
    gulp.src(path.src + 'img/**/*')
        .pipe(plumber())
        .pipe(changed(dest))
        .pipe(gulp.dest(dest))
});

gulp.task('compressImages', function(){
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

gulp.task('icons', function() {
  return gulp.src('node_modules/font-awesome/fonts/**.*')
    .pipe(gulp.dest('www/fonts/'));
});

gulp.task('files', function(){
    gulp.src(path.src + 'files/*')
        .pipe(changed('www/files/'))
        .pipe(gulp.dest('www/files/'))
});

gulp.task('vendor', function(){
    gulp.src('node_modules/slick-carousel/slick/**/*')
        .pipe(gulp.dest('www/vendor/slick/'));

    gulp.src('node_modules/dropzone/**/*')
        .pipe(gulp.dest('www/vendor/dropzone/'));
  
    gulp.src(['node_modules/ckeditor/**/*', path.src + 'js/ckeditor/**/*'])
        .pipe(gulp.dest('www/vendor/ckeditor/'));
});


gulp.task('watch', function () {
    gulp.watch(path.src + 'scss/**/*.scss', ['stylesDev']);
    gulp.watch(path.src + 'js/**/*.js', ['scriptsDev']);
    gulp.watch(path.src + 'images/*', ['imagesDev']);
});


gulp.task('build:prod', ['stylesProd', 'scriptsProd', 'imagesProd', 'files', 'icons', 'vendor']);
gulp.task('build:dev', ['stylesDev', 'scriptsDev', 'imagesDev', 'files', 'icons', 'vendor']);
gulp.task('default', ['build:dev', 'watch']);
