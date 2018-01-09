import React from 'react';
import PropTypes from 'prop-types';

import DashboardBlock from '../Dashboard-Block';
import AlertItem from './Alert-Item';

/**
 * List all open alerts/activity on a site's server.
 *
 * @param {Array} items Alerts.
 */
const AlertsLog = ( { items } ) => <DashboardBlock title="Activity Log">
	{ ( items && items.length > 0 )
		? <ul className="alert-listing">
			{ items.map( alert => <AlertItem key={ alert.id } {...alert} /> ) }
		</ul>
		: <p>No Activity to Report</p>
	}
</DashboardBlock>

AlertsLog.defaultTypes = { items: [] }

AlertsLog.propTypes = {
	items: PropTypes.shape( {
		date:       PropTypes.string,
		level:      PropTypes.number,
		message:    PropTypes.string,
	} ),
}

export default AlertsLog;
