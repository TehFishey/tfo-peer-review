## TFO Peer Review

tfo-peer-review is a single-page fansite or "click site" webapp which interfaces with the Final Outpost adoptables game at <a href="https://finaloutpost.net/">FinalOutpost.net</a>. It is intended to provide users with a streamlined interface for quickly interacting with each other's creatures in large numbers. This webapp is currently being hosted at <a href="https://TFOPeerReview.click">TFOPeerReview.click</a>

## Requirements/Frameworks

tfo-peer-review features a React.js-based frontend and a PHP/MariaDB-based backend. It is intended to be run off a LAMP stack, but should work on any webserver which can serve PHP files and connect to an SQL database.

Requirements:
- npm 6.14
- PHP 7.4
- MariaDB 10.5
- a webserver capable of serving PHP files, such as XAMPP (for local development/testing) or Apache2 (for server deployment)
- a TFO API key tied to the appropriate IP address

## Getting Started

Clone the project and install frontend dependencies with npm:
```shell
git clone https://github.com/TehFishey/tfo-peer-review.git
cd tfo-peer-review/app/
npm install
```

Setup database, user, and tables via prepared SQL script:
```
cd tfo-peer-review/
mysql -u <root> -p <rootpassword>
> SOURCE dbsetup.sql;
```
*Note: this will create the SQL database 'tfopeerreview_db', and add permissions to user 'tfopeerreview_user' with password 'password'.*

Rename `tfo-peer-review/server/config/config-default.php` to `config.php`. Open the file with your preferred text editor, and replace *YOUR-API-CODE-HERE* with your TFO API key.

Setup your chosen webserver to use `tfo-peer-review/server` as its webroot (or move the server files to your webroot). For development environments, ensure that your server's (or browser's) CORS settings are configured appropriately. (Exact steps vary by server; check your webserver documentation for details.)

Open `tfo-peer-review/app/public/config.js` with your preferred text editor, and replace *https://localhost/api/* with the path to your server's or virtual host's api directory.

## Running the Project:

Execute `npm start` in `tfo-peer-review/app` to open the frontend in development mode. The frontend should be able to communicate with the api hosted by your webserver, and your webserver should be able to communicate with TFO.

## Building the Project:

Execute `npm run build` in `tfo-peer-review/app` to execute a build-task. Frontend files will be built to the `tfo-peer-review/app/build` directory. These may then be copied to your webroot (`tfo-peer-review/server` for example) for server-side hosting and deployment.

## Deployment Notes

In addition to hosting the above files and database, deployment servers will want to run two prepared cron scripts at regular intervals. The script at `tfo-peer-review/server/cron/cron.php` conducts regular clean-up and upkeep of the database tables, and should be run frequently (between every 5-15 minutes). The script at `tfo-peer-review/server/cron/cron-weekly.php` conducts log compilation tasks, and should be run weekly. 

**IMPORTANT NOTE***: Ensure that the scripts in the cron directory are *not* accessible externally once deployed, either by configuring your webserver to block access to them or moving them out of the webroot entirely. If moved out of webroot, be sure to update the scripts' include paths to account for any changes.

## Attributions
- React frontend bootstrapped with <a href="https://github.com/facebook/create-react-app">create-react-app</a>
- PHP Rate-Limiter adapted from code by <a href="https://ryanbritton.com/2016/11/rate-limiting-in-php-with-token-bucket/">Ryan Britton</a>
- RPC API loosely based on tutorial by <a href="https://www.codeofaninja.com/2017/02/create-simple-rest-api-in-php.html">codeofaninja.com</a> 
- Special thanks to Corteo for the creation of <a href="https://github.com/facebook/create-react-app">The Final Outpost</a> and its associated API.