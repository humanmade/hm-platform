import React from 'react';
import AdminPortal from '../portal';

class IframeLink extends React.Component {

	constructor( props ) {
		super( props );
		this.state = {
			show: false,
		};
	}

	onShow( event ) {
		event.preventDefault();
		this.setState( { show: true } );
	}

	onHide( event ) {
		event.preventDefault();
		this.setState( { show: false } );
	}

	render() {
		const src = this.props.src;

		if ( ! src ) {
			return this.props.children;
		}

		const Open  = <a className="hm-iframe-open" key="open" href={src} onClick={e => this.onShow(e)}>{ this.props.children }</a>;
		const Close = <a className="hm-iframe-close btn" key="close" href={src} onClick={e => this.onHide(e)}>&times; Close</a>;

		if ( ! this.state.show ) {
			return Open;
		}

		return [
			Open,
			<AdminPortal key="iframe" id="hm-platform-iframe-modal">
				{ Close }
				<iframe src={src + '?admin-request'} title={this.props.title || ''} width="100%" height="100%" />
			</AdminPortal>
		];
	}

}

export default IframeLink;
