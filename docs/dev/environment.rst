Development Environment Setup
=============================

Before you begin developing, ensure you have **Docker** running on your system.

Getting Started
---------------

First, clone the WordPress development repository:

::

  git clone https://github.com/WordPress/wordpress-develop.git

Change into the cloned directory:

::

  cd wordpress-develop

Initial Setup
-------------

Run the following commands to set up your environment:

::

  npm install
  npm run build:dev
  npm run env:start
  npm run env:install

These commands will:

- Install all Node.js dependencies.
- Build development versions of WordPress assets.
- Start the local Docker containers for WordPress.
- Install WordPress into the Docker environment.

Useful Commands
---------------

After setup, you can use these commands to control your environment:

- ``npm run env:start`` — Start Docker containers.
- ``npm run env:stop`` — Stop Docker containers.
- ``npm run env:restart`` — Restart Docker containers.

Visit your local WordPress install at **http://localhost:8889**.
