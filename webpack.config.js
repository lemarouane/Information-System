const Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
} 

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('build')
    // only needed for CDN's or subdirectory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG 
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', './assets/app.js')
    .addEntry('langues', './assets/js/langues.js')
    .addEntry('dependent_selects', './assets/js/ds.js')
    .addEntry('datatable_trans', './assets/js/datatable_trans.js')
    .addEntry('stats_rh', './assets/js/stats_rh.js') 
    .addEntry('orientation', './assets/js/orientation.js') 
    .addEntry('scolarite', './assets/js/scolarite.js') 
    .addEntry('convention', './assets/js/convention.js') 
    .addEntry('scolarite_stat', './assets/js/scolarite_stat.js') 
    .addEntry('cooperation_stat', './assets/js/cooperation_stat.js') 
    .addEntry('emploi', './assets/js/emploi.js') 
    .addEntry('tablesuivi', './assets/js/tablesuivi.js') 
    .addEntry('orientation_home', './assets/js/orientation_home.js') 
    .addEntry('att_type_select', './assets/js/att_type_select.js')
    .addEntry('validation_attestation', './assets/js/validation_attestation.js')     
    .addEntry('validation_autorisation', './assets/js/validation_autorisation.js')
    .addEntry('validation_conge', './assets/js/validation_conge.js')
    .addEntry('validation_ordremission', './assets/js/validation_ordremission.js')
  

    .addEntry('validation_reprise', './assets/js/validation_reprise.js')
    .addEntry('validation_ficheheure', './assets/js/validation_ficheheure.js')
    .addEntry('engagement ficheheure', './assets/js/engagement ficheheure.js')
    .addEntry('add_roles', './assets/js/add_roles.js')

    .addEntry('app_counter', './assets/js/app_counter.js')
    .addEntry('programmeEmploi', './assets/js/programmeEmploi.js')
    .addEntry('app_counter_scolarite', './assets/js/app_counter_scolarite.js')
    .addEntry('app_counter_scolariteFC', './assets/js/app_counter_scolariteFC.js')
    .addEntry('app_counter_cooperation', './assets/js/app_counter_cooperation.js')
    .addEntry('note_annuelle_calc', './assets/js/note_annuelle_calc.js')
    .addEntry('conge_calc', './assets/js/conge_calc.js')
    .addEntry('conventionDD', './assets/js/conventionDD.js')
    .addEntry('inscriptionDD', './assets/js/inscriptionDD.js')
    .addEntry('conge_calc_edit', './assets/js/conge_calc_edit.js')
    .addEntry('ordre_m_calc', './assets/js/ordre_m_calc.js')
    .addEntry('ordre_m_calc_edit', './assets/js/ordre_m_calc_edit.js')
    .addEntry('engagement', './assets/js/engagement.js')
    .addEntry('bootstrap-datepicker.min', './assets/js/bootstrap-datepicker.min.js')
    .addEntry('bootstraprtl', './assets/css/bootstrap-rtl.css')
    .addEntry('stylertl', './assets/css/style-rtl.css')
    .addEntry('stats_perso_dem', './assets/js/stats_perso_dem.js')
    .addEntry('datedeces', './assets/js/datedeces.js')


  


    // enables the Symfony UX Stimulus bridge (used in assets/bootstrap.js)
    .enableStimulusBridge('./assets/controllers.json')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // configure Babel
    // .configureBabel((config) => {
    //     config.plugins.push('@babel/a-babel-plugin');
    // })

    // enables and configure @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = '3.23';
    })



    .copyFiles({
        from: './assets/images',

         // optional target path, relative to the output dir
         to: 'images/[path][name].[ext]',

         // if versioning is enabled, add the file hash too
         //to: 'images/[path][name].[hash:8].[ext]',

         // only copy files matching this pattern
         //pattern: /\.(png|jpg|jpeg)$/
     }) 

    .copyFiles({
            from: './assets/css',
            to: 'css/[path][name].[hash:8].[ext]',
    })
    .copyFiles({
        from: './assets/js',
        to: 'js/[path][name].[ext]',
    })

    .copyFiles({
        from: './assets/plugins',
        to: 'plugins/[path][name].[hash:8].[ext]',
    })








    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment if you use React
    //.enableReactPreset()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
