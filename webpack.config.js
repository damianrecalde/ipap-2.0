const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin'); // Importamos el plugin

// Manually configure the runtime environment if not already configured by the "encore" command.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    .setOutputPath('public/build/')
    .setPublicPath('/build')
    .addEntry('app', './assets/app.js')
    .splitEntryChunks()
    .enableSingleRuntimeChunk()
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    .enableVersioning(Encore.isProduction())
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.38';
    })
    .enablePostCssLoader();

const config = Encore.getWebpackConfig();

// 🔹 Agregar el plugin para copiar las imágenes
config.plugins.push(
    new CopyWebpackPlugin({
        patterns: [
            { from: 'assets/images', to: 'images' } // Copia todas las imágenes a public/build/images
        ]
    })
);

module.exports = config;
