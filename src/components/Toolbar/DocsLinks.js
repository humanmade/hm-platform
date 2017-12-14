/*global HM*/
import React from 'react';
import withFetch from '../../utils/withFetch';
import { getDocsForURL } from '../../utils';
import Spinner from '../Spinner';
import IframeLink from '../IframeLink';

export class DocsLinks extends React.Component {
	render() {
		if ( this.props.loading ) {
			return <ul className="ab-submenu ab-sub-secondary">
				<li className="ab-submenu-header">Documentation</li>
				<li><Spinner /></li>
			</ul>;
		}

		if ( ! this.props.data || ! this.props.data.results ) {
			// @todo flag an error state, error boundary in outer app? modal?
			return null;
		}

		const docs = getDocsForURL( this.props.data.results );

		if ( ! docs.length ) {
			return null;
		}

		return <ul className="ab-submenu ab-sub-secondary hm-docs-links">
			<li className="ab-submenu-header">Documentation</li>
			{ docs.map( doc => <li key={doc.id}>
				<IframeLink
					src={doc.link} title={`${doc.parent} ${doc.title}`}
					show={this.state && this.state.active === doc.id}
					onOpen={() => this.setState({ active: doc.id })}
					onClose={() => this.setState({ active: null })}
				>
					{doc.parent} {doc.title}
				</IframeLink>
			</li> ) }
		</ul>;
	}
}

const EnhancedDocsLinks = withFetch(
	`${HM.EnterpriseKit.DocsURL}/wp-json/docs/v1/config`
)( DocsLinks );

export default EnhancedDocsLinks;
