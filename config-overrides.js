const { compose } = require('react-app-rewired');
const rewireStyledComponents = require('react-app-rewire-styled-components');

module.exports = compose(
	rewireStyledComponents,
)

//  custom config
module.exports = function(config, env){
	const rewires = compose(
		rewireStyledComponents,
	);

	console.log( config )

	return rewires(config, env);
}
