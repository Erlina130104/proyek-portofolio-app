<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SupportTicketController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CustomerFeedbackController;

/*
|--------------------------------------------------------------------------
| API Routes - WITH AUTHENTICATION
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // ========================================
    // PUBLIC ROUTES (No Authentication Required)
    // ========================================
    
    // Authentication
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    
    // Chatbot (Public)
    Route::post('/chatbot/chat', [ChatbotController::class, 'chat']);

    // ========================================
    // PROTECTED ROUTES (Authentication Required)
    // ========================================
    Route::middleware('auth:sanctum')->group(function () {
        
        // Auth
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'me']);

        // DASHBOARD
        Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
        Route::get('/dashboard/sales-chart', [DashboardController::class, 'salesChart']);
        Route::get('/dashboard/low-stock-alerts', [DashboardController::class, 'lowStockAlerts']);
        Route::get('/dashboard/financial-summary', [DashboardController::class, 'financialSummary']);

        // PRODUCTS
        Route::get('/products', [ProductController::class, 'index']);
        Route::post('/products', [ProductController::class, 'store']);
        Route::get('/products/categories/list', [ProductController::class, 'categories']);
        Route::get('/products/{id}', [ProductController::class, 'show']);
        Route::put('/products/{id}', [ProductController::class, 'update']);
        Route::delete('/products/{id}', [ProductController::class, 'destroy']);
        Route::patch('/products/{id}/stock', [ProductController::class, 'updateStock']);

        // TRANSACTIONS
        Route::get('/transactions', [TransactionController::class, 'index']);
        Route::post('/transactions', [TransactionController::class, 'store']);
        Route::get('/transactions/statistics/summary', [TransactionController::class, 'statistics']);
        Route::get('/transactions/{id}', [TransactionController::class, 'show']);
        Route::patch('/transactions/{id}/status', [TransactionController::class, 'updateStatus']);
        Route::get('/transactions/{id}/invoice', [TransactionController::class, 'invoice']);

        // EMPLOYEES
        Route::get('/employees', [EmployeeController::class, 'index']);
        Route::post('/employees', [EmployeeController::class, 'store']);
        Route::get('/employees/{id}', [EmployeeController::class, 'show']);
        Route::put('/employees/{id}', [EmployeeController::class, 'update']);
        Route::delete('/employees/{id}', [EmployeeController::class, 'destroy']);

        // ATTENDANCE
        Route::get('/attendances', [AttendanceController::class, 'index']);
        Route::post('/attendances', [AttendanceController::class, 'store']);
        Route::post('/attendances/record', [AttendanceController::class, 'record']);
        Route::get('/attendances/statistics', [AttendanceController::class, 'statistics']);
        Route::get('/attendances/today-summary', [AttendanceController::class, 'todaySummary']);

        // SUPPORT TICKETS
        Route::get('/support-tickets', [SupportTicketController::class, 'index']);
        Route::post('/support-tickets', [SupportTicketController::class, 'store']);
        Route::get('/support-tickets/statistics/summary', [SupportTicketController::class, 'statistics']);
        Route::get('/support-tickets/categories/list', [SupportTicketController::class, 'categories']);
        Route::get('/support-tickets/{id}', [SupportTicketController::class, 'show']);
        Route::put('/support-tickets/{id}', [SupportTicketController::class, 'update']);
        Route::delete('/support-tickets/{id}', [SupportTicketController::class, 'destroy']);
        Route::patch('/support-tickets/{id}/assign', [SupportTicketController::class, 'assign']);
        Route::patch('/support-tickets/{id}/status', [SupportTicketController::class, 'updateStatus']);

        // CUSTOMER FEEDBACKS
        Route::get('/feedbacks/insights/analysis', [CustomerFeedbackController::class, 'insights']);
        Route::get('/feedbacks', [CustomerFeedbackController::class, 'index']);
        Route::post('/feedbacks', [CustomerFeedbackController::class, 'store']);
        Route::get('/feedbacks/{id}', [CustomerFeedbackController::class, 'show']);
        Route::put('/feedbacks/{id}', [CustomerFeedbackController::class, 'update']);
        Route::delete('/feedbacks/{id}', [CustomerFeedbackController::class, 'destroy']);
        Route::patch('/feedbacks/{id}/status', [CustomerFeedbackController::class, 'updateStatus']);
    });
});

// ========================================
// HEALTH CHECK (Public)
// ========================================
Route::get('/health', function () {
    return response()->json([
        'status' => 'OK',
        'message' => 'Smart Community ERP API is running',
        'version' => '1.0.0',
        'timestamp' => now()->toISOString(),
        'environment' => 'PRODUCTION - Authentication enabled'
    ]);
});