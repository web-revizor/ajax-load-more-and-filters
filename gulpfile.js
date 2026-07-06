'use strict';

const babel = require('gulp-babel'),
  browserSync = require('browser-sync'),
  gulp = require('gulp'),
  gutil = require('gulp-util'),
  rigger = require('gulp-rigger'),
  rimraf = require('rimraf'),
  strip = require('gulp-strip-comments'),
  uglify = require('gulp-uglify'),
  prettier = require('gulp-prettier');

function swallowError(error) {
  // If you want details of the error in the console
  console.log(error.toString());
  this.emit('end');
}

const folders = {
  src: 'src/',
  dst: './',
};

const path = {
  build: {
    js: folders.dst + 'dist/js/',
  },
  src: {
    js: folders.src + 'js/*.js',
  },
  watch: {
    js: folders.src + 'js/**/*.js',
  },
  clean: ['dist/js'],
};

// Builds the public-facing load-more/filter script (dist/js/load_more_and_filter.js).
// No sourcemaps here on purpose: this is what actually ships inside the
// plugin zip, and a shipped .js with no matching .map next to it just
// 404s in the browser console. The admin UI is a separate Vite+React
// project — see frontend/ (which also ships without sourcemaps, by Vite's
// own default, so this keeps both bundles consistent).
gulp.task('js:build', function () {
  return gulp
    .src(path.src.js)
    .pipe(
      babel({
        presets: ['@babel/preset-env'],
      })
    )
    .on('error', swallowError)
    .pipe(rigger())
    .pipe(strip())
    .pipe(uglify())
    .on('error', gutil.log)
    .pipe(gulp.dest(path.build.js))
    .pipe(
      browserSync.reload({
        stream: true,
      })
    );
});

gulp.task('js:prettier', function () {
  return gulp
    .src('./src/js/**/*.*')
    .pipe(prettier({ singleQuote: true }))
    .pipe(gulp.dest('./src/js'));
});

gulp.task('clean', function (cb) {
  for (let i = 0; i < path.clean.length; i++) {
    rimraf(path.clean[i], cb);
  }
});

gulp.task('default', gulp.parallel('js:build'));

gulp.task('prettier', gulp.parallel('js:prettier'));

gulp.task('watch', function () {
  gulp.watch(path.watch.js, gulp.series('js:build'));
});
