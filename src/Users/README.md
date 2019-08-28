# OptimusCMS Users Module

This package provides the core backend functionality for managing the users who can access the CMS.

## Installation

This package can be installed through Composer.

```bash
composer require optimuscms/users
```

In Laravel 5.5 and above the package will autoregister the service provider. 

In Laravel 5.4 you must install this service provider:

```php
// config/app.php
'providers' => [
    ...
    OptimusCMS\Users\UserServiceProvider::class,
    ...
];
```

## API Routes

The API follows standard RESTful conventions, with responses being returned in JSON. 
Appropriate HTTP status codes are provided, and these should be used to check the outcome of an operation.

**Users**

 - [List users](#list-users)
 - [Get user](#get-user)
 - [Create user](#create-user)
 - [Update user](#update-user)
 - [Delete user](#delete-user)

### List users

List all registered users.

```http
GET /admin/api/users
```

**Request Body**

None

**Example Response**

```json
{
    "data": [
        {
            "id": 1,
            "name": "Jack Robertson",
            "email": "jack@optixsolutions.co.uk",
            "username": "jack",
            "created_at": "2019-02-19 09:14:44",
            "updated_at": "2019-02-19 09:14:50"
        },
        {
            "id": 2,
            "name": "Rich Moore",
            "email": "rich@optixsolutions.co.uk",
            "username": "rich",
            "created_at": "2019-02-19 09:36:23",
            "updated_at": "2019-02-19 09:36:23"
        }
    ]
}
```

### Create user

Create a new users who can access the CMS.

```http
POST /admin/api/users
```

**Request Body**

| Parameter  | Required  | Type     | Description                                       |
|------------|-----------|----------|---------------------------------------------------|
| `name`     | Yes       | `string` | The name of the user                              |
| `email`    | Yes       | `string` | The email address of the user                     |
| `username` | Yes       | `string` | A username which will be used to login to the CMS |
| `password` | Yes       | `string` | A password which will be used to login to the CMS |

**Example Response**

Returns the details of the newly created user. See [single user response example](#get-user).

### Get user

Get the details of a specific user.

```http
GET /admin/api/users/{id}
```

**Request Body**

None

**Example Response**

```json
{
    "data": {
        "id": 1,
        "name": "Jack Robertson",
        "email": "jack@optixsolutions.co.uk",
        "username": "jack",
        "created_at": "2019-02-19 09:14:44",
        "updated_at": "2019-02-19 09:14:50"
    }
}
```

### Update user

Update the details of a specific user.

```http
PATCH /admin/api/users/{id}
```

**Request Body**

| Parameter  | Required | Type     | Description                                       |
|------------|----------|----------|---------------------------------------------------|
| `name`     | Yes      | `string` | The name of the user                              |
| `email`    | Yes      | `string` | The email address of the user                     |
| `username` | Yes      | `string` | A username which will be used to login to the CMS |
| `password` | No       | `string` | A password which will be used to login to the CMS |

**Example Response**

Returns the details of the updated user. See [single user response example](#get-user).

### Delete user

Delete a user so they can no longer access the CMS.

```http
DELETE /admin/api/users/{id}
```

**Request Body**

None

**Example Response**

The HTTP status code will be `204` if successful.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
