#!/bin/sh

# Copy environment file to app directory
cp -- /supervisor.env /app/.env

# Run queue worker
exec php /app/artisan queue:work redis --queue=otp,default --sleep=3 --tries=3
