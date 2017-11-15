# We ❤ Contributions!

HM Platform is the core of the value we add to WordPress for our customers.

## What you need to know

Making updates to the Platform touches on many aspects of what makes it great.
It's not just code you need to update, but changelogs and documentation too.

Here's the workflow for making changes:

### 1. Update the changelog

You should do this with every change no matter how small.

Note down what you changed, referencing any particular files. If there are a lot
of changes to a specific feature you can reference the changelog of that repo and
just provide a summary. 

### 2. Version updates

HM Platform uses semantic versioning: [semver](http://semver.org/).

In short that is `MAJOR.MINOR.PATCH`.

If you have made changes that don't require any updates to the documentation itself
such as bug fixes then increase the `PATCH` version.

If you have made changes that require an update to the documentation eg. with a new
version of a feature or a new feature then increase the `MINOR` version.

If the changeset is significant and the product owners are in agreement then you
may increase the `MAJOR` version.

### 3. Documentation updates

Make sure any new code is documented on the [docs repository](https://github.com/humanmade/platform-docs)
before creating a new release.

These changes will include things like:

* New filters or action hooks
* New public classes, methods or functions
* Updated or new example code for carrying out common tasks
* Updated or new user guides for UI changes

### 4. Notifications

Depending on the significance of the changes you want to write or suggest a blog
post or newsletter update for customers.

Changelog updates will be linked to in the WordPress dashboard so patch updates
don't require any further work. 
