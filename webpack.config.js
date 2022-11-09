const Encore = require('@symfony/webpack-encore');
const CompressionPlugin = require("compression-webpack-plugin");

if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .addPlugin(new CompressionPlugin({
        test: /\.(js|css)$/,
        filename: '[path][name][ext].gz[query]',
        minRatio: 0.8,
        algorithm: 'gzip',
        deleteOriginalAssets: false
    }))
    .enablePostCssLoader()
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .enableStimulusBridge('./assets/controllers.json')
    .addEntry('app', './assets/app.ts')
    .splitEntryChunks()
    .copyFiles({
        from: './assets/images',
        to: 'assets/images/[path][name].[ext]',
        pattern: /\.(png|jpg|jpeg|svg|webmanifest|webp)$/
    })
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSassLoader()
    .enableSourceMaps(!Encore.isProduction())
    // .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })
;

module.exports = Encore.getWebpackConfig();
