# Composer version lock

## Why this plugin?

When working with many people in the same codebases, it sometimes is quite a hassle to resolve merge conflicts on the Composer lockfile.

Even when the only changes between branches is that two different packages have been added, the diff on the ```composer.lock``` file can still be several hundred lines. When the only expected changes are the hash and the info about the packages (and maybe some changed dependency versions), differences in composer versions between developers are often the culprit. Sometimes these are big differences, like [support for a Funding section](https://github.com/composer/composer/releases/tag/1.10.0), other times the order of some keys is just changed form one Composer version to the next.

This plugin makes it possible to share the required composer version in the ```composer.json``` file. 

## Getting started

to include this package, simply run;

```sh
composer require prinsfrank/composer-version-lock
```

To set the required composer version for your project, run;

```sh
composer config extra.composer-version {VERSION_CONSTRAINT}
```

Any [Composer constraint](https://getcomposer.org/doc/articles/versions.md#versions-and-constraints) can be used as the ```composer/semver``` package is used to determine if the current composer version satisfies the version constraint.

To get your current version, run ```composer --version```.

> **Note:** When no composer version is set, an error is displayed with instructions on how to enforce your current Composer version.

## How it works

### Commands that change the lock file 
Some of the composer commands change the ```composer.lock```:
- update
- remove
- require

Whenever a developer executes any of the above commands with a Composer version that doesn't satisfy the version constraint, they will get the following error, with the command aborting:

```sh
This package requires composer version 1.10.15, Currently version is 1.10.14
To change to the required version, run;

    composer self-update 1.10.15

```
> **Note:** When the version constraint is not an exact version or a next significant operator it is not possible to deduce a matching version so ```composer self-update {version}``` is displayed instead.

### Commands that don't change the lock file

When the developer executes any other command without a satisfiable version, they will just get a warning, with Composer continuing:

```sh
This package expects composer version 1.10.15
-> Continuing as the current action isn't modifying the lock file.
```

### Information when using the correct version

When the developer is using a Composer version that satisfies the constraint, the following message will be displayed:
```sh
Your composer version satisfies the required version set by the current package 
```