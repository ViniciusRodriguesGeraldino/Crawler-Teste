#!/bin/bash
git pull origin main &&
php bin/console cache:clear --no-warmup --env=prod &&
php bin/console messenger:stop-workers
