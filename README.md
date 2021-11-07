![iaNtheLoop logo](/resources/assets/iantheloop_README_logo.png)

## Description


This is a social network single page web application that I built mainly to advance my knowledge and understanding of creating an application that incorporates a frontend and a backend.

iaNtheLoop encompasses many of the features of a traditional social network application. After a user has created an account and logged in, they will be able to take advantage of the features that this application provides **including:**

 ✅ Follow/Following system </br>
 ✅ Profiles</br>
 ✅ Profile Walls</br>
 - Posts (text, picture, video)
 - Comments
 - Reply comments
 - Likes

 ✅ Newsfeed </br>
 ✅ Follow Suggestions </br>
 ✅ Customizable User Settings</br>
 - Delete Account
 - Change Password
 - Block certain users from seeing (profile, stories, messages)

 ✅ User Search </br>
 ✅ Notifications </br>
 ✅ Realtime Messaging </br>
 ✅ Stories </br>
 ✅ Reviews</br>


 The technologies that are currently used in this application are [**Laravel**](https://laravel.com/)  for the API and the JWT authentication mechanism, [**Vue JS**](https://vuejs.org/) (couples nicely with Laravel) and [**Sass**](https://sass-lang.com/) for the frontend , and [**mySQL**](https://www.mysql.com/) as the database. [**Vuex**](https://vuex.vuejs.org/) is being used to manage state on the front end because there was too much shared state that was needed in different parts of the application.

 So far the most challenging thing to implement was the **Follow/Follower** system. I initially over complicated things by not making a dedicated table to hold **following/followers**, instead stuffing them in a JSON column. While this worked, it did make for more work and complicated logic to keep everything in sync. Eventually I would like to refactor it because it will eventually introduce scaling problems.
<br>
<br>
 ### 🔮 Future features and implementations:
<br>

✏️ *Migrate Vue 2 to Vue 3* <br>
✏️ *Find a more maintained up-to-date JSON Web Token library* <br>
✏️ *Add User Insights* <br>
✏️ *Add User Groups/Events with invitations* <br>
✏️ *Video Stories* <br>
✏️ *Emoji reactions to posts and comments* <br>
✏️ *Two Factor Authentication Setting* <br>







## Installation

- Clone the repo
   ````sh
   git clone https://github.com/ianahart/iantheloop.git
   ````
- Create a ````.env```` file in the root directory. Follow directions in ````.env.        example```` file located in the root directory

- Create database (Normal installation)
   - To proceed with a normal install run all the commands below without ````docker-compose exec app````

   ````sh
    php artisan db:create
   ````
- Start docker up (Docker installation)
   ````sh
    docker-compose up -d
   ````

- Install PHP dependencies
   ````sh
    docker-compose exec app composer install
   ````
- Install JavaScript dependencies
   ````sh
    docker-compose exec app npm install
   ````

- Generate App Key
    ````sh
     docker-compose exec app php artisan key:generate
   ````
- Generate JWT secret
  ````sh
  docker-compose exec app php artisan jwt:secret --force
  ````
- Migrate database tables
  ````sh
   docker-compose exec  app php artisan migrate
  ````
- Start up Laravel
  ````sh
  docker-compose exec app php artisan serve
  ````
- Run the Dev server for [HMR]
  ````sh
   docker-compose exec app npm run hot
  ````
- Start the websockets server
  ````sh
   docker-compose exec app php artisan websockets:serve
  ````
- Start the queue
  ````sh
  docker-compose exec app php artisan queue:work database --queue=default,interactions,stories
  ````
- Start the scheduler
  ````sh
  docker-compose exec app php artisan schedule:work
## How To Use


1. Create an account
2. Login with account credentials
3. Create Profile
4. Follow other users

## License

MIT License

Copyright 2021 Ian Hart

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
