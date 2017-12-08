import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import Logo from '../assets/logo-small-red.svg';

class Menu extends Component {
	render() {
		return [
			<NavLink
				key="main-link"
				to="/"
				className="menu-top toplevel_page_hm-enterprise-kit"
				activeClassName="current"
			>
				<div className="wp-menu-arrow"><div /></div>
				<div className="wp-menu-image dashicons-before">
					<Logo className="hm-logo-small" title="Human Made Logo" />
				</div>
				<div className="wp-menu-name">Platform</div>
			</NavLink>,
			<ul key="submenu" className="wp-submenu wp-submenu-wrap">
				<li><NavLink exact to="/" activeClassName="current">Dashboard</NavLink></li>
				<li><NavLink to="/ek" activeClassName="current">Enterprise Kit</NavLink></li>
				<li><NavLink to="/stats" activeClassName="current">Stats</NavLink></li>
			</ul>
		];
	}
}

export default Menu;
