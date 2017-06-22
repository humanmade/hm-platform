## Changelog

### 1.2.0

- Include common AWS SDK to avoid potential loading issues
- Update wordpress-pecel-memcached-object-cache
	- Avoid error with clean cache
	- Return correct value from `wp_load_alloptions()`
	- Avoid undefined variable error
- Update aws-ses-wp-mail
	- Update AWS SDK to v3
	- Avoid warning when domain is not set
	- Avoid warning with `trigger_error()` call
- Update S3 Uploads
	- Avoid loading error with multiple AWS SDK instances

### 1.1.3

- Update S3 Uploads to 2.0.0-beta3 which fixed an issue with regenerating thumbs.


### 1.1.2

- Update Cavalcade to include fix for failed jobs never recovering.
