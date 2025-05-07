#!/bin/sh

# Copy docker environment variables so they are visible to cron
printenv | grep -v "no_proxy" >> /etc/environment

exec "$@"