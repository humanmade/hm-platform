import React from 'react';

import { storiesOf } from '@storybook/react';

import DashboardBlock from '../components/Dashboard-Block'

/**
 * Stories for Admin-side generic components.
 */
storiesOf( 'Admin', module )
	.add( 'Dashboard Block', () => <div id="dashboard-widgets"><DashboardBlock title={ 'My Little Dashboard Block' } /></div> );