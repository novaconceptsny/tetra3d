const mix = require('laravel-mix');

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
    .js('resources/js/modules/photo/index.js','public/js/modules/photo/index.js')
    // .sass('resources/sass/style.scss', 'public/css')
    .sass('resources/sass/page/photoeditor.scss', 'public/css/page')
    .sass('resources/sass/page/tour360.scss', 'public/css/page')
    .sass('resources/sass/page/photo-index.scss', 'public/css/page')
    .sourceMaps();
