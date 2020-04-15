var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // .addEntry('js/app', './assets/js/index.js')
    .addEntry('js/site/core', './assets/site/js/index.js')
    .addEntry('js/admin/app', [
        './assets/admin/js/index.js'
    ])
    .addStyleEntry('css/site/app', './assets/site/scss/style.scss')
    .addStyleEntry('css/site/pages/home', './assets/site/scss/Pages/_home.scss')
    .addStyleEntry('css/site/pages/shop', './assets/site/scss/Pages/_shop.scss')
    .addStyleEntry('css/admin/app',[
        './assets/admin/scss/style.scss',
    ])
    // uncomment if you use Sass/SCSS files
    .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()
    .autoProvideVariables({
        $: 'jquery',
        tjq: 'jquery',
        jQuery: 'jquery',
        'window.jQuery': 'jquery',
        'window.$': 'jquery',
    })
    .addLoader({
        test: /\.(htc)$/,
        use: [{
            loader: 'url-loader',
            options: {
                limit: 10000, // Convert images < 8kb to base64 strings
                name: '/[name].[hash].[ext]',
            }
        }]
    })
    .enableBuildNotifications(true, function (options) {
        options.alwaysNotify = true;
        options.title = 'DONE';
    })
    .enableSingleRuntimeChunk()
;

let config = Encore.getWebpackConfig();
config.resolve.alias = {
    'waypoints': __dirname + '/node_modules/jquery-waypoints/waypoints.js',
    'router': __dirname + '/assets/js/router.js'
};

module.exports = config;
