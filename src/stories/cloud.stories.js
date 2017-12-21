import React from 'react';

import { storiesOf } from '@storybook/react';

import BandwidthUsage from '../components/Cloud/Bandwidth-Usage'
import PullRequests from '../components/Cloud/Pull-Requests'
import { DashboardAdminDecorator } from './decorators';

import {
	bandwidthUsage,
	pullRequests
} from './cloud-data-stubs';

/**
 * Stories for HM-Cloud UI components.
 */
storiesOf( 'Cloud', module )
	.addDecorator( DashboardAdminDecorator )
	.add( 'Bandwidth Usage', () => <BandwidthUsage usageHistory={ bandwidthUsage } /> )
	.add( 'Pull Requests', () => <PullRequests items={ pullRequests } /> );
