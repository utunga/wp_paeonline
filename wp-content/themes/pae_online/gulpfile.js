//process.env.DISABLE_NOTIFIER = true; // Uncomment to disable all Gulp notifications.

/**		
 * Paekakariki Online.		
 *		
 * This file adds gulp tasks to the Paekakariki Online theme.		
 *		
 * @author SEO themes		
 */

// Require our dependencies.
var args         = require('yargs').argv,
	autoprefixer = require('autoprefixer'),
	browsersync  = require('browser-sync'),
	bump         = require('gulp-bump'),
	changecase   = require('change-case'),
	del          = require('del'),
	mqpacker     = require('css-mqpacker'),
	fs           = require('fs'),
	gulp         = require('gulp'),
	beautify     = require('gulp-cssbeautify'),
	cache        = require('gulp-cached'),
	cleancss     = require('gulp-clean-css'),
	concat       = require('gulp-concat'),
	csscomb      = require('gulp-csscomb'),
	cssnano      = require('gulp-cssnano'),
	filter       = require('gulp-filter'),
	imagemin     = require('gulp-imagemin'),
	notify       = require('gulp-notify'),
	pixrem       = require('gulp-pixrem'),
	plumber      = require('gulp-plumber'),
	postcss      = require('gulp-postcss'),
	rename       = require('gulp-rename'),
	replace      = require('gulp-replace'),
	s3           = require('gulp-s3-publish'),
	sass         = require('gulp-sass'),
	sort         = require('gulp-sort'),
	sourcemaps   = require('gulp-sourcemaps'),
	uglify       = require('gulp-uglify'),
	wpPot        = require('gulp-wp-pot'),
	zip          = require('gulp-zip'),
	focus        = require('postcss-focus');

// Set assets paths.
var paths = {
	all:     ['./**/*', '!./node_modules/', '!./node_modules/**', '!./screenshot.png', '!./assets/images/**'],
	concat:  ['assets/scripts/concat/*.js'],
	images:  ['assets/images/*', '!assets/images/*.svg'],
	php:     ['./*.php', './**/*.php', './**/**/*.php'],
	scripts: ['assets/scripts/scripts.js', 'assets/scripts/customize.js'],
	styles:  ['assets/styles/*.scss', '!assets/styles/min/']
};

function styles() {
  return gulp
		.src('assets/styles/style.scss')

		// Notify on error
		.pipe(plumber({
			errorHandler: notify.onError("Error: <%= error.message %>")
		}))

		// Initialize source map.
		.pipe(sourcemaps.init())

		// Process sass
		.pipe(sass({
			outputStyle: 'expanded'
		}))

		// Pixel fallbacks for rem units.
		.pipe(pixrem())

		// Parse with PostCSS plugins.
		.pipe(postcss([
			autoprefixer({
				browsers: 'last 2 versions'
			}),
			mqpacker({
				sort: true
			}),
			focus(),
		]))

		// Format non-minified stylesheet.
		.pipe(csscomb())

		// Output non minified css to theme directory.
		.pipe(gulp.dest('./'))

		// Inject changes via browsersync.
		.pipe(browsersync.reload({
			stream: true
		}))

		// Process sass again.
		.pipe(sass({
			outputStyle: 'compressed'
		}))

		// Combine similar rules.
		.pipe(cleancss({
			level: {
				2: {
					all: true
				}
			}
		}))

		// Minify and optimize style.css again.
		.pipe(cssnano({
			safe: false,
			discardComments: {
				removeAll: true,
			},
		}))

		// Add .min suffix.
		.pipe(rename({
			suffix: '.min'
		}))

		// Write source map.
		.pipe(sourcemaps.write('./', {
			includeContent: false,
		}))

		// Output the compiled sass to this directory.
		.pipe(gulp.dest('assets/styles/min'))

		// Filtering stream to only css files.
		.pipe(filter('**/*.css'))

		// Notify on successful compile (uncomment for notifications).
		.pipe(notify("Compiled: <%= file.relative %>"));
}

function scripts() {
  return (
    gulp
      .src(paths.scripts)
      .pipe(plumber())
      .pipe(sourcemaps.init())
      .pipe(cache('scripts'))
			.pipe(rename({
				suffix: '.min'
			}))
			.pipe(uglify())
			.pipe(sourcemaps.write('./', {
				includeContent: false,
			}))
      .pipe(gulp.dest('assets/scripts/min'))
			.pipe(browsersync.reload({
				stream: true
			}))
			.pipe(notify("Minified: <%= file.relative %>"))
  );
}

function images() {
  return gulp
		.src(paths.images)
		.pipe(plumber({
			errorHandler: notify.onError("Error: <%= error.message %>")
		}))

		// Cache files to avoid processing files that haven't changed.
		.pipe(cache('images'))

		// Optimize images.
		.pipe(imagemin({
			progressive: true
		}))

		// Output the optimized images to this directory.
		.pipe(gulp.dest('assets/images'))

		// Inject changes via browsersync.
		.pipe(browsersync.reload({
			stream: true
		}))

		// Notify on successful compile.
		.pipe(notify("Optimized: <%= file.relative %>"));
}

gulp.task('default', function (done) {
	scripts();
	styles();
	images();
	done();
});


