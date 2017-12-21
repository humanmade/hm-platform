import React from 'react';

import { storiesOf } from '@storybook/react';

import BandwidthUsage from '../components/Cloud/Bandwidth-Usage'
import PageGenerationTime from '../components/Cloud/Page-Generation-Time'
import PullRequests from '../components/Cloud/Pull-Requests'
import { DashboardAdminDecorator } from './decorators';

import {
	bandwidthUsage,
	pullRequests,
	responseTimeHistory,
} from './cloud-data-stubs';

/**
 * Stories for HM-Cloud UI components.
 */
storiesOf( 'Cloud', module )
	.addDecorator( DashboardAdminDecorator )
	.add( 'Bandwidth Usage', () => <BandwidthUsage usageHistory={ bandwidthUsage } /> )
	.add( 'Page Generation Time', () => <PageGenerationTime responseTimeHistory={ responseTimeHistory } /> )
	.add( 'Pull Requests', () => <PullRequests items={ pullRequests } /> );
