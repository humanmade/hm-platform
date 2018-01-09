/*
  WordPress admin Victory theme.
*/

/**
 * Colors.
 */
const blue = '#00a0d2';
const grey = '#72777c';
const lightGrey = '#eee';
const colors = [
	'#252525',
	'#525252',
	'#737373',
	'#969696',
	'#bdbdbd',
	'#d9d9d9',
	'#f0f0f0',
];

/**
 * Typography.
 */
const sansSerif = "-apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Oxygen-Sans, Ubuntu, Cantarell, \"Helvetica Neue\", sans-serif";
const letterSpacing = 'normal';
const fontSize = 9;

/**
 * Layout.
 */
const baseProps = {
	width:      500,
	height:     300,
	padding:    {
		top:    25,
		right:  25,
		bottom: 35,
		left:   50,
	},
	colorScale: colors
};

/**
 * Labels.
 */
const baseLabelStyles = {
	fontFamily: sansSerif,
	fontSize,
	letterSpacing,
	padding: 10,
	fill: grey,
	stroke: 'transparent'
};
const centeredLabelStyles = Object.assign({ textAnchor: 'middle' }, baseLabelStyles);

/**
 * Strokes
 */
const strokeLinecap = 'round';
const strokeLinejoin = 'round';

/**
 * Put it all together.
 */
export const adminTheme = {
	area: Object.assign( {
		style: {
			data:   { fill: grey },
			labels: centeredLabelStyles
		}
	}, baseProps ),
	axis: Object.assign( {
		style: {
			axis: {
				fill:        'transparent',
				stroke:      lightGrey,
				strokeWidth: 2,
				strokeLinecap,
				strokeLinejoin
			},
			axisLabel: Object.assign({}, centeredLabelStyles, {
				padding: 25
			} ),
			grid: {
				fill:          'none',
				stroke:        lightGrey,
				pointerEvents: 'visible'
			},
			ticks: {
				fill:   'transparent',
				size:   1,
				stroke: 'transparent'
			},
			tickLabels: baseLabelStyles
		}
	}, baseProps ),
	bar: Object.assign( {
		style: {
			data: {
				fill:        blue,
				padding:     8,
				strokeWidth: 0
			},
			labels: baseLabelStyles
		}
	}, baseProps ),
	chart: baseProps,
	line: Object.assign( {
		style: {
			data: {
				fill:        'transparent',
				stroke:      blue,
				strokeWidth: 2
			},
			labels: centeredLabelStyles
		}
	}, baseProps ),
	tooltip: {
		style:       Object.assign({}, centeredLabelStyles, { padding: 5, pointerEvents: 'none' }),
		flyoutStyle: {
			stroke:        grey,
			strokeWidth:   1,
			fill:          lightGrey,
			pointerEvents: 'none'
		},
		cornerRadius:  5,
		pointerLength: 10
	},
};
