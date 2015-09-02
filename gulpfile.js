'use strict';

var gulp = require('gulp');
var bump = require('gulp-bump');
var concat = require('gulp-concat');
var filter = require('gulp-filter');
var inject = require('gulp-inject');
var sass = require('gulp-sass');
var minifyCSS = require('gulp-minify-css');
var minifyHTML = require('gulp-minify-html');
var plumber = require('gulp-plumber');
var sourcemaps = require('gulp-sourcemaps');
var template = require('gulp-template');
var tsc = require('gulp-typescript');
var uglify = require('gulp-uglify');
var watch = require('gulp-watch');

var Builder = require('systemjs-builder');
var del = require('del');
var fs = require('fs');
var join = require('path').join;
var runSequence = require('run-sequence');
var semver = require('semver');
var series = require('stream-series');

var http = require('http');
var connect = require('connect');
var serveStatic = require('serve-static');
var openResource = require('open');

// --------------
// Configuration.
var APP_BASE = '/';

var PATH = {
  dest: {
    all: 'front/public',
    dev: {
      all: 'front/public',
      lib: 'front/public/lib',
      ng2: 'front/public/lib/angular2.js',
      router: 'front/public/lib/router.js'
    },
    prod: {
      all: 'front/public',
      lib: 'front/public/lib'
    }
  },
  src: {
    // Order is quite important here for the HTML tag injection.
    lib: [
      './node_modules/angular2/node_modules/traceur/bin/traceur-runtime.js',
      './node_modules/es6-module-loader/dist/es6-module-loader-sans-promises.js',
      './node_modules/es6-module-loader/dist/es6-module-loader-sans-promises.js.map',
      './node_modules/reflect-metadata/Reflect.js',
      './node_modules/reflect-metadata/Reflect.js.map',
      './node_modules/systemjs/dist/system.src.js',
      './node_modules/angular2/node_modules/zone.js/dist/zone.js',
      './front/lib/http.js'
    ],
    plugins: './mod'
  }
};

var ng2Builder = new Builder({
  defaultJSExtensions: true,
  paths: {
    'angular2/*': 'node_modules/angular2/es6/dev/*.js',
    rx: 'node_modules/angular2/node_modules/rx/dist/rx.js'
  },
  meta: {
    rx: {
      format: 'cjs'
    },
    'angular2/src/router/route_definition': {
      format: 'es6'
    }
  }
});

var appProdBuilder = new Builder({
  baseURL: 'file:./tmp',
  meta: {
    'angular2/angular2': { build: false },
    'angular2/router': { build: false }
  }
});

var HTMLMinifierOpts = { conditionals: true };

var tsProject = tsc.createProject('tsconfig.json', {
  typescript: require('typescript')
});

var semverReleases = ['major', 'premajor', 'minor', 'preminor', 'patch',
                      'prepatch', 'prerelease'];

var port = 5555;

// --------------
// Clean.

gulp.task('clean', function (done) {
  del(PATH.dest.all, done);
});

gulp.task('clean.dev', function (done) {
  del(PATH.dest.dev.all, done);
});

gulp.task('clean.app.dev', function (done) {
  // TODO: rework this part.
  del([join(PATH.dest.dev.all, '**/*'), '!' +
       PATH.dest.dev.lib, '!' + join(PATH.dest.dev.lib, '*')], done);
});

gulp.task('clean.prod', function (done) {
  del(PATH.dest.prod.all, done);
});

gulp.task('clean.app.prod', function (done) {
  // TODO: rework this part.
  del([join(PATH.dest.prod.all, '**/*'), '!' +
       PATH.dest.prod.lib, '!' + join(PATH.dest.prod.lib, '*')], done);
});

gulp.task('clean.tmp', function(done) {
  del('tmp', done);
});

