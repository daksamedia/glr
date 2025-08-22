# Gelaro API - Laravel Version

This is a Laravel conversion of the original PHP API project for Gelaro, a marketplace platform for service providers.

## Features

- **Authentication System**: JWT-based authentication with email verification
- **Vendor Management**: Create and manage service provider profiles
- **Booking System**: Handle service bookings between customers and vendors
- **Rating & Reviews**: Customer feedback system
- **Service Management**: Vendors can create and manage their services
- **Gallery Management**: Image gallery for vendor profiles
- **Statistics Tracking**: View counts, likes, and booking statistics

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy environment file:
   ```bash
   cp .env.example .env
   ```

4. Generate application key:
   ```bash
   php artisan key:generate
   ```

5. Generate JWT secret:
   ```bash
   php artisan jwt:secret
   ```

6. Configure your database in `.env` file

7. Run migrations:
   ```bash
   php artisan migrate
   ```

8. Create storage link:
   ```bash
   php artisan storage:link
   ```

9. Start the development server:
   ```bash
   php artisan serve
   ```

## API Endpoints

### Authentication
- `POST /api/auth/register` - User registration
- `POST /api/auth/login` - User login
- `GET /api/auth/verify` - Email verification
- `POST /api/auth/forgot` - Forgot password
- `POST /api/auth/reset_password` - Reset password

### Vendors
- `GET /api/product/read` - List all vendors
- `GET /api/product/detail` - Get vendor details
- `POST /api/product/create` - Create vendor (authenticated)
- `POST /api/product/update` - Update vendor (authenticated)
- `GET /api/product/me` - Get my vendor profile (authenticated)

### Bookings
- `POST /api/booking/create` - Create booking
- `GET /api/booking/user/me` - Get my bookings (authenticated)
- `GET /api/booking/vendor/me` - Get vendor bookings (authenticated)

### Services
- `GET /api/services/read` - List services by vendor
- `POST /api/services/create` - Create service (authenticated)
- `POST /api/services/update` - Update service (authenticated)

### Ratings
- `GET /api/ratings/read` - Get ratings for vendor
- `POST /api/ratings/create` - Create rating (authenticated)
- `GET /api/ratings/myratings` - Get my ratings (authenticated)

## Key Changes from Original PHP API

1. **Framework Migration**: Converted from vanilla PHP to Laravel framework
2. **Database**: Using Laravel's Eloquent ORM and migrations
3. **Authentication**: Implemented JWT authentication with tymon/jwt-auth
4. **Validation**: Using Laravel's built-in validation
5. **File Structure**: Organized according to Laravel conventions
6. **Error Handling**: Improved error handling and responses
7. **Code Organization**: Better separation of concerns with controllers, models, and middleware

## Models

- **User**: User accounts with authentication
- **Vendor**: Service provider profiles
- **Category**: Service categories
- **Service**: Individual services offered by vendors
- **Booking**: Service bookings
- **Rating**: Customer reviews and ratings
- **Gallery**: Image galleries for vendors
- **Statistic**: Analytics and statistics
- **Venue**: Venue information (if applicable)

## Configuration

Key configuration files:
- `.env` - Environment variables
- `config/jwt.php` - JWT configuration
- `config/auth.php` - Authentication configuration

## Security Features

- JWT token-based authentication
- Password hashing with bcrypt
- Input validation and sanitization
- CORS support
- Rate limiting (can be configured)

## File Storage

Images and files are stored in the `storage/app/public` directory and served through the public storage link.

## Email Integration

The system includes email functionality for:
- Account verification
- Password reset
- Booking notifications

Configure your mail settings in the `.env` file to enable email features.