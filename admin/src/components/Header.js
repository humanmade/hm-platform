/*global HM*/
import React, { Component } from 'react';
import logo from '../assets/logo-red.svg';
import './Header.css';

class Header extends Component {

	render() {
		return <header className="hm-ek-Header">
			<h1>
				<img className="hm-logo-large" src={logo} alt="Human Made" />
				Enterprise Kit
				{ ' ' }
				<small className="hm-ek-Header-Version">v{ HM.EnterpriseKit.Version }</small>
			</h1>
		</header>;
	}
}

export default Header;
