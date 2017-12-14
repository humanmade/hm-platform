import React, { Component } from 'react';
import { HashRouter, Route } from 'react-router-dom';
import AdminPortal from '../portal';
import Menu from './Menu';
import Header from './Header';

class Main extends Component {

	render() {
		return <HashRouter>
			<div id="hm-enterprise-kit-ui">
				<AdminPortal target="toplevel_page_hm-platform">
					<Menu />
				</AdminPortal>
				<Header />
				<Route exact path="/" render={() => <h2>Dashboard</h2>} />
				<Route path="/features" render={() => <h2>Features</h2>} />
				<Route path="/stats" render={() => <h2>Stats</h2>} />
			</div>
		</HashRouter>;
	}
}

export default Main;
