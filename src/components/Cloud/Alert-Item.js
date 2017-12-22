import React from 'react';
import PropTypes from 'prop-types';

import { getTimeSince } from '../../utils';

/**
 * Display status about a single Pull Request.
 *
 * @param {String} date       Date that the pull request was opened.
 */
const AlertItem = ( { date, level, message } ) => {
	const parsedDate = new Date( date );
	return <li className={ `alert-item alert-item--${ level }` }>
		<p className='alert-item__message'>{ message }</p>
		<time className="alert-item__time"><i>Updated { getTimeSince( date ) }</i></time>
	</li>
}

AlertItem.defaultProps = {};

AlertItem.propTypes = {
	date:    PropTypes.string,
	id:      PropTypes.number,
	level:   PropTypes.string,
	message: PropTypes.string,
};

export default AlertItem;
