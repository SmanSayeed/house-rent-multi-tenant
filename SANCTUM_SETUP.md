# Laravel Sanctum Setup Documentation

## Overview
Laravel Sanctum has been successfully installed and configured for the Multi-Tenant Flat & Bill Management System. This provides API authentication capabilities for future mobile app integration or external API access.

## What's Been Configured

### 1. Package Installation
- ✅ Laravel Sanctum v4.2.0 installed via Composer
- ✅ Configuration files published to `config/sanctum.php`
- ✅ Migration published to create `personal_access_tokens` table

### 2. User Model Configuration
- ✅ Added `HasApiTokens` trait to `app/Models/User.php`
- ✅ Users can now create API tokens for authentication

### 3. Middleware Configuration
- ✅ Added Sanctum middleware to `bootstrap/app.php`
- ✅ Configured `EnsureFrontendRequestsAreStateful` for SPA support

### 4. API Routes
- ✅ Created `routes/api.php` with authentication endpoints
- ✅ Added API routes to `bootstrap/app.php`

### 5. AuthController
- ✅ Created `app/Http/Controllers/AuthController.php` with:
  - `POST /api/register` - User registration
  - `POST /api/login` - User login (returns token)
  - `POST /api/logout` - Token revocation
  - `GET /api/user` - Get authenticated user
  - `GET /api/test` - Test protected route

## API Endpoints

### Public Endpoints
```
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

```
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password123"
}
```

### Protected Endpoints (Require Bearer Token)
```
GET /api/user
Authorization: Bearer {token}

GET /api/test
Authorization: Bearer {token}

POST /api/logout
Authorization: Bearer {token}
```

## Testing Sanctum

### Option 1: Use the Test Script
```bash
php test_sanctum.php
```

### Option 2: Manual Testing with cURL
```bash
# 1. Register a user
curl -X POST http://127.0.0.1:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"Test User","email":"test@example.com","password":"password123","password_confirmation":"password123"}'

# 2. Login (use token from registration response)
curl -X POST http://127.0.0.1:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"test@example.com","password":"password123"}'

# 3. Test protected route (replace {token} with actual token)
curl -X GET http://127.0.0.1:8000/api/test \
  -H "Authorization: Bearer {token}"
```

## Configuration Details

### Sanctum Configuration (`config/sanctum.php`)
- **Stateful Domains**: Configured for localhost and common development domains
- **Guard**: Uses 'web' guard for session-based authentication
- **Expiration**: Set to null (tokens don't expire by default)
- **Token Prefix**: Empty (no prefix)

### Database
- **Table**: `personal_access_tokens`
- **Columns**: 
  - `id` (primary key)
  - `tokenable_type` (model class)
  - `tokenable_id` (user ID)
  - `name` (token name)
  - `token` (hashed token)
  - `abilities` (JSON array)
  - `last_used_at` (timestamp)
  - `expires_at` (timestamp)
  - `created_at`, `updated_at`

## Integration with Multi-Tenant System

### For Future Development
When implementing the multi-tenant features, consider:

1. **Token Scoping**: Tokens should be scoped to specific tenants
2. **Middleware Integration**: Combine with `TenantMiddleware` for data isolation
3. **Role-Based Access**: Extend token abilities based on user roles (admin, house_owner, tenant)

### Example Token Creation with Tenant Context
```php
// In your controllers, when creating tokens:
$token = $user->createToken('auth-token', [
    'tenant_id' => $user->tenant_id,
    'role' => $user->role
]);
```

## Security Considerations

1. **Token Storage**: Store tokens securely on client side
2. **HTTPS**: Use HTTPS in production
3. **Token Rotation**: Implement token refresh mechanism if needed
4. **Rate Limiting**: Add rate limiting to authentication endpoints
5. **CORS**: Configure CORS properly for cross-origin requests

## Next Steps

1. ✅ Sanctum is ready for use
2. 🔄 Implement multi-tenant middleware
3. 🔄 Create role-based API endpoints
4. 🔄 Add API documentation (Swagger/OpenAPI)
5. 🔄 Implement token refresh mechanism
6. 🔄 Add rate limiting and security headers

## Troubleshooting

### Common Issues
1. **CORS Errors**: Ensure CORS is configured in `config/cors.php`
2. **Token Not Working**: Check if token is being sent correctly in Authorization header
3. **Middleware Issues**: Verify Sanctum middleware is properly registered
4. **Database Issues**: Ensure migrations have been run

### Debug Commands
```bash
# Check if Sanctum is working
php artisan tinker
>>> \Laravel\Sanctum\Sanctum::class

# Check token table
php artisan tinker
>>> \App\Models\User::first()->tokens

# Test token creation
php artisan tinker
>>> $user = \App\Models\User::first()
>>> $user->createToken('test-token')
```
