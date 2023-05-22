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
  .copyFiles({
    from: './assets/images',
    to: 'images/[path][name].[ext]',
  })
  .copyFiles({
    from: './assets/fonts',
    to: 'fonts/[path][name].[ext]',
  });

const webpackConfig = Encore.getWebpackConfig();

module.exports = {
  plugins: [
    new StylelintPlugin({
      configFile: '.stylelintrc.json',
      files: '**/*.css', // Chemin vers les fichiers CSS générés par Sass
    }),
  ],
  ...webpackConfig,
};






