const mix = require('laravel-mix');
require('laravel-mix-purgecss');

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


// mix.setPublicPath(__dirname + "/public");
// mix.setResourceRoot(__dirname + "/resources");

mix.js('resources/js/app.js', 'public/js').vue({extractStyles: true, globalStyles: 'resources/sass/general/_variables.scss'})
// .vue({ extractStyles: 'public/css/app.css' })
.sass('resources/sass/app.scss', 'public/css')
.purgeCss({enabled: true})
.copy("resources/assets", "public/images") // <- Might be optional
// .options(
//     {
//        processCssUrls: false // <- Might be optional
//     }
// )








