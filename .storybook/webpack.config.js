const path = require( 'path' );

module.exports = {
	module: {
		rules: [
			{
				test: /.(png|gif|jpg|jpeg|svg)(\?[a-z0-9=.]+)?$/,
				loader: 'url-loader?limit=100000'
			},
			{
				test: /.(scss|css)$/,
				loaders: [ 'style-loader', 'css-loader' ],
				include: [
					path.resolve( __dirname, '../../../wordpress/wp-admin/css' ),
					path.resolve( __dirname, '../src' ),
				],
				exclude: [
					/\.woff/,
					/\.woff2/,
					/\.eot/,
					/\.ttf/,
					/\.svg/,
				],
			},
		],
	},
};
