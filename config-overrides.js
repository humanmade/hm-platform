const { compose } = require( 'react-app-rewired' );
const rewireStyledComponents = require( 'react-app-rewire-styled-components' );
const rewireSVGReactLoader = require( 'react-app-rewire-svg-react-loader' );
const HtmlWebpackPlugin = require('html-webpack-plugin');
const ModuleScopePlugin = require('react-dev-utils/ModuleScopePlugin');

let paths = require( 'react-scripts/config/paths' );
paths.appBuild = paths.appBuild.replace( __dirname, `${__dirname}/admin` );
paths.appPublic = paths.appPublic.replace( __dirname, `${__dirname}/admin` );
paths.appHtml = paths.appHtml.replace( __dirname, `${__dirname}/admin` );
paths.appIndexJs = paths.appIndexJs.replace( __dirname, `${__dirname}/admin` );
paths.appSrc = paths.appSrc.replace( __dirname, `${__dirname}/admin` );
paths.testsSetup = paths.testsSetup.replace( __dirname, `${__dirname}/admin` );

//  custom config
module.exports = function ( config, env ) {
	const rewires = compose(
		rewireStyledComponents,
		rewireSVGReactLoader,
	);

	// Edit entry array.
	config = Object.assign( {}, config, {
		entry: [
			require.resolve('react-scripts/config/polyfills'),
			require.resolve('react-dev-utils/webpackHotDevClient'),
			paths.appIndexJs,
		],
		resolve: Object.assign( {}, config.resolve, {
			plugins: [
				new ModuleScopePlugin(paths.appSrc, [paths.appPackageJson]),
			]
		} ),
		plugins: config.plugins.filter( plugin => ! ( plugin instanceof HtmlWebpackPlugin ) ).concat( [
			new HtmlWebpackPlugin({
				inject: true,
				template: paths.appHtml,
			})
		] )
	} );

	console.log( paths, config.plugins )

	return rewires( config, env );
}
