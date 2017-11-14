import React, { Component } from 'react';
import './App.css';
import Main from './components/Main';
import Toolbar from './components/Toolbar';
import AdminPortal from './portal';

class App extends Component {
  render() {
    return [
      <AdminPortal key="main" id="hm-enterprise-kit">
        <Main/>
      </AdminPortal>,
      <AdminPortal key="toolbar" id="wp-admin-bar-hm-platform-toolbar-ui">
        <Toolbar />
      </AdminPortal>,
    ];
  }
}

export default App;
