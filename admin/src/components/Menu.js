import React, { Component } from 'react';
import { NavLink } from 'react-router-dom';
import logo from '../assets/logo-small-red.svg';

class Menu extends Component {
	render() {
		return [
			<NavLink
				key="main-link"
				to="/"
				className="menu-top toplevel_page_hm-enterprise-kit"
			>
				<div className="wp-menu-arrow"><div /></div>
				<div className="wp-menu-image dashicons-before">
					<img className="hm-logo-small" src={logo} alt="Human Made" />
				</div>
				<div className="wp-menu-name">Enterprise Kit</div>
			</NavLink>,
			<ul key="submenu" className="wp-submenu wp-submenu-wrap">
				<li><NavLink to="/">Dashboard</NavLink></li>
				<li><NavLink to="/features">Features</NavLink></li>
				<li><NavLink to="/stats">Stats</NavLink></li>
			</ul>
		];
	}
}

export default Menu;
