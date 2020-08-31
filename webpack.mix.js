let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js')
  .js('resources/js/contracts/index.js', 'public/js/contracts')
  .js('resources/js/contracts/edit.js', 'public/js/contracts')
  .js('resources/js/inlands/edit.js', 'public/js/inlands')
  .js('resources/js/inlands/index.js', 'public/js/inlands')
  .js('resources/js/transit_time/index.js', 'public/js/transit_time')
  .sass('resources/sass/app.scss', 'public/css');
