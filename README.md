# API Anime Platform

This project is a RESTful API built with Symfony and API Platform, designed to manage data related to animes, authors, characters, tags, and anime types. Authentication is handled via JWT.

## 🚀 Features

- Manage **animes**, **authors**, **characters**, **tags**, and **anime types**.
- Secure authentication using **JWT**.
- Fully RESTful architecture powered by **API Platform**.
- Auto-generated API documentation accessible via Swagger UI.
- Built-in filters and sorting for certain entities.

---

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- Symfony CLI (optional)
- MySQL

---

## 🛠️ Installation

1. Clone the repository:

   ```bash  
   git clone https://github.com/your-repo/api-anime-platform.git  
   cd api-anime-platform  
   ```

2. Install dependencies:
    
    ```bash
    composer install  
    ```
   
3. Configure the environment:

    ```bash
    cp .env .env.local
    ```

    Update the variables to include your database credentials:
    
    ```bash
    DATABASE_URL="mysql://db_user:db_password@127.0.0.1:3306/db_name"  
    JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem  
    JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem  
    JWT_PASSPHRASE="your_passphrase"
    ```

4. Generate JWT keys:

    ```bash
    mkdir -p config/jwt  
    openssl genrsa -out config/jwt/private.pem 2048  
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem  
    ```
   
5. Run migrations to create the database schema:

    ```bash
    php bin/console doctrine:database:create  
    php bin/console doctrine:migrations:migrate    
    ```

6. (Optional) Start the Symfony server:

    ```bash
    symfony server:start  
    ```
   
---

## 🧪 Testing the API

### Authentication

#### Retrieve a JWT Token
**Endpoint**:  
`POST /login`

**Request Payload**:
```json  
{  
  "username": "user@example.com",  
  "password": "password"  
}
```

**Response Example:**:
```json
{  
  "token": "your_jwt_token",
  "refresh_token": "your_refresh_token"
}  
```

Include the returned token in the Authorization header for subsequent API calls:

```http
Authorization: Bearer your_jwt_token
```

---

### Main Endpoints

| **Resource**      | **Endpoint**               | **Methods**       | **Description**                          |  
|--------------------|----------------------------|-------------------|------------------------------------------|  
| **Animes**         | `/animes`                 | `GET`, `POST`, `PUT`, `DELETE` | Manage anime records, including creating, reading, updating, and deleting. |  
| **Authors**        | `/authors`                | `GET`, `POST`, `PUT`, `DELETE` | Manage author records.                 |  
| **Characters**     | `/characters`             | `GET`, `POST`, `PUT`, `DELETE` | Manage character records.              |  
| **Tags**           | `/tags`                   | `GET`, `POST`, `PUT`, `DELETE` | Manage tag records.                    |  
| **Anime Types**    | `/type_animes`            | `GET`, `POST`, `PUT`, `DELETE` | Manage anime type records.             |  

#### Example:

##### Fetch All Animes
**Endpoint**:  
`GET /animes`

**Headers**:
```http  
Authorization: Bearer your_jwt_token  
Content-Type: application/json 
```

**Response Example:**:

```json
[  
  {  
    "id": 1,  
    "name": "One Piece",  
    "slug": "one-piece",  
    "content": "A story about pirates and adventures.",  
    "type_anime": "/api/type_animes/1",  
    "tag": ["/api/tags/1"],  
    "author": "/api/authors/1",  
    "firstBroadcast": "1999-10-20",  
    "episodes": 1000  
  }  
]  
```

---

## 📖 API Documentation

This API provides access to resources such as animes, authors, characters, tags, and anime types. Below is a brief overview of the available operations and usage.

### Base URL

For local development:  `http://127.0.0.1:8000/api`

