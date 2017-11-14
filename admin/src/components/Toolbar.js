/*global HM*/
import React, { Component } from 'react';
import logo from '../assets/logo-small-red.svg';

class Toolbar extends Component {
	render() {
		return [
			<a key="link" className="ab-item" href={HM.EnterpriseKit.AdminURL + '#/'}>
				<img className="hm-logo-small" src={logo} alt="Human Made" />
				{ ' ' }
				Platform:
				{ ' ' }
				<strong>{ HM.Environment }</strong>
			</a>,
			<div key="submenu" className="ab-sub-wrapper">
				<ul className="ab-submenu">
					<li><span className="ab-item">Get Support</span></li>
				</ul>
			</div>
		];
	}
}

export default Toolbar;
