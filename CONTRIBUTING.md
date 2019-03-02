# How to contribute

If you want to get involved in the WordPress Mautic extension, here are the
instruction that allow you to work with the code.

## Getting Started

You must fork the project on your own Github account and download the plugin code on your machine using git :

```bash
git checkout git@github.com:[YOUR USERNAME HERE]/mautic-wordpress.git
```

After that you must keep your repository in sync with the root one, under Mautic organisation.
To do that, you can add the `mautic/mautic-wordpress` remote to your own repository :

```bash
git remote add root git@github.com:mautic/mautic-wordpress.git
```

Be warned, you can't push modifications directly on that repository, you must
work on your own fork and create a pull-request when something requires a review.

When you want to synchronize your project with the Mautic root, use the following commands :

```bash
# Retrieve root master branch content and merge it in your current branch
git pull root master
```

## Environment

To work with the plugin code, you'll require a WordPress development version on your machine.
For the details, you can take a look in the `.travis-ci.yml` file but globally you must :

```bash
# Clone WordPress repository on your machine
git clone git@github.com:WordPress/WordPress.git

# if already cloned, you must retrieve latest changes
git fetch

# If you want to test on a specific WP version, you can choose it with Git
git checkout 5.1 # Can be 4.3, 5.0.1, ... To test on latest changes, use master
```

After retrieving the WordPress code, you must create the test configuration file :

```bash
# Cd's to your WordPress git clone
cp wp-tests-config-sample.php wp-tests-config.php

# Edit the file content to match your current env

# define( 'DB_NAME', 'wordpress_test' );
# define( 'DB_USER', 'root' );
# define( 'DB_PASSWORD', 'stephane' );
# define( 'DB_HOST', 'localhost' );

# Also, don't forget to add
# define('SCRIPT_DEBUG', false);
# At the end of that file.
```

Those steps are a bit complicated but are mandatory to be able to run the test suite.
After those edits, you must create a database on the configured MySQL server. Be warned,
the database will be created / dropped every time the test suite is run, don't use one
which is real for you.

## Quality insurance

### Unit tests

PHPUnit is used write and run unit tests. They are stored in the `tests` folder.

To run them, you must use this command :

```bash
WP_DEVELOP_DIR="path/to/your/wordpress/clone" make test
```

### Code sniffing

Since WordPress use PHP_CodeSniffer to apply syntax rule on its own code base,
we chose to use the same rule in Mautic plugin.

```bash
make code-sniffer
```

### Continuous integration

The code is validated after each pull request or `git push` on a branch. The code
sniffing and the unit test are executed to ensure no regression is introduced.

If you submit a patch, it must pass those tests to be accepted. If you require some
help, ping one of the maintainers in your PR comments.
