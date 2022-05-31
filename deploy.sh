#!/bin/bash
git pull origin main  --allow-unrelated-histories  &&
php bin/console cache:clear --no-warmup --env=prod &&
php bin/console messenger:stop-workers
