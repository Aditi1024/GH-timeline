#!/bin/bash

# Path to your project directory
PROJECT_DIR="$(pwd)"
PHP_PATH="$(which php)"
CRON_FILE="/tmp/gh_cronjob"

# CRON job entry: run every 5 mins
echo "*/5 * * * * $PHP_PATH $PROJECT_DIR/cron.php" > $CRON_FILE

# Install the CRON job
crontab $CRON_FILE

echo "CRON job installed to run cron.php every 5 minutes."
