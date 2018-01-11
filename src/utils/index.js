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

/**
 * Calculate the amount of time since a date and return in nice English.
 *
 * @param {String} time ISO timestamp
 * @return {String}
 */
export const getTimeSince = time => {
	const now = new Date();
	const then = new Date( time );

	// If we're dealing with the future we don't have a timeSince to parse. Bounce.
	if ( now < then ) {
		return null;
	}

	const diff = Math.floor( now - then );
	const microMinute = 60000; // Minute in microseconds
	const microHour = 3600000; // Hour in microseconds
	const microDay = 86400000; // Day in microseconds.

	if ( diff < microMinute ) {
		return 'just a minute ago';
	} else if ( diff < microHour ) {
		const time = Math.floor( diff / 1000 / 60 );
		return `${ time } minute${ time > 1 ? 's' : ''} ago`;
	} else if ( diff < microDay ) {
		const time = Math.floor( diff / microHour );
		return`${ time } hour${ time > 1 ? 's' : ''} ago`;
	} else if ( diff > microDay && diff < ( microDay * 2 ) ) {
		return 'yesterday';
	} else {
		return `${ Math.floor( diff / microDay ) } days ago`;
	}
};

/**
 * Convert bytes to gigabytes and return a nicely formatted number.
 *
 * @param bytes
 * @returns {string}
 */
export const convertBytesToGigabytes = bytes => {
	return Number( bytes / 1073741824 ).toLocaleString( undefined, { maximumFractionDigits: 0 } );
}
