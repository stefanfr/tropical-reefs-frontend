const Encore = require('@symfony/webpack-encore');
const CompressionPlugin = require("compression-webpack-plugin");

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .copyFiles([
        {from: './node_modules/ckeditor4/', to: 'ckeditor/[path][name].[ext]', pattern: /\.(js|css)$/, includeSubdirectories: false},
        {from: './node_modules/ckeditor4/adapters', to: 'ckeditor/adapters/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/lang', to: 'ckeditor/lang/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/plugins', to: 'ckeditor/plugins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/skins', to: 'ckeditor/skins/[path][name].[ext]'},
        {from: './node_modules/ckeditor4/vendor', to: 'ckeditor/vendor/[path][name].[ext]'}
    ])
    .addPlugin(new CompressionPlugin({
        test: /\.(js|css)$/,
        filename: '[path][name][ext].gz[query]',
        minRatio: 0.8,
        algorithm: 'gzip',
        deleteOriginalAssets: false
    }))
    .configureTerserPlugin((options) => {
        options.cache = true;
        options.terserOptions = {
            output: {
                comments: false
            }
        }
    })
    .enablePostCssLoader()
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addStyleEntry('manager', './assets/styles/manager.scss')
    .enableStimulusBridge('./assets/controllers.json')
    .addEntry('app', './assets/app.ts')
    .addEntry('glide', './assets/glide.ts')
    .splitEntryChunks()
    .copyFiles({
        from: './assets/images',
        to: 'assets/images/[path][name].[ext]',
        pattern: /\.(png|jpg|jpeg|svg|webmanifest|webp|.mp4)$/
    })
    .copyFiles({
        from: './assets/videos',
        to: 'assets/videos/[path][name].[ext]',
        pattern: /\.(mp4)$/
    })
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
;

module.exports = Encore.getWebpackConfig();
