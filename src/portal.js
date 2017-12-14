import { Component } from 'react';
import ReactDOM from 'react-dom';

class AdminPortal extends Component {

	setDomElement( props ) {
		if ( props.target ) {
			this.domEl = document.getElementById( props.target );
		} else {
			this.domEl = document.createElement( 'div' );
			document.body.appendChild( this.domEl );
		}

		if ( this.domEl ) {
			this.domEl.innerHTML = '';
		}
	}

	componentWillMount() {
		this.setDomElement( this.props );
	}

	componentDidMount() {
		this.props.onLoad && this.props.onLoad();
	}

	componentWillUnmount() {
		document.body.removeChild( this.domEl );
		this.props.onUnload && this.props.onUnload();
	}

	shouldComponentUpdate( nextProps ) {
		return (
			nextProps.target !== this.props.target
		) || (
			nextProps.id !== this.props.id
		);
	}

	componentWillUpdate( nextProps ) {
		this.setDomElement( nextProps );

		// Support an unload method call in the props.
		this.props.onUnload && this.props.onUnload();
	}

	componentDidUpdate() {
		this.props.onLoad && this.props.onLoad();
	}

	render() {
		return this.domEl
			? ReactDOM.createPortal( this.props.children, this.domEl )
			: null;
	}
}

export default AdminPortal;
