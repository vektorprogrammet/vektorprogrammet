var gulp = require('gulp'),
    plumber = require('gulp-plumber'),
    autoprefixer = require('gulp-autoprefixer'),
    imagemin = require('gulp-imagemin'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    cssnano = require('gulp-cssnano'),
    htmlmin = require('gulp-htmlmin'),
    rename = require('gulp-rename'),
    sass = require('gulp-sass'),
    changed = require('gulp-changed');

var path = {
    dist: 'www/bundles/app/',
    src: 'src/AppBundle/Resources/Public/'
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

gulp.task('scriptsProd', function () {
    var dest = path.dist + 'js/';
    gulp.src(path.src + 'js/**/*.js')
        .pipe(plumber())
        .pipe(changed(dest))
        .pipe(gulp.dest(path.dist + 'js/'))
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

gulp.task('scriptsDev', function () {
    var dest = path.dist + 'js/';
    gulp.src(path.src + 'js/**/*.js')
        .pipe(plumber())
        .pipe(changed(dest))
        .pipe(gulp.dest(path.dist + 'js/'))
        .pipe(gulp.dest(dest))
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

gulp.task('files', function(){
    gulp.src(path.src + 'files/*')
        .pipe(gulp.dest('www/files/'))
});


gulp.task('watch', function () {
    gulp.watch(path.src + 'scss/**/*.scss', ['stylesDev']);
    gulp.watch(path.src + 'js/**/*.js', ['scriptsDev']);
    gulp.watch(path.src + 'images/*', ['imagesDev']);
});


gulp.task('build:prod', ['stylesProd', 'scriptsProd', 'imagesProd', 'files']);
gulp.task('build:dev', ['stylesDev', 'scriptsDev', 'imagesDev', 'files']);
gulp.task('default', ['build:dev', 'watch']);