// -------------
// Build plugins.
gulp.task('build.plugins', function (cb) {
//  var result = gulp.src('./front/app/**/*scss');
  var plugins = fs.readdirSync(PATH.src.plugins);
  plugins.map(function(plugin, i){
    var path = PATH.src.plugins + '/' + plugin;
    try {
      var info = require(path + '/plugin.json');

      // ----------
      // Build plugins to source
      gulp.src(path + '/app/**/*ts')
        .pipe(gulp.dest('./front/app/src/plugins/' + plugin));

      gulp.src(path + '/app/templates/**/*html')
        .pipe(gulp.dest('./front/app/templates/plugins/' + plugin));

      gulp.src(path + '/app/stylesheets/**/*scss')
        .pipe(gulp.dest('./front/app/stylesheets/plugins/' + plugin));

    } catch (error) {
      if(error.code != 'MODULE_NOT_FOUND')
        console.log(error);
    }

    if(i == plugins.length -1)
      cb();

  });

});


// ----------
// Builds scss for plugins
gulp.task('build.plugins.scss', function () {
    gulp.src('./front/app/stylesheets/plugins/**/*scss')
      .pipe(concat('plugins.scss'))
      .pipe(gulp.dest('./front/app/stylesheets/'));
});

// --------------
// Build dev.

gulp.task('build.ng2.dev', function () {
  ng2Builder.build('angular2/router', PATH.dest.dev.router, {});
  return ng2Builder.build('angular2/angular2', PATH.dest.dev.ng2, {});
});

gulp.task('build.lib.dev', ['build.ng2.dev'], function () {
  return gulp.src(PATH.src.lib)
    .pipe(gulp.dest(PATH.dest.dev.lib));
});

/**
 * Build CSS from SCSS
 */
gulp.task('build.scss', ['build.plugins.scss'], function () {
	  var result = gulp.src('./front/app/**/*scss')
	    .pipe(sass().on('error', sass.logError))
	    .pipe(gulp.dest(PATH.dest.dev.all));

	  return result;
});

/**
 * Convert Typscript to ES5 (Dev)
 */
gulp.task('build.js.dev', function () {
  var result = gulp.src('./front/app/**/*ts')
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(tsc(tsProject));

  return result.js
    .pipe(sourcemaps.write())
    .pipe(template(templateLocals()))
    .pipe(gulp.dest(PATH.dest.dev.all));
});

/**
 * Build assets (Dev)
 */
gulp.task('build.assets.dev', ['build.scss', 'build.js.dev'], function () {
  return gulp.src(['./front/app/**/*.html', './front/app/**/*.css', './front/app/**/*.png', './front/app/**/*.jpg'])
    .pipe(gulp.dest(PATH.dest.dev.all));
});

/**
 * Compile index page (Dev)
 */
gulp.task('build.index.dev', function() {
  var target = gulp.src(injectableDevAssetsRef(), { read: false });
  return gulp.src('./front/app/index.php')
    .pipe(inject(target, { transform: transformPath('dev') }))
    .pipe(template(templateLocals()))
    .pipe(gulp.dest(PATH.dest.dev.all));
});

gulp.task('build.app.dev', function (done) {
  runSequence('clean.app.dev', 'build.plugins', 'build.assets.dev', 'build.index.dev', done);
});

gulp.task('build.dev', function (done) {
  runSequence('clean.dev', 'build.lib.dev', 'build.app.dev', done);
});

// --------------
// Build prod.

gulp.task('build.ng2.prod', function () {
  ng2Builder.build('angular2/router', join('tmp', 'router.js'), {});
  return ng2Builder.build('angular2/angular2', join('tmp', 'angular2.js'), {});
});

gulp.task('build.lib.prod', ['build.ng2.prod'], function () {
  var jsOnly = filter('**/*.js');
  var lib = gulp.src(PATH.src.lib);
  var ng2 = gulp.src('tmp/angular2.js');
  var router = gulp.src('tmp/router.js');

  return series(lib, ng2, router)
    .pipe(jsOnly)
    .pipe(concat('lib.js'))
    .pipe(uglify())
    .pipe(gulp.dest(PATH.dest.prod.lib));
});

