<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Laravel Boilerplate for Squareboat

This repository provides a robust Laravel 11 boilerplate that standardizes project setups for our organization. It comes preconfigured with several key features and integrations to help you hit the ground running.

## Table of Contents

- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Environment Configuration](#environment-configuration)
- [Project Structure](#project-structure)
- [Integrated Functionalities](#integrated-functionalities)
- [Authentication & Authorization](#authentication--authorization)
- [API Routing & Documentation](#api-routing--documentation)
- [Service-Repository Pattern](#service-repository-pattern)
- [Social Authentication (Google Login)](#social-authentication-google-login)
- [Error Monitoring with Sentry](#error-monitoring-with-sentry)
- [Running the Application](#running-the-application)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

## Features

- **Authentication & Authorization:**
  - API authentication via Laravel Sanctum
  - Role-based access control using Spatie Laravel Permission

- **API Routing:**
  - Custom Route Service Provider with global prefix (e.g. `/api/v1`)

- **Documentation:**
  - Swagger/OpenAPI integration (via DarkaOnLine/L5-Swagger)

- **Social Authentication:**
  - Google login integration using Laravel Socialite

- **Error Monitoring:**
  - Sentry integration for real-time error logging and monitoring

- **Service-Repository Pattern:**
  - Clean separation of business logic (services) and data access (repositories)

- **Telescope for development environment:**
  - Telescope is already present for the developement environment so that you can check everything with the help of UI 

## Requirements

- **PHP:** >= 8.0
- **Composer**
- **Node.js & npm:** For asset compilation
- **Database:** MySQL or any supported database
- **Redis:** (Optional) For queues and caching if you later integrate queue monitoring

## Installation

1. **Clone the Repository:**
```bash
git clone <your-repo-url>
cd laravel_boiler_plate
```

2. **Install Composer Dependencies:**
```bash
composer install
```

3. **Install Node Dependencies:**
```bash
npm install
```

4. **Copy the Environment File and Generate an Application Key:**
```bash
cp .env.example .env
php artisan key:generate
```

5. **Run Migrations (and Seed, if applicable):**
```bash
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
```

6. **Compile Frontend Assets:**
```bash
npm run dev
```

## Environment Configuration

In your .env file, configure the following (update values as needed):

```dotenv
# Application
APP_NAME="Laravel Boilerplate"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=boilerplate_db
DB_USERNAME=root
DB_PASSWORD=your_password

# API Authentication (Sanctum)
SANCTUM_STATEFUL_DOMAINS=localhost
SESSION_DOMAIN=localhost

# Social Authentication (Google)
GOOGLE_CLIENT_ID=your-google-client-id
GOOGLE_CLIENT_SECRET=your-google-client-secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback

# Sentry Error Monitoring
SENTRY_LARAVEL_DSN=https://YOUR_PUBLIC_KEY@o0.ingest.sentry.io/YOUR_PROJECT_ID

# Swagger Documentation (optional settings)
L5_SWAGGER_GENERATE_ALWAYS=true
```

After modifying the .env file, clear your configuration cache:
```bash
php artisan config:clear
```

## Project Structure

A few key directories:

- **app/Http/Controllers:** Contains your application controllers (including Auth, SocialAuthController, etc.)
- **app/Repositories:** Contains repository interfaces and implementations
- **app/Services:** Contains service classes that encapsulate business logic
- **app/Swagger:** Contains dummy classes (e.g., OpenApiDefinition.php) anchoring global Swagger annotations
- **routes/web.php & routes/api.php:** Define your web and API routes
- **config/:** Contains configuration files for Sanctum, Sentry, L5-Swagger, etc.

## Integrated Functionalities

### Authentication & Authorization

- **API Authentication:**
  Laravel Sanctum is used for token-based API authentication.

- **Role-Based Access Control:**
  Spatie Laravel Permission is installed. The User model uses the HasRoles trait:
  ```php
  use Spatie\Permission\Traits\HasRoles;
  
  class User extends Authenticatable
  {
      use HasApiTokens, HasRoles, Notifiable;
  }
  ```

### API Routing & Documentation

- **Routing:**
  A custom Route Service Provider prefixes all API routes with /api/v1.
  Example in app/Providers/RouteServiceProvider.php:
  ```php
  protected function mapApiRoutes()
  {
      Route::prefix('api/v1')
          ->middleware('api')
          ->group(base_path('routes/api.php'));
  }
  ```

- **Swagger Documentation:**
  Swagger is integrated using DarkaOnLine/L5-Swagger.
  Global annotations are anchored in a dummy class:
  ```php
  <?php
  
  namespace App\Swagger;
  
  /**
   * @OA\Info(
   *   title="Laravel Boilerplate API",
   *   version="1.0.0",
   *   description="This is the API documentation for the Laravel Boilerplate.",
   *   @OA\Contact(email="support@example.com"),
   *   @OA\License(name="MIT", url="https://opensource.org/licenses/MIT")
   * )
   */
  class OpenApiDefinition
  {
      // Dummy class for Swagger global annotations.
  }
  ```

  Generate docs with:
  ```bash
  php artisan l5-swagger:generate
  ```
  And access them at: http://localhost:8000/api/documentation

### Service-Repository Pattern

- **Repository Interface & Implementation:**
  Define repository interfaces (e.g., UserRepositoryInterface) and concrete classes (e.g., UserRepository) in app/Repositories.

- **Service Layer:**
  Services (in app/Services) encapsulate business logic and interact with repositories.

- **Binding:**
  Bind interfaces to implementations in app/Providers/AppServiceProvider.php:
  ```php
  public function register()
  {
      $this->app->bind(
          \App\Repositories\UserRepositoryInterface::class,
          \App\Repositories\UserRepository::class
      );
  }
  ```

### Social Authentication (Google Login)

- **Laravel Socialite:**
  Installed via:
  ```bash
  composer require laravel/socialite
  ```

- **Configuration:**
  Add provider settings in config/services.php and set environment variables as shown in the Environment Configuration section.

- **Controller:**
  A SocialAuthController handles redirection and callback:
  ```php
  <?php
  
  namespace App\Http\Controllers\Auth;
  
  use App\Http\Controllers\Controller;
  use Illuminate\Http\Request;
  use Socialite;
  use App\Models\User;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Str;
  
  class SocialAuthController extends Controller
  {
      public function redirectToProvider($provider)
      {
          // Use the "web" middleware to ensure sessions are available
          return Socialite::driver($provider)->redirect();
      }
  
      public function handleProviderCallback($provider)
      {
          try {
              $socialUser = Socialite::driver($provider)->stateless()->user();
          } catch (\Exception $e) {
              return redirect('/login')->withErrors('Unable to login using ' . $provider . '. Please try again.');
          }
  
          $user = User::firstOrCreate(
              ['email' => $socialUser->getEmail()],
              [
                  'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'No Name',
                  'password' => bcrypt(Str::random(16))
              ]
          );
  
          Auth::login($user, true);
          return redirect()->intended('/');
      }
  }
  ```

- **Routes:**
  Define social authentication routes in routes/web.php (ensure these are within the "web" middleware group):
  ```php
  use App\Http\Controllers\Auth\SocialAuthController;
  
  Route::middleware(['web'])->group(function () {
      Route::get('auth/{provider}', [SocialAuthController::class, 'redirectToProvider'])
          ->name('social.redirect');
      Route::get('auth/{provider}/callback', [SocialAuthController::class, 'handleProviderCallback'])
          ->name('social.callback');
  });
  ```

### Error Monitoring with Sentry

- **Installation:**
  ```bash
  composer require sentry/sentry-laravel
  php artisan vendor:publish --provider="Sentry\Laravel\ServiceProvider"
  ```

- **Configuration:**
  Set your DSN in the .env file (see Environment Configuration). Sentry will automatically capture exceptions and performance issues.

## Running the Application

1. **Serve the Application:**
   ```bash
   php artisan serve
   ```
   The app will be available at http://localhost:8000.

2. **Testing Endpoints:**
   - Use Postman or cURL to test API endpoints (e.g., authentication, user endpoints)
   - Access Swagger UI at: http://localhost:8000/api/documentation
   - For Google login, visit: http://localhost:8000/auth/google

3. **Error Testing:**
   - Trigger an error (e.g., visit a test route) to ensure Sentry logs it

## Testing

- **Automated Testing:**
  Write tests using PHPUnit (or Pest) in the tests/ directory.

- **Run Tests:**
  ```bash
  php artisan test
  ```

## Contributing

Contributions are welcome! Please open an issue or submit a pull request with improvements or bug fixes. Follow our coding standards and add tests when applicable.

## License

This project is open-sourced under the MIT License.
