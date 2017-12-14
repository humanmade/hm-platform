const { compose } = require( 'react-app-rewired' );
const rewireStyledComponents = require( 'react-app-rewire-styled-components' );
const rewireSVG = require( 'react-app-rewire-svg-react-loader' );
const rewireSass = require('react-app-rewire-sass');

//  custom config
module.exports = function ( config, env ) {
	const rewires = compose(
		rewireStyledComponents,
		rewireSVG,
		rewireSass,
	);

	return rewires( config, env );
}
