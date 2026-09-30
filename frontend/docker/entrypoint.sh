#!/bin/sh
set -eu

cd /app

npm install

exec npm run serve -- --host 0.0.0.0 --port 8080
