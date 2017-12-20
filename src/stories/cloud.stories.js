import React from 'react';

import { storiesOf } from '@storybook/react';

import DashboardBlock from '../components/Dashboard-Block'

/**
 * Stories for HM-Cloud UI components.
 */
storiesOf( 'Cloud', module )
	.add( 'Dashboard Block', () => <DashboardBlock title={ 'My Little Dashboard Block' } /> );