import React from 'react';
import PropTypes from 'prop-types';

import DashboardBlock from '../Dashboard-Block';
import PullRequestItem from './Pull-Request-Item';

/**
 * List all open Pull Requests against the particular site.
 *
 * @param {Array} items Pull Requests.
 */
const PullRequests = ( { items } ) => <DashboardBlock title="Pull Requests">
	{ ( items && items.length > 0 )
		? <ul>
			{ items.map( pr => <PullRequestItem
				date={ pr.date }
				id={ pr.id }
				link={ pr.link }
				status={ pr.status }
				statusText={ pr.statusText }
				title={ pr.title }
				key={ pr.id }
			/> ) }
		</ul>
		: <p>No Open Pull Requests</p>
	}
</DashboardBlock>

PullRequests.defaultTypes = { items: [] }

PullRequests.propTypes = {
	items: PropTypes.shape( {
		date:       PropTypes.string,
		id:         PropTypes.number,
		link:       PropTypes.string,
		status:     PropTypes.string,
		statusText: PropTypes.string,
		title:      PropTypes.string,
	} ),
}

export default PullRequests;
