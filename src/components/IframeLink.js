import React from 'react';
import AdminPortal from '../portal';

class IframeLink extends React.Component {

	constructor( props ) {
		super( props );
		this.state = {
			show:    false,
			loading: true,
		};
	}

	onOpen( event ) {
		event && event.preventDefault();
		this.setState( { show: true } );
		this.props.onOpen && this.props.onOpen();
	}

	onClose( event ) {
		event && event.preventDefault();
		this.setState( { show: false } );
		this.props.onClose && this.props.onClose();
	}

	componentDidUpdate() {
		if ( ! this.iframe || ! this.iframe.contentWindow ) {
			return;
		}

		this.iframe.contentWindow.onload = () => this.setState( { loading: false } );
		this.iframe.contentWindow.onunload = () => this.setState( { loading: true } );
	}

	render() {
		const src = this.props.src;

		if ( ! src ) {
			return this.props.children;
		}

		const Open  = <a className="hm-iframe-open" key="open" href={src} onClick={e => this.onOpen(e)}>{ this.props.children }</a>;
		const Close = <a className="hm-iframe-close btn" key="close" href={src} onClick={e => this.onClose(e)}>Close</a>;

		// Allow a show prop to override state.
		if ( typeof this.props.show !== 'undefined' ) {
			if ( ! this.props.show ) {
				return Open;
			}
		} else {
			if ( ! this.state.show ) {
				return Open;
			}
		}

		return [
			Open,
			<AdminPortal key="iframe" id={src} onUnload={() => this.setState( { loading: true } )}>
				<div className="hm-platform-modal">
					{ Close }
					<iframe
						//className={ this.state.loading ? 'hm-iframe-loading' : 'hm-iframe-loaded' }
						src={src + '?admin-request'}
						title={ this.props.title || '' }
						width="100%"
						height="100%"
						ref={iframe => this.iframe = iframe}
					/>
				</div>
			</AdminPortal>
		];
	}

}

IframeLink.instances = [];

export default IframeLink;
