import React from 'react';
import PropTypes from 'prop-types';

/**
 * Generic Dashboard wrapper block for displaying some information in the admin.
 *
 * @param {String} title   Title to display in collapsbile header.
 * @param {Array} children React children.
 */
const DashboardBlock = ( { title, children } ) => {
	return <div className="postbox-container">
		<div className="postbox">
			<button type="button" className="handlediv" aria-expanded="true">
				<span className="screen-reader-text">Toggle panel: { title }</span>
				<span className="toggle-indicator" aria-hidden="true" />
			</button>
			<h2 className="hndle"><span>{title}</span></h2>
			<div className="inside">
				{children}
			</div>
		</div>
	</div>
}

DashboardBlock.propTypes = { title: PropTypes.string }

export default DashboardBlock;
