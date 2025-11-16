<?php

// API Routes
$router->get('/api/users', [\App\Controllers\Api\UserController::class, 'index']);
$router->get('/api/users/{id}', [\App\Controllers\Api\UserController::class, 'show']);
$router->post('/api/users', [\App\Controllers\Api\UserController::class, 'store']);
$router->put('/api/users/{id}', [\App\Controllers\Api\UserController::class, 'update']);
$router->delete('/api/users/{id}', [\App\Controllers\Api\UserController::class, 'destroy']);

// Health check endpoint
$router->get('/api/health', function() {
    return $app->response->json(['status' => 'OK', 'timestamp' => time()]);
});