# Description
Web application using Symfony framework and MySQL database. The app keeps users, chirps (tweets) and likes. Users can register, login, logout, view feed page (all chirps from followers sorted by time in descending order), create a chirp, delete a chirp, view own chirps, discover new people, get all chirps by specific user (sorted by time in descending order), follow or unfollow a user, like or dislike a chirp. Admins have access to edit and delete chirps to all users.

# Screenshots
<img width="960" alt="1register" src="https://user-images.githubusercontent.com/17432777/136666519-cd995122-ae1d-4cfc-8984-36d0730d8e2b.png">

<img width="960" alt="2feed" src="https://user-images.githubusercontent.com/17432777/136666521-6bf8deda-3585-493b-b531-abd61d8a4e9e.png">

<img width="960" alt="3discover" src="https://user-images.githubusercontent.com/17432777/136666522-443db24d-5232-471b-9405-14e8b34a6e3b.png">

<img width="960" alt="follow" src="https://user-images.githubusercontent.com/17432777/136666776-32feeb19-3731-40ef-8f35-47ad67530965.png">

<img width="960" alt="4profile" src="https://user-images.githubusercontent.com/17432777/136666523-e8a2ee44-3eb4-4944-9124-897cc9aeaf6e.png">

##### ER Diagram
<img width="408" alt="erdiagram" src="https://user-images.githubusercontent.com/17432777/136666525-cbca8ac6-165c-4edf-a2ef-ca24464b4314.png">

# Run
* [Install XAMPP](https://www.apachefriends.org/index.html).
* [Install Composer](https://getcomposer.org/download/).
* Open the project (with VSCode for example) and type in console: 
    * ```composer update --ignore-platform-reqs```
    * ```php bin/console doctrine:database:create```
    * ```php bin/console doctrine:schema:update --force --dump-sql```
* Add user roles in "roles" table - ROLE_ADMIN and ROLE_USER
* Finally start the project:
    * ```php bin/console server:run```