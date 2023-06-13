const Encore = require('@symfony/webpack-encore');
const CopyWebpackPlugin = require('copy-webpack-plugin');


Encore
  .enableSingleRuntimeChunk()
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('home', './assets/js/pages/home.js')
  .addEntry('trick', './assets/js/pages/trick.js')
  .addStyleEntry('styles', './assets/sass/app.scss')
  .enableSassLoader()
  .enablePostCssLoader()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())
  .addPlugin(new CopyWebpackPlugin({
    patterns: [
      {
        from: './assets/images',
        to: 'images/[path][name][ext]',
        globOptions: {
          ignore: ['**/header/**'] // Ignorer les fichiers dans le sous-dossier "header"
        }
      },
    ]
  }))

module.exports = Encore.getWebpackConfig();

