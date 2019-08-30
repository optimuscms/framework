# OptimusCMS Pages Module

This package provides the core backend functionality for creating pages and page templates 
within the CMS.

## Installation

This package can be installed through Composer.

```bash
composer require optimuscms/pages
```

In Laravel 5.5 and above the package will autoregister the service provider. 

In Laravel 5.4 you must install this service provider:

```php
// config/app.php
'providers' => [
    ...
    Optimus\Pages\PageServiceProvider::class,
    ...
];
```

## API Routes

The API follows standard RESTful conventions, with responses being returned in JSON. 
Appropriate HTTP status codes are provided, and these should be used to check the outcome of an operation.

**Pages**

 - [List pages](#list-pages)
 - [Get page](#get-page)
 - [Create page](#create-page)
 - [Update page](#update-page)
 - [Delete page](#delete-page)
 
**Templates**

 - [List templates](#list-templates)

### List pages

List all pages.

```http
GET /admin/api/pages
```

**Request Body**

| Parameter | Required | Type  | Description                                                                        |
|-----------|----------|-------|------------------------------------------------------------------------------------|
| `parent`  | No       | `int` | A page ID. When provided will only show pages that have this page as their parent. |


**Example Response**

```json
{
    "data": [
        {
            "id": 1,
            "title": "A root page",
            "slug": "a-root-page",
            "uri": "a-root-page",
            "has_fixed_uri": true,
            "parent_id": null,
            "template": "default",
            "has_fixed_template": true,
            "contents": [
                {
                    "id": 1,
                    "key": "content",
                    "value": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium."
                }
            ],
            "media": [
                {
                    "id": 1,
                    "folder_id": null, 
                    "name": "Image", 
                    "file_name": "image.jpg",
                    "disk": "public",
                    "mime_type": "image/jpeg", 
                    "size": 102400,
                    "group": "image",
                    "created_at": "2018-12-25 10:15:12",
                    "updated_at": "2018-12-25 10:15:12"
                }
            ],
            "children_count": 1,
            "is_stand_alone": false,
            "is_published": true,
            "is_deletable": true,
            "created_at": "2019-02-19 09:36:23",
            "updated_at": "2019-02-19 09:36:23"
        }
    ]
}
```

### Create page

Create a new page.

```http
POST /admin/api/pages
```

**Request Body**

| Parameter        | Required | Type      | Description                                                                                         |
|------------------|----------|-----------|-----------------------------------------------------------------------------------------------------|
| `title`          | Yes      | `string`  | The page title                                                                                      |
| `template`       | Yes      | `string`  | The name of the template that should be applied to this page.                                       |
| `is_stand_alone` | Yes      | `boolean` | If false, the page will not appear in any navigation and will only be accessible via a direct link. |
| `is_published`   | Yes      | `boolean` | Whether the page is ready to be made public.                                                        |
| `parent_id`      | No       | `int`     | The ID of a page to nest this one under.                                                            |
| `slug`           | No       | `string`  | A URL-friendly identifier. Will be used as part of the final public-facing URL to the page.         |


**Example Response**

Returns the newly created page. See [single page response example](#get-page).

### Get page

Get the details of a specific page.

```http
GET /admin/api/pages/{id}
```

**Request Body**

None

**Example Response**

```json
{
    "data": {
        "id": 2,
        "title": "A sub page",
        "slug": "a-sub-page",
        "uri": "a-root-page/a-sub-page",
        "has_fixed_uri": true,
        "parent_id": 1,
        "template": "default",
        "has_fixed_template": true,
        "contents": [
            {
                "id": 2,
                "key": "content",
                "value": "Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium."
            }
        ],
        "media": [
            {
                "id": 1,
                "folder_id": null, 
                "name": "Image", 
                "file_name": "image.jpg",
                "disk": "public",
                "mime_type": "image/jpeg", 
                "size": 102400,
                "group": "image",
                "created_at": "2018-12-25 10:15:12",
                "updated_at": "2018-12-25 10:15:12"
            }
        ],
        "children_count": 0,
        "is_stand_alone": false,
        "is_published": true,
        "is_deletable": true,
        "created_at": "2019-02-19 09:36:23",
        "updated_at": "2019-02-19 09:36:23"
    }
}
```

### Update page

Update the details of a specific page.

```http
PATCH /admin/api/pages/{id}
```

**Request Body**

| Parameter        | Required | Type      | Description                                                                                         |
|------------------|----------|-----------|-----------------------------------------------------------------------------------------------------|
| `title`          | Yes      | `string`  | The page title                                                                                      |
| `template`       | Yes      | `string`  | The name of the template that should be applied to this page.                                       |
| `is_stand_alone` | Yes      | `boolean` | If false, the page will not appear in any navigation and will only be accessible via a direct link. |
| `is_published`   | Yes      | `boolean` | Whether the page is ready to be made public.                                                        |
| `parent_id`      | No       | `int`     | The ID of a page to nest this one under.                                                            |
| `slug`           | No       | `string`  | A URL-friendly identifier. Will be used as part of the final public-facing URL to the page.         |


**Example Response**

Returns the updated page. See [single page response example](#get-page).

### Delete page

Delete a specific page.

```http
DELETE /admin/api/pages/{id}
```

**Request Body**

None

**Example Response**

The HTTP status code will be `204` if successful.

### List templates

List all available templates.

```http
GET /admin/api/page-templates
```

**Request Body**

None

**Example Response**

```json
{
    "data": [
        {
            "name": "home", 
            "label": "Home"
        },
        {
            "name": "default", 
            "label": "Default"
        }
    ]
}
```

### Working with page templates

#### Create a template

```php
use Optimus\Pages\Template;
use Illuminate\Http\Request;
use Optimus\Pages\Models\Page;

class DefaultTemplate extends Template
{
    public function name(): string
    {
        return 'default';
    }
    
    public function label(): string
    {
        return 'A custom label';
    }

    public function validate(Request $request)
    {
        $request->validate([
            'content' => 'required'
        ]);
    }

    public function save(Page $page, Request $request)
    {
        $page->addContents([
            'content' => $request->input('content')
        ]);

        $page->attachMedia($request->input('media_id'));
    }
}
```

#### Register and retrieve templates

```php
use Optimus\Pages\Facades\Template;

// Get all the registered templates...
Template::all();

// Get the template with the given name...
Template::find($name);

// Register a template...
Template::register(new DefaultTemplate);

// Register multiple templates...
Template::registerMany([
    new HomeTemplate,
    new ContactTemplate
]);
```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
