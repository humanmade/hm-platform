/**
 * Example bandwidth usage for a Cloud site.
 *
 * @type {Array}
 */
export const bandwidthUsage = [
	{ date: '2017-09-19T00:00:00Z', usage: 1000000000000 },
	{ date: '2017-09-20T00:00:00Z', usage: 1500000000000 },
	{ date: '2017-09-21T00:00:00Z', usage: 1250000000000 },
	{ date: '2017-09-22T00:00:00Z', usage: 750000000000 },
	{ date: '2017-09-23T00:00:00Z', usage: 1100000000000 },
	{ date: '2017-09-24T00:00:00Z', usage: 1200000000000 },
	{ date: '2017-09-25T00:00:00Z', usage: 1300000000000 },
	{ date: '2017-09-26T00:00:00Z', usage: 500000000000 },
	{ date: '2017-09-27T00:00:00Z', usage: 600000000000 },
	{ date: '2017-09-28T00:00:00Z', usage: 1100000000000 },
	{ date: '2017-09-29T00:00:00Z', usage: 1040000000000 },
	{ date: '2017-09-30T00:00:00Z', usage: 1150000000000 },
	{ date: '2017-10-01T00:00:00Z', usage: 900000000000 },
	{ date: '2017-10-02T00:00:00Z', usage: 800000000000 },
	{ date: '2017-10-03T00:00:00Z', usage: 950000000000 },
	{ date: '2017-10-04T00:00:00Z', usage: 1100000000000 },
	{ date: '2017-10-05T00:00:00Z', usage: 1050000000000 },
	{ date: '2017-10-06T00:00:00Z', usage: 1200000000000 },
	{ date: '2017-10-07T00:00:00Z', usage: 1040000000000 },
	{ date: '2017-10-08T00:00:00Z', usage: 1030000000000 },
	{ date: '2017-10-09T00:00:00Z', usage: 1020000000000 },
	{ date: '2017-10-10T00:00:00Z', usage: 950000000000 },
	{ date: '2017-10-11T00:00:00Z', usage: 800000000000 },
	{ date: '2017-10-12T00:00:00Z', usage: 1050000000000 },
	{ date: '2017-10-13T00:00:00Z', usage: 1080000000000 },
	{ date: '2017-10-14T00:00:00Z', usage: 1090000000000 },
	{ date: '2017-10-15T00:00:00Z', usage: 1110000000000 },
	{ date: '2017-10-16T00:00:00Z', usage: 1150000000000 },
	{ date: '2017-10-17T00:00:00Z', usage: 1000000000000 },
	{ date: '2017-10-18T00:00:00Z', usage: 1050000000000 },
]

/**
 *
 * @type {Array}
 */
export const alerts = [
	{
		date:    '2017-10-17T18:22:46Z',
		id:      1,
		level:   'error',
		message: 'Scheduled maintenance is tomorrow Oct. 18, from 6pm to 7pm GMT.',
	},
	{
		date:    '2017-10-11T18:22:46Z',
		id:      2,
		level:   'success',
		message: 'System maintenance successfully completed in 45 minutes. No issues to report.',
	},
	{
		date:    '2017-10-06T18:22:46Z',
		id:      3,
		level:   'error',
		message: 'Scheduled maintenance is starting in 30 minutes.',
	},
	{
		date:    '2017-10-01T18:22:46Z',
		id:      4,
		level:   'success',
		message: 'Pull request eatcake #hm2017 has been pushed to production',
	},
	{
		date:    '2017-09-20T18:22:46Z',
		id:      5,
		level:   'success',
		message: 'System maintenance successfully completed in 45 minutes. No issues to report.',
	}
];

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

/**
 * Average daily server response times for a site.
 *
 * @type {Array}
 */
export const responseTimeHistory = [
	{ date: '2017-09-19T00:00:00Z', time: 100 },
	{ date: '2017-09-20T00:00:00Z', time: 95 },
	{ date: '2017-09-21T00:00:00Z', time: 105 },
	{ date: '2017-09-22T00:00:00Z', time: 102 },
	{ date: '2017-09-23T00:00:00Z', time: 103 },
	{ date: '2017-09-24T00:00:00Z', time: 103 },
	{ date: '2017-09-25T00:00:00Z', time: 115 },
	{ date: '2017-09-26T00:00:00Z', time: 90 },
	{ date: '2017-09-27T00:00:00Z', time: 150 },
	{ date: '2017-09-28T00:00:00Z', time: 105 },
	{ date: '2017-09-29T00:00:00Z', time: 103 },
	{ date: '2017-09-30T00:00:00Z', time: 104 },
	{ date: '2017-10-01T00:00:00Z', time: 97 },
	{ date: '2017-10-02T00:00:00Z', time: 90 },
	{ date: '2017-10-03T00:00:00Z', time: 85 },
	{ date: '2017-10-04T00:00:00Z', time: 87 },
	{ date: '2017-10-05T00:00:00Z', time: 84 },
	{ date: '2017-10-06T00:00:00Z', time: 80 },
	{ date: '2017-10-07T00:00:00Z', time: 85 },
	{ date: '2017-10-08T00:00:00Z', time: 87 },
	{ date: '2017-10-09T00:00:00Z', time: 105 },
	{ date: '2017-10-10T00:00:00Z', time: 85 },
	{ date: '2017-10-11T00:00:00Z', time: 80 },
	{ date: '2017-10-12T00:00:00Z', time: 83 },
	{ date: '2017-10-13T00:00:00Z', time: 86 },
	{ date: '2017-10-14T00:00:00Z', time: 93 },
	{ date: '2017-10-15T00:00:00Z', time: 75 },
	{ date: '2017-10-16T00:00:00Z', time: 80 },
	{ date: '2017-10-17T00:00:00Z', time: 77 },
	{ date: '2017-10-18T00:00:00Z', time: 83 },
];
