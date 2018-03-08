## Changelog

### 1.2.3

- Update S3 Uploads
	- Fix deleting original attachments.
	- Improve memory usage when copying large files.

### 1.2.2

- Update batcache
	- Merge with upstream
	- Update deprecated constructor
- Update luidicrousdb
	- Fixes compatibilty with 4.6
	- Updates to `query()` method
	- Major versioh bump to 4.x
	- Fixes WordPress 4.8.3 SQLi vulnerability
- Update wordpress-pecel-memcached-object-cache
	- Reintroduce warning with `trigger_error()` call
	- Ensure `alloptionskeys` doesn't return false from cache
- Update S3 Uploads
	- Added ability to disable rewriting of file upload url
	- Added ability to set S3 object permissions
	- Ignore copy of a file path if it is a directory
- Update Tachyon
	- Add hook to be able to save image dimensions

### 1.2.1

- Update AWS SES plugin to latest version
    - Adds a filter to get more logging details
- Send logs from SES and Cavalcade to CloudWatch
- Add ElasticSearch support and plugin

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
