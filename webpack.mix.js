const mix = require('laravel-mix');
require('laravel-mix-purgecss');
require('dotenv').config();

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/app.js', 'public/js').vue({extractStyles: true, globalStyles: 'resources/sass/general/_variables.scss'})

.sass('resources/sass/app.scss', 'public/css')
.purgeCss({enabled: true})
.copy("resources/assets", "public/images")









