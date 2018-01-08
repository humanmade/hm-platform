import React from 'react';
import PropTypes from 'prop-types';

import DashboardBlock from '../Dashboard-Block';

/**
 * Data about the current environment's data.
 *
 * @param {Object} gitData        Data about the current Git state in this environment.
 * @param {Object} environmentData Data about the HM Cloud environment in this environment.
 */
const EnvironmentData = ( { gitData, environmentData } ) => <DashboardBlock title="Application Data" id="environment-data">
	<div className="environment-data">
		<div className="environment-data-block">
			<h3 className="environment-data-block__title">Application Version</h3>
			<dl>
				<dt>Git Branch:</dt>
				<dd>{ gitData.branch }</dd>
				<dt>Commit:</dt>
				<dd>{ gitData.commit && `${ gitData.commit.description } (${ gitData.commit.rev.substring( 0, 7 ) })` }</dd>
			</dl>
		</div>
		<div className="environment-data-block">
			<h3 className="environment-data-block__title">HM Cloud Software</h3>
			<dl>
				<dt>PHP:</dt>
				<dd>v{ environmentData.php }</dd>
				<dt>MySQL:</dt>
				<dd>v{ environmentData.mySql }</dd>
				<dt>Elasticsearch:</dt>
				<dd>v{ environmentData.elasticsearch }</dd>
			</dl>
		</div>
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
