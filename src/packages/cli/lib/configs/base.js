module.exports = (webpackConfig, service) => {
    const path = require('path')
    const options = service.options
    const outputDir = options.getOption('outputDir', './dist/')
    const publicPath = service.getPublicPath()

    const resolveLocal = require('../util/resolveLocal')
    const inlineLimit = 4096

    const genAssetSubPath = dir => {
      return `${dir}/[name]${options.filenameHashing ? '.[hash:8]' : ''}.[ext]`

    }

    const genUrlLoaderOptions = dir => {
      return {
        limit: inlineLimit,
        // publicPath: './',
        // use explicit fallback to avoid regression in url-loader>=1.1.0
        fallback: {
          loader: require.resolve('file-loader'),
          options: {
            name: genAssetSubPath(dir),
            esModule: false
          }
        }
      }
    }

    const relativePath = path.relative( service.context, outputDir )
    webpackConfig
      .mode('production')
      .context(service.context)
      .output
        .filename('[name].js')
        .path(service.resolve(outputDir))
        .publicPath(publicPath)

      webpackConfig
        .performance
        .hints(false)

    webpackConfig.resolve
        // This plugin can be removed once we switch to Webpack 6
        .plugin('pnp')
          .use({ ...require('pnp-webpack-plugin') })
          .end()
        .extensions
          .merge(['.mjs', '.js', '.jsx', '.tsx', '.ts', '.vue', '.json', '.wasm'])
          .end()
        .modules
          .add('node_modules')
          .add(service.resolve('node_modules'))
          .add(resolveLocal('node_modules'))
          .end()
        .alias
          .set('@', service.resolve('src'))
          .set(
            'vue$',
            options.runtimeCompiler
              ? 'vue/dist/vue.esm.js'
              : 'vue/dist/vue.runtime.esm.js'
          )

    webpackConfig.resolveLoader
        .plugin('pnp-loaders')
          .use({ ...require('pnp-webpack-plugin').topLevelLoader })
          .end()
        .modules
          .add('node_modules')
          .add(service.resolve('node_modules'))
          .add(resolveLocal('node_modules'))

    webpackConfig.module
        .noParse(/^(vue|vue-router|vuex|vuex-router-sync)$/)

    // vue-loader --------------------------------------------------------------
	const vueLoaderCacheConfig = api.genCacheConfig('vue-loader', {
        'vue-loader': require('vue-loader/package.json').version,
        '@vue/compiler-sfc': require('@vue/compiler-sfc/package.json').version
      })

      webpackConfig.resolve
        .alias
          .set(
            'vue$',
            options.runtimeCompiler
              ? 'vue/dist/vue.esm-bundler.js'
              : 'vue/dist/vue.runtime.esm-bundler.js'
          )

      webpackConfig.module
        .rule('vue')
          .test(/\.vue$/)
          .use('cache-loader')
            .loader(require.resolve('cache-loader'))
            .options(vueLoaderCacheConfig)
            .end()
          .use('vue-loader')
            .loader(require.resolve('vue-loader-v16'))
            .options({
              ...vueLoaderCacheConfig,
              babelParserPlugins: ['jsx', 'classProperties', 'decorators-legacy']
            })
            .end()
          .end()

      webpackConfig
        .plugin('vue-loader')
          .use(require('vue-loader-v16').VueLoaderPlugin)

      // feature flags <http://link.vuejs.org/feature-flags>
      webpackConfig
        .plugin('feature-flags')
          .use(require('webpack').DefinePlugin, [{
            __VUE_OPTIONS_API__: 'true',
            __VUE_PROD_DEVTOOLS__: 'false'
          }])

    // static assets -----------------------------------------------------------
    webpackConfig.module
      .rule('images')
        .test(/\.(png|jpe?g|gif|webp)(\?.*)?$/)
        .use('url-loader')
            .loader(require.resolve('url-loader'))
            .options(genUrlLoaderOptions('img'))

    webpackConfig.module
      .rule('ts')
      .test(/\.tsx?$/)
      .exclude
        .add(/node_modules/)
        .add(/vendor/)
        .end()
      .use('ts-loader')
      .loader("ts-loader")


    // do not base64-inline SVGs.
    // https://github.com/facebookincubator/create-react-app/pull/1180
    webpackConfig.module
      .rule('svg')
        .test(/\.(svg)(\?.*)?$/)
        .use('file-loader')
            .loader(require.resolve('file-loader'))
            .options({
                name: genAssetSubPath('img'),
                esModule: false
            })

    webpackConfig.module
      .rule('media')
        .test(/\.(mp4|webm|ogg|mp3|wav|flac|aac)(\?.*)?$/)
        .use('url-loader')
            .loader(require.resolve('url-loader'))
            .options(genUrlLoaderOptions('media'))

    webpackConfig.module
      .rule('fonts')
        .test(/\.(woff2?|eot|ttf|otf)(\?.*)?$/i)
        .use('url-loader')
            .loader(require.resolve('url-loader'))
            .options(genUrlLoaderOptions('fonts'))

    // shims
    webpackConfig.node
      .merge({
            // prevent webpack from injecting useless setImmediate polyfill because Vue
            // source contains it (although only uses it if it's native).
            setImmediate: false,
            // process is injected via DefinePlugin, although some 3rd party
            // libraries may require a mock to work properly (#934)
            process: 'mock',
            // prevent webpack from injecting mocks to Node native modules
            // that does not make sense for the client
            dgram: 'empty',
            fs: 'empty',
            net: 'empty',
            tls: 'empty',
            child_process: 'empty'
      })

    const resolveClientEnv = require('../util/resolveClientEnv')
    webpackConfig
        .plugin('define')
            .use(require('webpack').DefinePlugin, [{
              'process.env.NODE_ENV': JSON.stringify( process.env.NODE_ENV ),
                __ZIONBUILDER__: JSON.stringify({
                  appName: service.pkg.name
                })
              }]
            )

    webpackConfig.externals([
      function( context, request, callback ){
        if (/^@zb\/.*$/.test(request)){
          const modules = request.replace('@', '').split('/')
          // Externalize to a commonjs module using the request path
          return callback(null, modules, 'root');
        }

        callback()
      }
    ])

    webpackConfig
        .plugin('case-sensitive-paths')
            .use(require('case-sensitive-paths-webpack-plugin'))

    // friendly error plugin displays very confusing errors when webpack
    // fails to resolve a loader, so we provide custom handlers to improve it
    const { transformer, formatter } = require('../util/resolveLoaderError')
    webpackConfig
        .plugin('friendly-errors')
            .use(require('@soda/friendly-errors-webpack-plugin'), [{
                additionalTransformers: [transformer],
                additionalFormatters: [formatter]
            }])

    const TerserPlugin = require('terser-webpack-plugin')
    const terserOptions = require('./terserOptions')
    webpackConfig.optimization
        .minimizer('terser')
            .use(TerserPlugin, [terserOptions(options)])
}