# PowerPoster

PowerPoster is a project that allows you to create and retrieve posts through an API. Follow the steps below to initialize the project.

## Setup

1. **Web Server**: Set up an Apache web server and place all the project files in the root directory for web files. For example, if you're using XAMPP, the root directory would be the `htdocs` folder.

2. **Database**: Install MariaDB and create a database for the project. Once the database is created, run the SQL query provided in the `init_database.sql` file to initialize the necessary tables.

Congratulations! You have successfully set up the PowerPoster project.

## API Endpoints

The PowerPoster project provides the following API endpoints:

### Create a Post

**Endpoint**: `POST /api/posts`

This endpoint allows you to create a new post by sending a POST request to the specified URL. Provide the title and content of the post in the request body as a JSON object.

### Get a Post by ID

**Endpoint**: `GET /api/posts/{id}`

To retrieve a specific post, send a GET request to the above endpoint with the corresponding post ID. Replace `{id}` in the URL with the ID of the desired post.

### Get Posts by Pagination

**Endpoint**: `GET /api/posts?page=5&limit=5`

To retrieve multiple posts with pagination, send a GET request to the above endpoint. You can customize the pagination by specifying the `page` and `limit` parameters in the query string. In the example URL provided, the request will retrieve page 5 with a limit of 5 posts per page.

Feel free to explore and utilize these API endpoints in your PowerPoster project.

---

**Note**: For the `Create a Post` endpoint, you need to provide the title and content values in the request body as a JSON object. For the `Get a Post by ID` endpoint, replace `{id}` in the URL with the actual ID of the desired post. For the `Get Posts by Pagination` endpoint, customize the `page` and `limit` parameters in the URL as per your requirements. If these parameters are not provided, the default values are `page=1` and `limit=10`.
