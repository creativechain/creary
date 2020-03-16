const mix = require('laravel-mix');
const fs = require('fs');

class ResourceConfig {
    constructor(type, filesDir, outputDir, excludeFiles = [], minify = false) {
        this.type = type;
        /*this.filesDir = filesDir;*/
        this.outputDir = outputDir;
        this.excludeFiles = excludeFiles || [];

        let files = fs.readdirSync(filesDir);
        this.excludeFiles.forEach(function (value) {
            if (files.includes(value)) {
                files.splice(files.indexOf(value), 1)
            }
        });

        files.forEach(function (value, index) {
            files[index] = filesDir + value;
        });

        this.files = files;
        this.minify = minify;

    }
}

const JS_CONTROL_CONFIG = new ResourceConfig('js', 'resources/js/front/', 'public/js/control/', ['required', 'components'], true);
const JS_FAQ_ES_CONFIG = new ResourceConfig('js', 'resources/js/faq/es', 'public/js/faq/es', ['required', 'components'], true);
const JS_FAQ_EN_CONFIG = new ResourceConfig('js', 'resources/js/faq/en', 'public/js/faq/en', ['required', 'components'], true);

const SASS_CONFIG = new ResourceConfig('sass', 'resources/sass/', 'public/css/custom/', ['imported']);

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

/**
 *
 * @param {ResourceConfig} config
 */
function applyConf(config) {
    let toMinifyFiles = [];
    config.files.forEach(function (file) {
        mix[config.type](file, config.outputDir);
        if (config.minify) {
            let fileParts = file.split('/');
            let fileName = fileParts.pop();
            toMinifyFiles.push(config.outputDir + fileName);
        }
    });

    if (toMinifyFiles.length > 0) {
        //mix.minify(toMinifyFiles);
    }
}

applyConf(SASS_CONFIG);
applyConf(JS_CONTROL_CONFIG);
applyConf(JS_FAQ_ES_CONFIG);
applyConf(JS_FAQ_EN_CONFIG);

//Copy fonts
mix.copyDirectory('resources/assets/fonts', 'public/fonts');

mix.disableSuccessNotifications();
