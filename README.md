Bitbucket
===========
*Simple Bitbucket integration for Phproject*

### Installation
Clone the repo into the app/plugins directory of an existing Phproject installation. Ensure the web server has write access to the bitbucket directory after cloning.

The plugin will automatically generate a secure key for Bitbucket to send POST requests to, and will show the complete hook URL on the next page view. To see the hook URL again later, log in as an administrator user and go to Administration > Plugins > Bitbucket - Details.

Follow [Atlassian's guide](https://confluence.atlassian.com/display/BITBUCKET/POST+hook+management) to configure your plugin's connection to Bitbucket.