gulp.task('build.js.tmp', function () {
  var result = gulp.src(['./front/app/**/*ts', '!./front/app/init.ts'])
    .pipe(plumber())
    .pipe(tsc(tsProject));

  return result.js
    .pipe(template({ VERSION: getVersion() }))
    .pipe(gulp.dest('tmp'));
});

// TODO: add inline source maps (System only generate separate source maps file).
gulp.task('build.js.prod', ['build.js.tmp'], function() {
  return appProdBuilder.build('app', join(PATH.dest.prod.all, 'app.js'),
    { minify: true }).catch(function (e) { console.log(e); });
});

gulp.task('build.init.prod', function() {
  var result = gulp.src('./front/app/init.ts')
    .pipe(plumber())
    .pipe(sourcemaps.init())
    .pipe(tsc(tsProject));

  return result.js
    .pipe(uglify())
    .pipe(template(templateLocals()))
    .pipe(sourcemaps.write())
    .pipe(gulp.dest(PATH.dest.prod.all));
});

gulp.task('build.assets.prod', ['build.js.prod'], function () {
  var filterHTML = filter('**/*.html');
  var filterCSS = filter('**/*.css');
  return gulp.src(['./front/app/**/*.html', './front/app/**/*.css'])
    .pipe(filterHTML)
    .pipe(minifyHTML(HTMLMinifierOpts))
    .pipe(filterHTML.restore())
    .pipe(filterCSS)
    .pipe(minifyCSS())
    .pipe(filterCSS.restore())
    .pipe(gulp.dest(PATH.dest.prod.all));
});

gulp.task('build.index.prod', function() {
  var target = gulp.src([join(PATH.dest.prod.lib, 'lib.js'),
                         join(PATH.dest.prod.all, '**/*.css')], { read: false });
  return gulp.src('./front/app/index.html')
    .pipe(inject(target, { transform: transformPath('prod') }))
    .pipe(template(templateLocals()))
    .pipe(gulp.dest(PATH.dest.prod.all));
});

gulp.task('build.app.prod', function (done) {
  // build.init.prod does not work as sub tasks dependencies so placed it here.
  runSequence('clean.app.prod', 'build.init.prod', 'build.assets.prod',
              'build.index.prod', 'clean.tmp', done);
});

gulp.task('build.prod', function (done) {
  runSequence('clean.prod', 'build.lib.prod', 'clean.tmp', 'build.app.prod',
              done);
});

// --------------
// Version.

registerBumpTasks();

gulp.task('bump.reset', function() {
  return gulp.src('package.json')
    .pipe(bump({ version: '0.0.0' }))
    .pipe(gulp.dest('./'));
});

// --------------
// Utils.

function transformPath(env) {
  var v = '?v=' + getVersion();
  return function (filepath) {
    arguments[0] = filepath.replace('/' + PATH.dest[env].all, '') + v;
    return inject.transform.apply(inject.transform, arguments);
  };
}

function injectableDevAssetsRef() {
  var src = PATH.src.lib.map(function(path) {
    return join(PATH.dest.dev.lib, path.split('/').pop());
  });
  src.push(PATH.dest.dev.ng2, PATH.dest.dev.router,
           join(PATH.dest.dev.all, '**/*.css'));
  return src;
}

function getVersion(){
  var pkg = JSON.parse(fs.readFileSync('package.json'));
  return pkg.version;
}

function templateLocals() {
  return {
    VERSION: getVersion(),
    APP_BASE: APP_BASE
  };
}

function registerBumpTasks() {
  semverReleases.forEach(function (release) {
    var semverTaskName = 'semver.' + release;
    var bumpTaskName = 'bump.' + release;
    gulp.task(semverTaskName, function() {
      var version = semver.inc(getVersion(), release);
      return gulp.src('package.json')
        .pipe(bump({ version: version }))
        .pipe(gulp.dest('./'));
    });
    gulp.task(bumpTaskName, function(done) {
        runSequence(semverTaskName, 'build.app.prod', done);
    });
  });
}
