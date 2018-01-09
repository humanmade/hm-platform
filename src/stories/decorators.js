import React from 'react';

/**
 * Decorator to replicate the insane WP-admin dashboard class/ID structure.
 *
 * @param storyFn
 */
export const DashboardAdminDecorator = storyFn => (
	<div className="wp-admin" id="wpbody-content">
		<div className="metabox-holder" id="dashboard-widgets" style={ { padding: 20 } }>
			{ storyFn() }
		</div>
	</div>
);
