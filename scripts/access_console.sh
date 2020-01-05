#!/bin/sh

ps=$(docker ps -q)
docker exec -it $ps bash
