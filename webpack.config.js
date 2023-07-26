// eslint-disable-next-line no-undef
const Encore = require("@symfony/webpack-encore");
// eslint-disable-next-line no-undef
const CopyWebpackPlugin = require("copy-webpack-plugin");

Encore.enableSingleRuntimeChunk()
  .setOutputPath("public/build/")
  .setPublicPath("/build")
  .addEntry("home", "./assets/js/pages/home.js")
  .addEntry("trick", "./assets/js/pages/trick.js")
  .addEntry("trick-details", "./assets/js/pages/trick-details.js")
  .addEntry("reset-password", "./assets/js/pages/reset-password.js")
  .addEntry("login", "./assets/js/pages/login.js")
  .addEntry("register", "./assets/js/pages/register.js")
  .addEntry("page404", "./assets/js/pages/page404.js")
  .addEntry("bootstrap", "bootstrap")
  .addStyleEntry("styles", "./assets/sass/app.scss")
  .splitEntryChunks()
  .enableSassLoader()
  .enablePostCssLoader()
  .cleanupOutputBeforeBuild()
  .enableVersioning(Encore.isProduction())
  .addPlugin(
    new CopyWebpackPlugin({
      patterns: [
        {
          from: "./assets/images",
          to: "images/[path][name][ext]",
          globOptions: {
            ignore: ["**/header/**"], // Ignorer les fichiers dans le sous-dossier "header"
          },
        },
        {
          from: "./assets/animations",
          to: "animations/[path][name][ext]",
        },
      ],
    }),
  );

// eslint-disable-next-line no-undef
module.exports = Encore.getWebpackConfig();
