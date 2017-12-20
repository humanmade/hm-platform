import React from 'react';
import PropTypes from 'prop-types';

import DashboardBlock from '../Dashboard-Block';
import PullRequestItem from './Pull-Request-Item';

const PullRequests = ( { items } ) => <DashboardBlock title="Pull Requests">
	{
		items.map( pr => <PullRequestItem
			date={ pr.date }
			id={ pr.id }
			link={ pr.link }
			status={ pr.status }
			statusText={ pr.statusText }
			title={ pr.title }
			key={ pr.id }
		/> )
	}
</DashboardBlock>



PullRequests.propTypes = {}

export default PullRequests;