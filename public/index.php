<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Selective\BasePath\BasePathMiddleware;
use Ramsey\Uuid\Uuid;

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/db.php';

$app = AppFactory::create();
$app->add(new BasePathMiddleware($app));

$app->get('/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write("Hello there!");
    return $response;
});

// Define a route to create a post
$app->post('/api/posts', function (Request $request, Response $response) {
    // Retrieve the post data from the request body
    $jsonData = $request->getBody()->getContents();
    $postData = json_decode($jsonData, true);

    $missingFields = [];
    if (empty($postData['title'])) {
        $missingFields[] = 'title';
    }
    if (empty($postData['content'])) {
        $missingFields[] = 'content';
    }

    if (!empty($missingFields)) {
        // Return an error response with a 400 Bad Request status code
        $errorMessage = 'You are missing the following required field(s): ' . implode(', ', $missingFields);
        $response->getBody()->write(json_encode(['error' => $errorMessage]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    $pdo = establishDBConnection();

    $userId = Uuid::uuid4()->toString();
    $title = $postData['title'];
    $content = $postData['content'];

    $insertPostSQL = "INSERT INTO posts (userId, Title, Content) VALUES (:userId, :title, :content)";
    $statement = $pdo->prepare($insertPostSQL);
    $statement->bindParam(':userId', $userId);
    $statement->bindParam(':title', $title);
    $statement->bindParam(':content', $content);
    $statement->execute();

    $postId = $pdo->lastInsertId();

    $responseData = [
        'message' => 'Post created successfully',
        'id' => $postId
    ];

    $response->getBody()->write(json_encode($responseData));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});


// Define a route to fetch a post by ID
$app->get('/api/posts/{id}', function (Request $request, Response $response, array $args) {
    $postId = $args['id'];
    $pdo = establishDBConnection();

    $getPostSQL = "SELECT userId, title, content FROM posts WHERE id = :postId";
    $statement = $pdo->prepare($getPostSQL);
    $statement->bindParam(':postId', $postId);
    $statement->execute();

    $post = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$post) {
        // Return a 404 response if the post with the given ID was not found
        $errorResponse = [
            'error' => 'Post not found'
        ];

        $response->getBody()->write(json_encode($errorResponse));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }

    $responseData = [
        'userId' => $post['userId'],
        'title' => $post['title'],
        'content' => $post['content']
    ];

    $response->getBody()->write(json_encode($responseData));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});


// Define a route to fetch multiple posts with pagination
$app->get('/api/posts', function (Request $request, Response $response) {
    // Retrieve pagination parameters from the query string
    $page = $request->getQueryParams()['page'] ?? 1;
    $limit = $request->getQueryParams()['limit'] ?? 10;

    // Calculate the offset based on the page and limit
    $offset = ($page - 1) * $limit;

    // Fetch posts based on pagination parameters from the database
    $pdo = establishDBConnection();

    $getPostsSQL = "SELECT id, userId, title, content FROM posts LIMIT :limit OFFSET :offset";
    $statement = $pdo->prepare($getPostsSQL);
    $statement->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $statement->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $statement->execute();

    $posts = $statement->fetchAll(PDO::FETCH_ASSOC);

    // Return the posts as a JSON response
    $responseData = [
        'page' => (int)$page,
        'limit' => (int)$limit,
        'data' => $posts
    ];

    $response->getBody()->write(json_encode($responseData));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

// Catch-all route for 404 errors
$app->map(['GET', 'POST', 'PUT', 'PATCH', 'DELETE'], '/{routes:.+}', function (Request $request, Response $response) {
    // Return a blank page with a 404 status code
    $response->getBody()->write('404 General kenobi not found');
    return $response->withStatus(404);
});

$app->run();
