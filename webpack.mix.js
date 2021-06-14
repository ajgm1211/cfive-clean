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
    .js('resources/js/contracts_lcl/index.js', 'public/js/contracts_lcl')
    .js('resources/js/contracts_lcl/edit.js', 'public/js/contracts_lcl')
    .js('resources/js/inlands/edit.js', 'public/js/inlands')
    .js('resources/js/inlands/index.js', 'public/js/inlands')
    .js('resources/js/inlands/location.js', 'public/js/inlands')
    .js('resources/js/transit_time/index.js', 'public/js/transit_time')
    .js('resources/js/saleterms/index.js', 'public/js/saleterms')
    .js('resources/js/saleterms/edit.js', 'public/js/saleterms')
    .js('resources/js/saleterms/list.js', 'public/js/saleterms')
    .js('resources/js/saleterms/codes.js', 'public/js/saleterms')
    .js('resources/js/quote/edit.js', 'public/js/quote')
    .js('resources/js/quote/index.js', 'public/js/quote')
    .js('resources/js/providers/index.js', 'public/js/providers')
    .js('resources/js/search/index.js', 'public/js/search')
    .sass('resources/sass/app.scss', 'public/css');