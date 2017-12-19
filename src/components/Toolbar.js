/*global HM*/
import React, { Component } from 'react';
import Logo from '../assets/logo-small-red.svg';
import DocsIndicator from './Toolbar/DocsIndicator';
import DocsLinks from './Toolbar/DocsLinks';

class Toolbar extends Component {
	render() {
		return [
			<a key="link" className="ab-item" href={ HM.AdminURL + '#/' }>
				<Logo className="hm-logo-small" title="Human Made" />
				{ ' ' }
				Quick links
				{ ' ' }
				<DocsIndicator/>
			</a>,
			<div key="submenu" className="ab-sub-wrapper">
				<ul className="ab-submenu">
					<li><a className="ab-item">Environment: <strong>{ HM.Environment }</strong></a></li>
					<li><a href={ HM.AdminURL + '#/support' } className="ab-item">Get Support</a></li>
				</ul>
				<DocsLinks/>
			</div>
		];
	}
}

export default Toolbar;
