import React from 'react';

import { storiesOf } from '@storybook/react';

import PullRequests from '../components/Cloud/Pull-Requests'
import { DashboardAdminDecorator } from './decorators';

import { pullRequests } from './cloud-data-stubs';

/**
 * Stories for HM-Cloud UI components.
 */
storiesOf( 'Cloud', module )
	.addDecorator( DashboardAdminDecorator )
	.add( 'Pull Requests', () => <PullRequests items={ pullRequests } /> );