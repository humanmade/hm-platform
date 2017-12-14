/**
 * Helper functions that don't need their own file.
 */

// eslint-disable-next-line
Array.prototype.unique = function() {
	return this
		.map( item => JSON.stringify( item ) )
		.reduce( ( result, item ) => {
			if ( result.includes( item ) ) {
				return result;
			}
			result.push( item );
			return result;
		}, [] )
		.map( item => JSON.parse( item ) );
};

/**
 * Get an array of documents for the current URL from the docs site config.
 *
 * @param config
 * @returns {Array}
 */
export const getDocsForURL = config => {
	return Object.keys( config )
		.filter( pattern => window.location.href.match( new RegExp( pattern.replace( /^\/?(.*?)\/?$/, '$1' ) ) ) )
		.reduce( ( posts, pattern ) => posts.concat( config[ pattern ] ), [] )
		.unique();
}
