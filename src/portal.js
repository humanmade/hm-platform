import { Component } from 'react';
import ReactDOM from 'react-dom';

class AdminPortal extends Component {
	componentWillMount() {
		this.domEl = document.getElementById( this.props.id );

		if ( this.domEl ) {
			this.domEl.innerHTML = '';
		}
	}

	render() {
		return this.domEl
			? ReactDOM.createPortal( this.props.children, this.domEl )
			: null;
	}
}

export default AdminPortal;
