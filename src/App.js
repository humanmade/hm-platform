import React, { Component } from 'react';
import Main from './components/Main';
import Toolbar from './components/Toolbar';
import AdminPortal from './portal';

class App extends Component {
  render() {
    return [
      <AdminPortal key="main" target="hm-platform">
        <Main/>
      </AdminPortal>,
      <AdminPortal key="toolbar" target="wp-admin-bar-hm-platform-toolbar-ui">
        <Toolbar />
      </AdminPortal>,
    ];
  }
}

export default App;
