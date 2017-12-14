import React from 'react';
import withFetch from '../../utils/withFetch';

class DocsIndicator extends React.Component {

	render() {
		if ( this.props.loading ) {
			return <Spinner/>;
		}
	}

}

export DocsIndicator;

export default withFetch( `${HM.EnterpriseKit.DocsURL}wp-json/docs/v1/config` )( DocsIndicator );
