## Running project
 - docker-compose up
 
## FAQ
 - If get the error ```Permission Denied Error``` . Please run this command
   - `docker exec -it app-aspire chown -R www-data:www-data /var/www/`
