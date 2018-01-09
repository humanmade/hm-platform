import { configure } from '@storybook/react';

// Import WordPress admin-side styles.
import '../../../wordpress/wp-admin/css/admin-menu.css';
import '../../../wordpress/wp-admin/css/common.css';
import '../../../wordpress/wp-admin/css/dashboard.css';
import '../../../wordpress/wp-admin/css/forms.css';
import '../../../wordpress/wp-admin/css/widgets.css';
import '../../../wordpress/wp-admin/css/wp-admin.css';

// Our Application's CSS.
import '../src/index.css';

const req = require.context('../src/stories', true, /\.stories\.js$/)

function loadStories() {
	req.keys().forEach( ( filename ) => req( filename ) )
}

configure( loadStories, module );
