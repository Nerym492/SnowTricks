const Encore = require('@symfony/webpack-encore');
const StylelintPlugin = require('stylelint-webpack-plugin');

Encore
  .enableSingleRuntimeChunk()
  .setOutputPath('public/build/')
  .setPublicPath('/build')
  .addEntry('app', './assets/js/app.js')
  .addStyleEntry('styles', './assets/sass/app.scss')
  .enableSassLoader()
  .enablePostCssLoader()
  .cleanupOutputBeforeBuild()
  .enableSourceMaps(!Encore.isProduction())
  .enableVersioning(Encore.isProduction())

const webpackConfig = Encore.getWebpackConfig();

module.exports = {
  module: {
    rules: [
      {
        test: /\.(png|jpe?g|gif)$/i,
        type: 'asset/resource',
        generator: {
          filename: 'images/[name][ext]',
        },
      },
    ],
  },
  plugins: [
    new StylelintPlugin({
      configFile: '.stylelintrc.json',
      files: '**/*.css', // Chemin vers les fichiers CSS générés par Sass
    }),
  ],
  ...webpackConfig,
};






