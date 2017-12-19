import { configure } from '@storybook/react';

// Our Application's CSS.
import '../src/index.css';

const req = require.context('../src/stories', true, /\.stories\.js$/)

function loadStories() {
	req.keys().forEach( ( filename ) => req( filename ) )
}

configure( loadStories, module );
