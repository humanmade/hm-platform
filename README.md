<table width="100%">
	<tr>
		<td align="left" width="70">
			<strong>HM Platform</strong><br />
			Shared library for sites on the Human Made Platform.
		</td>
		<td align="right" width="20%">
			Version 2.0.0
		</td>
	</tr>
	<tr>
		<td>
			A <strong><a href="https://hmn.md/">Human Made</a></strong> project. Maintained by @joehoyle.
		</td>
		<td align="center">
			<img src="https://hmn.md/content/themes/hmnmd/assets/images/hm-logo.svg" width="100" />
		</td>
	</tr>
</table>

## Human Made Platform

This is the Human Made Platform library that should be included on
all sites that are being hosted by Human Made. This includes some plugins that
are required for the hosting platform, however these _can_ be disabled if alternative
versions of plugins are installed.

See the [platform](http://engineering.hmn.md/platform/plugins/) page for details on the
libraries that are included.

### Install Instructions

1. Add this repository to the content directory of the WordPress install, we recommend you add
it as a git submodule.
1. Require the `hm-platform/load.php` file from your `wp-config.php`.

### Configuring Activated Modules

To optionally enable or disable any of the platform modules you can create a file called `hm.json`
and place it in your root directory or content directory. You can also override the settings depending
on your environment, for example `hm.local.json` will override settings when working locally.

#### Legacy Configuration 

Previously you could define the `global $hm_platform` variable. This is still supported but we recommend
updating to use `hm.json`.

You can set any of the following to false:

```php
global $hm_platform;
$hm_platform = array_merge( $hm_platform, array(
	's3-uploads'      => true,
	'aws-ses-wp-mail' => true,
	'tachyon'         => true,
	'cavalcade'       => true,
	'batcache'        => true,
	'memcached'       => true,
	'ludicrousdb'     => true,
) );
```

### Search Engine Indexing

By default, hm-platform will force disable indexing by search engines on any non-production environment. If you wish to disable this feature, add the following to your config:

```php
define( 'HM_DISABLE_INDEXING', false );
```

This will fall back to whatever the `blog_public` option value is in the database.
