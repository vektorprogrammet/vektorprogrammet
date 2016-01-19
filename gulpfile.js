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
    changed = require('gulp-changed')

var path = {
    dist: 'www/bundles/app/',
    src: 'src/AppBundle/Resources/Public/'
};

gulp.task('styles', function () {
    var dest = path.dist + 'css/';
    gulp.src(path.src + 'scss/**/*.scss')
        .pipe(plumber())
        .pipe(changed(dest))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(gulp.dest(path.dist + 'css/'))
        .pipe(cssnano())
        .pipe(gulp.dest(dest))
});

gulp.task('scripts', function () {
    var dest = path.dist + 'js/';
    gulp.src(path.src + 'js/**/*.js')
        .pipe(plumber())
        .pipe(changed(dest))
        .pipe(gulp.dest(path.dist + 'js/'))
        .pipe(uglify())
        .pipe(gulp.dest(dest))
});

gulp.task('images', function () {
    var dest = path.dist + 'img/';
    gulp.src(path.src + 'img/**/*')
        .pipe(plumber())
        .pipe(changed(dest))
        .pipe(imagemin({
            progressive: true,
            interlaced: true,
            optimizationLevel: 3
        }))
        .pipe(gulp.dest(dest))
});

gulp.task('compressImages', function(){
    var dest = 'www/images/';
    gulp.src('www/images/**/*')
        .pipe(plumber())
        .pipe(imagemin({
            progressive: true,
            interlaced: true,
            optimizationLevel: 3
        }))
        .pipe(gulp.dest(dest))
});

gulp.task('build:prod', ['styles', 'scripts', 'images'])

gulp.task('watch', function () {
    gulp.watch(path.src + 'styles/**/*.scss', ['styles']);
    gulp.watch(path.src + 'scripts/**/*.js', ['scripts']);
    gulp.watch(path.src + 'images/*', ['images']);
});


gulp.task('default', ['build:public', 'watch']);
