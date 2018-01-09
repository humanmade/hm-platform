import React from 'react';

import { storiesOf } from '@storybook/react';

import AlertsLog from '../components/Cloud/Alerts-Log'
import EnvironmentData from '../components/Cloud/Environment-Data'
import PullRequests from '../components/Cloud/Pull-Requests'
import { DashboardAdminDecorator } from './decorators';

import {
	alerts,
	gitData,
	environmentData,
	pullRequests
} from './cloud-data-stubs';

/**
 * Stories for HM-Cloud UI components.
 */
storiesOf( 'Cloud', module )
	.addDecorator( DashboardAdminDecorator )
	.add( 'Activity Log', () => <AlertsLog items={ alerts } /> )
	.add( 'Application Data', () => <EnvironmentData gitData={ gitData } environmentData={ environmentData } /> )
	.add( 'Pull Requests', () => <PullRequests items={ pullRequests } /> );