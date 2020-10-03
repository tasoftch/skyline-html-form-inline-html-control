
const pkg = require('./package.json');
const name = pkg.name;
module.exports = (env = {}) => {
    return {
        mode: env.development ? 'development' : 'production',

        entry: './src',
        output: {
            filename: `./${name}.min.js`,
            library: name,
            libraryTarget: 'umd',
        },
        module: {
            rules: [{
                test: /\.js$/,
                loader: 'babel-loader',
                include: /src/,
            }],
        }
    };
};