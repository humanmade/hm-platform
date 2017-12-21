import React from 'react';
import PropTypes from 'prop-types';
import { VictoryLine, VictoryChart, VictoryAxis, VictoryTooltip } from 'victory';

import DashboardBlock from '../Dashboard-Block';
import { adminTheme } from '../../victory-theme';

/**
 * Display current bandwidth usage against a site for a rolling 30-day period.
 *
 * @param {Array} responseTimeHistory An array of usage data for the current site.
 */
const PageGenerationTime = ( { responseTimeHistory } ) => {
	const highestTime = responseTimeHistory.reduce( ( carry, item ) => { return item.time > carry ? item.time : carry }, 0 );

	return <DashboardBlock title="Page Generation Time">
		<VictoryChart
			theme={ adminTheme }
			domainPadding={ 10 }
		>
			<VictoryAxis
				dependentAxis
				tickCount={ 5 }
				tickFormat={ y => `${ Number( y ).toFixed( 0 ) } ms` }
			/>
			<VictoryAxis
				tickCount={ 6 }
				tickFormat={ x => new Date( x ).getDate() }
				style={ { grid: {
						fill: "none",
						stroke: "none",
						pointerEvents: "visible"
					} } }
			/>
			<VictoryLine
				data={ responseTimeHistory }
				labels={ datum => {
					if ( datum.time !== highestTime ) {
						return '';
					}

					return `${ datum.time } ms`
				} }
				x="date"
				y="time"
			/>
		</VictoryChart>
	</DashboardBlock>
}

PageGenerationTime.defaultTypes = { usageHistory: [] }

PageGenerationTime.propTypes = {
	usageHistory: PropTypes.arrayOf( PropTypes.shape( {
		time: PropTypes.number,
		date: PropTypes.string,
	} ) ),
}

export default PageGenerationTime;


