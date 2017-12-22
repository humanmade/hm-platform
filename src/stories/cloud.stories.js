import React from 'react';

import { storiesOf } from '@storybook/react';

import AlertsLog from '../components/Cloud/Alerts-Log'
import PullRequests from '../components/Cloud/Pull-Requests'
import { DashboardAdminDecorator } from './decorators';

import {
	alerts,
	pullRequests,
} from './cloud-data-stubs';

/**
 * Stories for HM-Cloud UI components.
 */
storiesOf( 'Cloud', module )
	.addDecorator( DashboardAdminDecorator )
	.add( 'Activity Log', () => <AlertsLog items={ alerts } /> )
	.add( 'Pull Requests', () => <PullRequests items={ pullRequests } /> );