/*global HM*/
import React, { Component } from 'react';
import logo from '../assets/logo-small-red.svg';

class Toolbar extends Component {
	render() {
		return [
			<a key="link" className="ab-item" href={ HM.EnterpriseKit.AdminURL + '#/' }>
				<img className="hm-logo-small" src={logo} alt="Human Made" />
				{ ' ' }
				Quick links
			</a>,
			<div key="submenu" className="ab-sub-wrapper">
				<ul className="ab-submenu">
					<li><a className="ab-item">Environment: <strong>{ HM.Environment }</strong></a></li>
					<li><a href={ HM.EnterpriseKit.AdminURL + '#/support' } className="ab-item">Get Support</a></li>
				</ul>
			</div>
		];
	}
}

export default Toolbar;
