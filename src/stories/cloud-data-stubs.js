/**
 *
 * @type {Array}
 */
export const pullRequests = [
	{
		date:       '2017-10-17T18:22:46Z',
		id:         31,
		link:       'https://github.com/humanmade/hm-stack/pull/31',
		status:     'PENDING',
		statusText: 'In Progress',
		title:      'SEO Plugin',
	},
	{
		date:       '2017-10-11T18:22:46Z',
		id:         12,
		link:       'https://github.com/humanmade/hm-stack/pull/12',
		status:     'PENDING',
		statusText: 'In Progress',
		title:      'Fix load time on subdomain',
	},
	{
		date:       '2017-10-06T18:22:46Z',
		id:         61,
		link:       'https://github.com/humanmade/hm-stack/pull/61',
		status:     'CHANGES_REQUESTED',
		statusText: 'Waiting on SC',
		title:      'Implement new legal requirements',
	},
	{
		date:       '2017-10-01T18:22:46Z',
		id:         40,
		link:       'https://github.com/humanmade/hm-stack/pull/40',
		status:     'DISMISSED',
		statusText: 'Icebox',
		title:      'Change EditFlow setup',
	},
	{
		date:       '2017-09-20T18:22:46Z',
		id:         33,
		link:       'https://github.com/humanmade/hm-stack/pull/33',
		status:     'COMMENTED',
		statusText: 'Waiting on SC',
		title:      'Add colors on picker',
	}
];

/**
 * Data about the current git status of an environment.
 *
 * @type {Object}
 */
export const gitData = {
	branch: 'master',
	commit: {
		date:         '2017-09-20T18:22:46Z',
		description: 'Merge pull request #213 from humanmade/update-build-tools\\n\\nUpdate build tools',
		rev:         '055d9ce8d6676aa7880b7060baf56c24c9bae9d0',
		status:      'active',
	},
}

/**
 * Data about the current environment status.
 *
 * @type {Object}
 */
export const environmentData = {
	php:           '7.1.2',
	mySql:         '10.2',
	elasticsearch: '12',
}
