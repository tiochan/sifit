#!/bin/sh

# rm sifit.tgz
# tar czvf sifit.tgz ../../../sifit

if [ $# -lt 1 ]; then
	echo "Missing parameter DB_HOST"
	echo "Usage: $0 <db_host>"
	echo "Example"
	echo "$0 192.168.1.20"
	exit 1
fi

DB_HOST=$1

# --force-rm
docker-compose build --build-arg DB_HOST="${DB_HOST}"

