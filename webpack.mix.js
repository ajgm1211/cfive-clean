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

mix.js('resources/assets/js/app.js', 'public/js')
	.js('resources/assets/js/contracts/index.js', 'public/js/contracts')
	.js('resources/assets/js/contracts/edit.js', 'public/js/contracts')
   .sass('resources/assets/sass/app.scss', 'public/css');
