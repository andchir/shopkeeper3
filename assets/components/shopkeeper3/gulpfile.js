
var gulp = require('gulp'),
    concat = require('gulp-concat'),
    uglify = require('gulp-uglify'),
    pump = require('pump'),
    sourcemaps = require('gulp-sourcemaps'),
    prefixCss = require('gulp-prefix-css'),
    uglifyCss = require('gulp-uglifycss'),
    rename = require("gulp-rename");

gulp.task('css_bs3_prefix', function() {
    return gulp.src('./mgr/css/bootstrap/css/bootstrap.css')
        .pipe(prefixCss('.app-container'))
        .pipe(gulp.dest('./mgr/css/bootstrap-custom/css/'))
        .pipe(uglifyCss({
            "maxLineLen": 80,
            "uglyComments": true
        }))
        .pipe(rename(function (path) {
            path.basename += ".min";
            path.extname = ".css"
        }))
        .pipe(gulp.dest('./mgr/css/bootstrap-custom/css/'));
});

gulp.task('default', ['css_bs3_prefix']);

