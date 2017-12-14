/*global HM*/
import React, { Component } from 'react';
import Logo from '../assets/logo-red.svg';

class Header extends Component {

	render() {
		return <header className="hm-ek-Header">
			<h1>
				<Logo className="hm-logo-large" title="Human Made" />
				Platform
				{ ' ' }
				<small className="hm-ek-Header-Version">v{ HM.EnterpriseKit.Version }</small>
			</h1>
		</header>;
	}
}

export default Header;
