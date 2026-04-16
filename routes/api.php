<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\FeedController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ConnectionController;
use App\Http\Controllers\Api\GroupController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\BookmarkController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\EvaluationController;
use App\Http\Controllers\Api\SubmissionController;

Route::post('/register', [AuthController::class, 'register'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::post('/login', [AuthController::class, 'login'])
    ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class]);

Route::middleware(["auth:sanctum"])->group(function () {
    Route::get("/user", function (Request $request) { return $request->user(); });
    Route::post("/user/profile", [ProfileController::class, "update"]);
    Route::get("/search", [SearchController::class, "index"]);

    Route::get("/notifications", [NotificationController::class, "index"]);
    Route::post("/notifications/mark-read", [NotificationController::class, "markAsRead"]);
    
    Route::get("/bookmarks", [BookmarkController::class, "index"]);
    Route::post("/posts/{post}/bookmark", [BookmarkController::class, "toggle"]);
    
    Route::post("/report", [ReportController::class, "store"]);
    Route::get("/feed", [FeedController::class, "index"]);

    Route::post("/posts", [PostController::class, "store"]);
    Route::post("/posts/{post}/verify", [PostController::class, "verify"]);
    Route::post("/posts/{post}/like", [LikeController::class, "toggle"]);
    Route::post("/posts/{post}/comments", [CommentController::class, "store"]); 
    
    Route::post("/users/{user}/request", [ConnectionController::class, "request"]);
    Route::post("/users/{user}/accept", [ConnectionController::class, "accept"]);
    
    Route::post("/groups", [GroupController::class, "store"]);
    Route::get("/admin/stats", function () {
        return response()->json([
            "users_count" => \App\Models\User::count(),
            "posts_count" => \App\Models\Post::count(),
            "groups_count" => \App\Models\Group::count(),
            "users" => \App\Models\User::orderBy("created_at", "desc")->get(),
        ]);
    });
    
    Route::post("/groups/join/{code}", [GroupController::class, "join"]);
    Route::get("/groups/mine", [GroupController::class, "myGroups"]);
    Route::get("/groups", [GroupController::class, "index"]);
    Route::get("/groups/{group}", [GroupController::class, "show"]);
    Route::delete("/groups/{group}", [GroupController::class, "destroy"]);
    
    Route::get("/groups/{group}/evaluations", [EvaluationController::class, "index"]);
    Route::post("/groups/{group}/evaluations", [EvaluationController::class, "store"]);
    Route::post("/evaluations/{evaluation}/submissions", [SubmissionController::class, "store"]);
    Route::post("/submissions/{submission}/grade", [SubmissionController::class, "grade"]);

    // Teams / Agrupaciones
    Route::get("/groups/{group}/teams", [\App\Http\Controllers\Api\TeamController::class, "index"]);
    Route::post("/groups/{group}/teams", [\App\Http\Controllers\Api\TeamController::class, "store"]);
    Route::post("/teams/{team}/members", [\App\Http\Controllers\Api\TeamController::class, "addMember"]);
    Route::delete("/teams/{team}/members/{user}", [\App\Http\Controllers\Api\TeamController::class, "removeMember"]);
    Route::delete("/teams/{team}", [\App\Http\Controllers\Api\TeamController::class, "destroy"]);
});
