#!/bin/sh

while true
do
  if ! pgrep -x php > /dev/null; then
    exit 1
  fi
  echo "PHP process is still running. Sleeping for 5 seconds."
  sleep 5
done
