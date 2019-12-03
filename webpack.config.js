const path = require('path');
const CopyPlugin = require('copy-webpack-plugin');
const VueLoaderPlugin = require('vue-loader/lib/plugin');

module.exports = {
    
    //watch: true,
    mode: 'development',
    devtool:"source-map",
    module:{
        rules: [
            {
              test: /\.vue$/,
              loader: 'vue-loader'
            },
            {
              test: /\.css$/,
              use: [
                'vue-style-loader',
                'css-loader'
              ]
            },
            {
              test: /\.(ttf|woff|woff2)(\?.+)?$/,
              include: [
                path.resolve('src'),
                path.resolve('node_modules/element-ui/'),
              ],
              use: [
                {
                  loader: 'url-loader',
                  options: {
                    name:"fonts/[name].[ext]",
                    limit: 10000,
                  }
                }
              ]
            },
            {
                test: /\.js$/,
                exclude: /(node_modules|bower_components)/,
                use: [{
                  loader: 'babel-loader',
                  options: {
                    presets: ['@babel/preset-env']
                  }
                },"eslint-loader"]
              }
        ]
    },
    
    entry: {
        application: "./src/js/application.js",
        login: "./src/js/login.js"
    },
    output: {
        filename: 'js/[name].js',
        path: path.resolve(__dirname, 'dist'),
    },
    
    plugins: [
        new VueLoaderPlugin(),
        new CopyPlugin([
            { from: './src/back-end', to: 'back-end' },
            { from: './src/assets', to: 'assets' },
            { from: './uploads', to: 'uploads' },
            { from: './src/index.php', to: 'index.php' }
        ])
    ]
};