# Project API Documentation

## Table of Contents
1. [Prerequisites](#prerequisites)
2. [Setup Instructions](#setup-instructions)
3. [API Documentation](#api-documentation)
   - [Authentication](#authentication)
     - [Register User](#register-user)
     - [Login User](#login-user)
   - [Example Requests](#example-requests)
4. [User CRUD API](#user-crud-api-documentation)
   - [Fetch Users List](#fetch-users-list)
   - [Fetch Single User](#fetch-single-user)
   - [Update User](#update-user)
   - [Delete User](#delete-user)
   - [Restore Deleted User](#restore-deleted-user)
5. [Project Management API](#project-management-api-documentation)
   - [Endpoints Overview](#endpoints)
   - [Create Project](#2-create-project)
   - [List Projects](#1-list-projects)
   - [Advanced Filtering](#advanced-filtering)
6. [Attributes Management API](#attributes-management-api-documentation)
   - [Endpoints Overview](#endpoints-1)
   - [Attribute Types](#attribute-types)
7. [Timesheet Management API](#timesheet-management-api-documentation)
   - [Endpoints Overview](#endpoints-2)
   - [Filtering Options](#filtering-options)

## Prerequisites
Ensure you have the following installed before setting up the project:
- **PHP 8.2+**
- **Composer**
- **MySQL or PostgreSQL**
- **Laravel 11** (installed globally via Composer)
- **Node.js & NPM** (for frontend assets, if applicable)

## Setup Instructions

### 1. Clone the Repository
```sh
git https://github.com/DevAthul-88/timesheet_api.git
cd timesheet_api
```

### 2. Install Dependencies
```sh
composer install
```

### 3. Configure Environment
Copy the example environment file and update the necessary credentials:
```sh
cp .env.example .env
```
Update database connection details in `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=timesheet_api
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Generate Application Key
```sh
php artisan key:generate
```

### 5. Run Migrations and Seeders
```sh
php artisan migrate --seed
```

### 6. Start the Development Server
```sh
php artisan serve
```

### 7. Open Browser  
```
http://127.0.0.1:8000/
```

### 8. View Markdown Documentation  
The **Markdown-based documentation** will be displayed.  

### ❌ No Markdown Output?  
Run:  
```sh
php artisan cache:clear && php artisan view:clear
```

## API Documentation

### Authentication
#### Register User
**Endpoint:** `POST /api/register`

**Request:**
```json
{
  "name": "John Doe",
  "email": "johndoe@example.com",
  "password": "password",
  "password_confirmation": "password"
}
```

**Response:**
```json
{
  "message": "User registered successfully",
  "token": "<JWT_TOKEN>"
}
```

#### Login User
**Endpoint:** `POST /api/login`

**Request:**
```json
{
  "email": "johndoe@example.com",
  "password": "password"
}
```

**Response:**
```json
{
  "message": "Login successful",
  "token": "<JWT_TOKEN>"
}
```

## Example Requests

### Fetch User Profile
**Endpoint:** `GET /api/user`

**Headers:**
```json
{
  "Authorization": "Bearer <JWT_TOKEN>"
}
```

**Response:**
```json
{
  "id": 1,
  "name": "John Doe",
  "email": "johndoe@example.com"
}
```

## Test Credentials
Use the following test credentials to authenticate API requests:

```json
{
  "email": "test@example.com",
  "password": "password"
}
```

## User CRUD API Documentation

### Fetch Users List
**Endpoint:** `GET /api/users`

**Query Parameters:**
- `search` (optional): Filter users by first name, last name, or email.
- `per_page` (optional, default: 15): Number of results per page.

**Response:**
```json
{
  "data": [
    {
      "id": 1,
      "first_name": "John",
      "last_name": "Doe",
      "email": "johndoe@example.com"
    }
  ],
  "links": {...},
  "meta": {...}
}
```

### Fetch Single User
**Endpoint:** `GET /api/users/{id}`

**Response:**
```json
{
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "johndoe@example.com"
  }
}
```

### Update User
**Endpoint:** `PUT /api/users/{id}`

**Request Body:**
```json
{
  "first_name": "Updated Name",
  "last_name": "Updated Last",
  "email": "updated@example.com"
}
```

**Response:**
```json
{
  "data": {
    "id": 1,
    "first_name": "Updated Name",
    "last_name": "Updated Last",
    "email": "updated@example.com"
  }
}
```

### Delete User
**Endpoint:** `DELETE /api/users/{id}`

**Response:**
```json
{
  "message": "User deleted successfully"
}
```

### Restore Deleted User
**Endpoint:** `POST /api/users/{id}/restore`

**Response:**
```json
{
  "data": {
    "id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "johndoe@example.com"
  }
}
```

## Project Management API Documentation

### Overview
This API provides comprehensive CRUD operations for Projects with advanced filtering and attribute management.

### Endpoints

#### 1. List Projects
- **Endpoint:** `GET /api/projects`
- **Query Parameters:**
  - `per_page`: Number of projects per page (default: 10)
  - `filter[status]`: Filter by project status
  - `filter[eav]`: Filter by extended attributes

**Example Request:**
```bash
GET /api/projects?per_page=20&filter[status]=pending
```

**Response Example:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Marketing Campaign",
            "status": "pending",
            "users": [
                {
                    "id": 1,
                    "name": "John Doe"
                }
            ],
            "attributeValues": [
                {
                    "name": "Department",
                    "value": "Marketing"
                }
            ]
        }
    ],
    "meta": {
        "current_page": 1,
        "total": 15
    }
}
```

#### 2. Create Project
- **Endpoint:** `POST /api/projects`
- **Request Body:**
```json
{
    "name": "Marketing Campaign",
    "status": "pending",
    "attributes": {
        "Department": "Marketing",
        "Start Date": "2024-02-01",
        "Priority": "Medium",
        "Budget": 25000,
        "Client": "XYZ Company"
    }
}
```

#### 3. Get Single Project
- **Endpoint:** `GET /api/projects/{project_id}`
- **Returns:** Full project details including users, timesheets, and attributes

#### 4. Update Project
- **Endpoint:** `PUT/PATCH /api/projects/{project_id}`
- **Request Body:** Same as create project
- **Supports:** Updating project details and attributes

#### 5. Delete Project
- **Endpoint:** `DELETE /api/projects/{project_id}`
- **Note:** Soft deletes project and associated timesheets

#### 6. Assign User to Project
- **Endpoint:** `POST /api/projects/{project_id}/assign-user`
- **Request Body:**
```json
{
    "user_id": 1,
    "role": "member"
}
```

#### 7. Remove User from Project
- **Endpoint:** `POST /api/projects/{project_id}/remove-user`
- **Request Body:**
```json
{
    "user_id": 1
}
```

### Test Credentials & Sample Data

#### Test Projects
1. Marketing Project
   - ID: 1
   - Name: "Marketing Campaign"
   - Status: "pending"

2. Development Project
   - ID: 2
   - Name: "Product Development"
   - Status: "active"

#### Test Users
1. Project Manager
   - ID: 1
   - Name: "John Doe"
   - Email: "john.doe@example.com"

2. Team Member
   - ID: 2
   - Name: "Jane Smith"
   - Email: "jane.smith@example.com"

### Error Handling
- 404: Project not found
- 400: Invalid attribute
- 422: Validation errors
- 500: Server errors

### Advanced Filtering
#### EAV (Entity-Attribute-Value) Filtering
```bash
# Filter projects by department and priority
GET /api/projects?filter[eav][Department]=Marketing&filter[eav][Priority]=Medium
```

### Notes
- Supports extended (EAV) attributes
- Soft delete implementation
- Transactional operations for data integrity
- Comprehensive logging for tracking operations

## Attributes Management API Documentation

### Overview
This API provides CRUD operations for managing dynamic attributes across different entity types.

### Endpoints

#### 1. List Attributes
- **Endpoint:** `GET /api/attributes`
- **Description:** Retrieve all defined attributes

**Response Example:**
```json
{
    "data": [
        {
            "id": 1,
            "name": "Priority",
            "type": "select",
            "description": "Project priority level",
            "options": ["Low", "Medium", "High", "Critical"],
            "is_required": true,
            "meta": {
                "created_at": "2024-02-15T10:00:00Z",
                "updated_at": "2024-02-15T10:00:00Z",
                "options_count": 4
            }
        }
    ]
}
```

#### 2. Create Attribute
- **Endpoint:** `POST /api/attributes`
- **Request Body:**
```json
{
    "name": "Department",
    "type": "select",
    "description": "Project department",
    "options": ["Marketing", "Sales", "Engineering", "Support"],
    "is_required": false,
    "entity_type": "App\\Models\\Project"
}
```

**Response:**
- **Success:** Returns created attribute details
- **Status Code:** 201 Created

#### 3. Get Single Attribute
- **Endpoint:** `GET /api/attributes/{attribute_id}`
- **Description:** Retrieve specific attribute details

**Response Example:**
```json
{
    "data": {
        "id": 1,
        "name": "Priority",
        "type": "select",
        "description": "Project priority level",
        "options": ["Low", "Medium", "High", "Critical"],
        "is_required": true,
        "meta": {
            "created_at": "2024-02-15T10:00:00Z",
            "updated_at": "2024-02-15T10:00:00Z",
            "options_count": 4
        }
    }
}
```

#### 4. Update Attribute
- **Endpoint:** `PUT/PATCH /api/attributes/{attribute_id}`
- **Request Body:** Same as create attribute
- **Description:** Update existing attribute details

#### 5. Delete Attribute
- **Endpoint:** `DELETE /api/attributes/{attribute_id}`
- **Description:** Remove an attribute

### Attribute Types
- `text`: Single-line text input
- `textarea`: Multi-line text input
- `select`: Dropdown selection
- `multiselect`: Multiple selection
- `number`: Numeric input
- `date`: Date selection
- `boolean`: True/False toggle

### Example Attribute Scenarios

#### Project Priority Attribute
```json
{
    "name": "Priority",
    "type": "select",
    "description": "Project priority level",
    "options": ["Low", "Medium", "High", "Critical"],
    "is_required": true,
    "entity_type": "App\\Models\\Project"
}
```

#### Budget Attribute
```json
{
    "name": "Budget",
    "type": "number",
    "description": "Project budget allocation",
    "is_required": false,
    "entity_type": "App\\Models\\Project"
}
```

### Error Handling
- 400: Bad Request
- 404: Attribute Not Found
- 422: Validation Error
- 500: Server Error

### Validation Rules
- `name`: Required, unique, max 255 characters
- `type`: Required, must be one of predefined types
- `description`: Optional, max 500 characters
- `options`: Optional, must be valid JSON array for select types
- `is_required`: Boolean
- `entity_type`: Must be a valid PHP class namespace

### Test Attributes
1. Project Priority
   - ID: 1
   - Name: "Priority"
   - Type: "select"

2. Project Department
   - ID: 2
   - Name: "Department"
   - Type: "select"

### Notes
- Supports dynamic attribute creation
- Attributes can be associated with specific entity types
- Comprehensive error logging
- Transactional database operations

## Timesheet Management API Documentation

### Overview
This API provides comprehensive CRUD operations for managing timesheets, with advanced filtering and sorting capabilities.

### Endpoints

#### 1. List Timesheets
- **Endpoint:** `GET /api/timesheets`
- **Query Parameters:**
  - `per_page`: Number of results per page (default: 10)
  - `sort_by`: Column to sort by (default: 'created_at')
  - `sort_direction`: Sort order ('asc' or 'desc')
  - `task_name`: Filter by task name
  - `project_id`: Filter by project
  - `user_id`: Filter by user
  - `date_from`: Start date filter
  - `date_to`: End date filter

**Example Request:**
```bash
GET /api/timesheets?per_page=15&project_id=1&date_from=2024-01-01&date_to=2024-01-31
```

**Response Example:**
```json
{
    "data": [
        {
            "id": 1,
            "user_id": 1,
            "project_id": 1,
            "task_name": "Design",
            "date": "2024-01-15",
            "hours": 8.5,
            "description": "Worked on the initial design",
            "user": {
                "id": 1,
                "name": "John Doe"
            },
            "project": {
                "id": 1,
                "name": "Marketing Campaign"
            }
        }
    ],
    "meta": {
        "current_page": 1,
        "total_pages": 3,
        "total_items": 30,
        "sort_by": "created_at",
        "sort_direction": "desc"
    }
}
```

#### 2. Create Timesheet
- **Endpoint:** `POST /api/timesheets`
- **Request Body:**
```json
{
    "user_id": 1,
    "project_id": 1,
    "task_name": "Design",
    "date": "2024-01-15",
    "hours": 8.5,
    "description": "Worked on the initial design"
}
```

#### 3. Get Single Timesheet
- **Endpoint:** `GET /api/timesheets/{timesheet_id}`
- **Returns:** Detailed timesheet information

#### 4. Update Timesheet
- **Endpoint:** `PUT/PATCH /api/timesheets/{timesheet_id}`
- **Request Body:** Same as create timesheet

#### 5. Delete Timesheet
- **Endpoint:** `DELETE /api/timesheets/{timesheet_id}`

#### 6. Get Current User's Timesheets
- **Endpoint:** `GET /api/my-timesheets`
- **Description:** Retrieves timesheets for the authenticated user

### Filtering Options

#### Sorting
- Supported columns: 
  - `id`
  - `task_name`
  - `date`
  - `hours`
  - `created_at`
  - `updated_at`

#### Filtering
- Filter by task name (partial match)
- Filter by project
- Filter by user
- Date range filtering

### Validation Rules
- `user_id`: Required, must exist in users table
- `project_id`: Required, must exist in projects table
- `task_name`: Required, max 255 characters
- `date`: Required, valid date
- `hours`: Required, numeric, min 0, max 24
- `description`: Optional, max 1000 characters

### Example Timesheet Scenarios

#### Full Day Work
```json
{
    "user_id": 1,
    "project_id": 2,
    "task_name": "Feature Development",
    "date": "2024-02-15",
    "hours": 8,
    "description": "Implemented new user authentication module"
}
```

### Bulk Timesheet Operations

#### Bulk Create Timesheets
- **Endpoint:** `POST /api/timesheets/bulk`
- **Request Body:**
```json
{
    "timesheets": [
        {
            "user_id": 1,
            "project_id": 1,
            "task_name": "Design",
            "date": "2024-02-15",
            "hours": 4,
            "description": "UI/UX design review"
        },
        {
            "user_id": 1,
            "project_id": 2,
            "task_name": "Development",
            "date": "2024-02-15",
            "hours": 4,
            "description": "Backend API implementation"
        }
    ]
}
```

#### Bulk Update Timesheets
- **Endpoint:** `PUT /api/timesheets/bulk`
- **Request Body:**
```json
{
    "timesheets": [
        {
            "id": 1,
            "task_name": "Updated Task Name",
            "hours": 6,
            "description": "Updated description"
        },
        {
            "id": 2,
            "task_name": "Another Updated Task",
            "hours": 5,
            "description": "Another updated description"
        }
    ]
}
```

### Reporting and Analytics

#### Timesheet Summary
- **Endpoint:** `GET /api/timesheets/summary`
- **Query Parameters:**
  - `date_from`: Start date for summary
  - `date_to`: End date for summary
  - `user_id`: (Optional) Filter by specific user
  - `project_id`: (Optional) Filter by specific project

**Response Example:**
```json
{
    "total_hours": 280.5,
    "average_daily_hours": 7.2,
    "projects_breakdown": [
        {
            "project_id": 1,
            "project_name": "Marketing Campaign",
            "total_hours": 120.5
        },
        {
            "project_id": 2,
            "project_name": "Product Development",
            "total_hours": 160
        }
    ],
    "user_breakdown": [
        {
            "user_id": 1,
            "user_name": "John Doe",
            "total_hours": 180.5
        },
        {
            "user_id": 2,
            "user_name": "Jane Smith",
            "total_hours": 100
        }
    ]
}
```

### Export Options

#### Export Timesheets
- **Endpoint:** `GET /api/timesheets/export`
- **Query Parameters:**
  - `format`: Export format (csv, xlsx, pdf)
  - `date_from`: Start date for export
  - `date_to`: End date for export
  - `user_id`: (Optional) Filter by specific user
  - `project_id`: (Optional) Filter by specific project

### Performance Considerations
- Pagination implemented to manage large datasets
- Indexing on frequently queried columns
- Caching mechanisms for frequently accessed reports
- Efficient query optimization

### Security Considerations
- Authentication required for all endpoints
- Data privacy and scope restrictions
- Audit logging for all timesheet modifications

### Integration Points
- Seamless integration with Project Management API
- User authentication and authorization
- Reporting and invoicing systems
- Performance management tools

## Conclusion
This comprehensive Timesheet Management API provides robust, flexible, and secure methods for tracking, managing, and analyzing work hours across projects and users. With advanced filtering, reporting, and export capabilities, it serves as a powerful tool for project management and resource allocation.
