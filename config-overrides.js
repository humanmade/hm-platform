const { compose } = require( 'react-app-rewired' );
const rewireStyledComponents = require( 'react-app-rewire-styled-components' );
const rewireSVGReactLoader = require( 'react-app-rewire-svg-react-loader' );

//  custom config
module.exports = function ( config, env ) {
	const rewires = compose(
		rewireStyledComponents,
		rewireSVGReactLoader,
	);

	return rewires( config, env );
}
