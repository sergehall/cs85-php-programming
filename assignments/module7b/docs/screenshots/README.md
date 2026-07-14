# Assignment 7B Screenshot Evidence

Capture the standalone application at `http://127.0.0.1:8008` unless the instructor specifically requests the integrated coursework URL.

1. `01-project-setup.png`  
   **Caption:** Laravel project setup for Module 7 Assignment 7B, showing the generated project folder and successful installation output.

2. `02-routes-code.png`  
   **Caption:** The `routes/web.php` file defines two closure routes, two controller routes, four named routes, and the dynamic `{id}` parameter.

3. `03-controller-code.png`  
   **Caption:** `HobbyController` organizes the personalized hobby data and provides the `index()` and `show()` actions.

4. `04-home-page.png`  
   **Caption:** Home page rendered from a closure route with my name and page title passed into a Blade template.

5. `05-about-page.png`  
   **Caption:** About page showing personal student information passed from the `/about` closure route.

6. `06-hobbies-page.png`  
   **Caption:** Hobbies index rendered by `HobbyController@index`, with three personalized hobbies displayed through a Blade loop.

7. `07-hobby-detail.png`  
   **Caption:** Dynamic `/hobbies/1` route rendered by `HobbyController@show` with the selected Photography record.

8. `08-not-found.png`  
   **Caption:** Required `/hobbies/999` test correctly returns Laravel's 404 Not Found response for a missing hobby.

9. `09-active-navigation.png`  
   **Caption:** Named-route navigation highlights the current Hobbies page while keeping Home and About available.

10. `10-route-list.png`  
    **Caption:** Artisan route table confirms the four required GET routes, route names, dynamic parameter, and controller actions.

Recommended route-table command:

```bash
php artisan route:list --path=hobbies
```
