import React from 'react';
import PropTypes from 'prop-types';

import DashboardBlock from '../Dashboard-Block';

/**
 * Data about the current environment's data.
 *
 * @param {Array} items Pull Requests.
 */

/**
 * Data about the current environment's data.
 *
 * @param {Object} gitData        Data about the current Git state in this environment.
 * @param {Object} enironmentData Data about the HM Cloud environment in this environment.
 */
const EnvironmentData = ( { gitData, enironmentData } ) => <DashboardBlock title="Application Data">
	<div className="environment-data-block">
		<h3 className="environment-data-block__title">Application Version</h3>
		<dl>
			<dt>Git Branch:</dt>
			<dd>{ gitData.branch }</dd>
			<dt>Commit:</dt>
			<dd>{  }</dd>
		</dl>
	</div>
	<div className="environment-data-block">
		<h3 className="environment-data-block__title">HM Cloud Version</h3>
		<dl>
			<dt>PHP:</dt>
			<dd>{ enironmentData.php }</dd>
			<dt>MySQL:</dt>
			<dd>{ enironmentData.mySql }</dd>
			<dt>Elasticsearch:</dt>
			<dd>{ enironmentData.elasticsearch }</dd>
		</dl>
	</div>
</DashboardBlock>

EnvironmentData.propTypes = {
	gitData: PropTypes.shape( {
		branch: PropTypes.string,
		commit: PropTypes.shape( {
			date:        PropTypes.string,
			description: PropTypes.string,
			rev:         PropTypes.string,
			status:      PropTypes.string,
		} ),
	} ),
	enironmentData: PropTypes.shape( {
		php:          PropTypes.number,
		mySql:        PropTypes.number,
		elsticsearch: PropTypes.number,
	} ),
}

export default EnvironmentData;
