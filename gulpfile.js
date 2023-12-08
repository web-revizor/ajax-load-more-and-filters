'use strict';

const babel = require('gulp-babel'),
    browserSync = require('browser-sync'),
    cleanCSS = require('gulp-clean-css'),
    gulp = require('gulp'),
    gutil = require('gulp-util'),
    rigger = require('gulp-rigger'),
    rimraf = require('rimraf'),
    sass = require('gulp-sass')(require('node-sass')),
    sourcemaps = require('gulp-sourcemaps'),
    strip = require('gulp-strip-comments'),
    uglify = require('gulp-uglify'),
    prettier = require('gulp-prettier'),
    postcss = require('gulp-postcss'),
    tailwindcss = require('tailwindcss');


function swallowError(error) {
    // If you want details of the error in the console
    console.log(error.toString());
    this.emit('end');
}

const folders = {
    src: 'src/',
    dst: './'
}

const path = {
    config: "./tailwind.config.js",
    build: {
        js: folders.dst + 'dist/js/',
        css: folders.dst + 'dist/css/',
    },
    src: {
        js: folders.src + 'js/*.js',
        css: folders.src + 'styles/app.scss',
    },
    watch: {
        js: folders.src + 'js/**/*.js',
        css: folders.src + 'styles/**/**/*.scss'
    },
    clean: ['js', 'css'],
    node: 'node_modules'
};

gulp.task('js:build', function () {
    return gulp
        .src(path.src.js)
        .pipe(babel({
            presets: [
                "@babel/preset-env"
            ]
        }))
        .on('error', swallowError)
        .pipe(rigger())
        .pipe(strip())
        .pipe(sourcemaps.init())
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest(path.build.js))
        .pipe(browserSync.reload({
            stream: true
        }));
});

gulp.task('js:prettier', function () {
    return gulp
        .src('./src/js/**/*.*')
        .pipe(prettier({singleQuote: true}))
        .pipe(gulp.dest('./src/js'));
});


gulp.task("styles:build", () => {
    return gulp.src(path.src.css)
        .pipe(sass({
            includePaths: [path.src.css],
            outputStyle: 'compressed',
            sourceMap: true,
            errLogToConsole: true
        }))
        .pipe(postcss([tailwindcss(path.config), require("autoprefixer")]))
        .pipe(cleanCSS({level: {0: {specialComments: 0}}}))
        .pipe(sourcemaps.write('/'))
        .pipe(gulp.dest(path.build.css))
        .pipe(browserSync.reload({
            stream: true
        }));
});

gulp.task('styles:prettier', function () {
    return gulp
        .src('./src/styles/**/*.*')
        .pipe(prettier({singleQuote: true}))
        .pipe(gulp.dest('./src/styles'));
});


gulp.task('clean', function (cb) {
    for (var i = 0; i < path.clean.length; i++) {
        rimraf(path.clean[i], cb);
    }
});

gulp.task('default', gulp.parallel(
    'js:build',
    'styles:build'
));

gulp.task('prettier', gulp.parallel('styles:prettier', 'js:prettier'));