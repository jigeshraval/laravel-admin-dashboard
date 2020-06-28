# AdLara Bootstrap

Laravel VueJS/Vuetify Powered admin, booting up required components that separates Admin and Front and provides rich tools such as Media Library

# What will this bootstraping do? (List of Functions) 

- Ready to go Laravel/Vue/Vuetify Admin
- Sample Blog, ready to extend or use
- Provides Multiple Authentication for the Front end and the Back end (Admin)
- Creates Separate Controllers Folders (AdminControllers and FrontControllers)
- Separate Route files, for admin -> admin.php and for front-end web.php 
- Separte views for admin and front end so that the application remains organized and flows well 
- Media Management: Manage static assets of your application through database. It provides boiler plate to record every upload in the database. Of course, it has Rich Media Library built with VueJs (https://vuejs.org) and Uppy.io (https://uppy.io/). 
- The media library reduces the time consuming tasks of creating uploading functionality and manage it and speed-up the other tasks 
- Image resizing (Powered by http://image.intervention.io/)

# Installation Steps 

1. composer require jigeshraval/laravel-admin-dashboard
2. Add "AdLara\Boot\BootstrapServiceProvider::class" in the providers array in config/app.php
3. Make sure database credentials are added in .env file and database is connected with the Laravel Application
4. Delete all the migrations from database/migrations folder
5. php artisan vendor:publish --tag=adlarafullsetup --force 
6. php artisan migrate
7. php artisan storage:link (For Media and Static Resources)
8. npm install
9. npm run watch (for the dev mode) or npm run production (if you're ready to deploy)
10. Now go to http://127.0.0.1:8000/admin and you will be able to see login screen, use jigesh@jigeshraval.com and password: adlaraera1 for your initial login

# This step is only if you want database seeding to be done
11. php artisan db:seed --class=PostSeeder

# Documentation

- https://jigeshraval.com/en/blog/laravel-admin-dashboard
