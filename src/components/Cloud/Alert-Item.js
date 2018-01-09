import React from 'react';
import PropTypes from 'prop-types';

import { getTimeSince } from '../../utils';

/**
 * Display status about a single Pull Request.
 *
 * @param {String} date    Date that the pull request was opened.
 * @param {String} level   Level of intensity of the alert.
 * @param {String} message Alert message to display.
 */
const AlertItem = ( { date, level, message } ) => {
	const parsedDate = new Date( date );
	return <li className={ `alert-item alert-item--${ level }` }>
		<p className='alert-item__message'>{ message }</p>
		<time datetime={ parsedDate.toISOString() } className="alert-item__time">Updated { getTimeSince( date ) }</time>
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
