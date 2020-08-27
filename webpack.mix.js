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
    .js('resources/assets/js/inlands/edit.js', 'public/js/inlands')
    .js('resources/assets/js/inlands/index.js', 'public/js/inlands')
    //.js('resources/assets/js/quote/edit.js', 'public/js/quote')
    .js('resources/assets/js/quote/index.js', 'public/js/quote')
    //.js('resources/assets/js/quoteLCL/edit.js', 'public/js/quoteLCL')
    .js('resources/assets/js/quoteLCL/index.js', 'public/js/quoteLCL')
    .js('resources/assets/js/transit_time/index.js', 'public/js/transit_time')
    .sass('resources/assets/sass/app.scss', 'public/css');