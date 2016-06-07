Bitbucket
===========
*Simple Bitbucket integration for Phproject*

### Installation
Clone the repo into the app/plugins directory of an existing Phproject installation. Ensure the web server has write access to the bitbucket directory after cloning.

The plugin will automatically generate a secure key for Bitbucket to send POST requests to, and will show the complete hook URL on the next page view. To see the hook URL again later, log in as an administrator user and go to Administration > Plugins > Bitbucket - Details.

Follow [Atlassian's guide](https://confluence.atlassian.com/display/BITBUCKET/POST+hook+management) to configure your plugin's connection to Bitbucket.

#### Advanced Setup

By default, commits that don't have a matching user will use user ID 1 as their author when adding comments and updates. You can override that value in the `config` table by setting `site.plugins.bitbucket.default_user_id` to any valid user ID.

In addition, you can create a `usermap.ini` file in your `app/plugin/bitbucket/` directory mapping alternate email addresses to their correct emails in your Phproject database:

```ini
alternate@example.org = primary@example.com
```
