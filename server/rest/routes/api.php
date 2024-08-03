<?php

use App\Http\Controllers\AssignmentController;
use App\Http\Controllers\ContentController;
use App\Http\Controllers\ResourceController;
use App\Http\Controllers\SelectedSubjectController;
use App\Http\Controllers\SolutionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SubjectResourceController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\TestResourceController;
use App\Http\Controllers\UserController;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Ramsey\Uuid\Uuid;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// todo: get rid of dev routes for prod

Route::prefix("v1")->group(function(){
    Route::get("/healthcheck",function(){
        return response()->json(["message"=>"hello from kaizenklass"]);
    });
    Route::get("get-assignments",[AssignmentController::class,"getAssignments"]);   
    Route::post("register-contributor",[UserController::class,"registerContributor"]);
    Route::post("login",[UserController::class,"login"]);
    Route::get("user-data",[UserController::class,"userData"]); // No regular users
    Route::get("get-subjects",[SubjectController::class,"getSubjects"]);
    Route::get("get-subject-assignments/{subjectUuid}",[AssignmentController::class,"getAssignmentsBySubject"]); // used in subjects page
    Route::get("get-assignment-solutions/{assignmentUuid}",[AssignmentController::class,"getSolutionsByAssignment"]); // for each assignment
    Route::get("get-assignment-subjects",[AssignmentController::class,"getAssignmentsWithSubjects"]); // for assignments page
    Route::get("get-deadlines",[AssignmentController::class,"getAssignmentsWithDeadline"]);
    Route::get("get-resources",[ResourceController::class,"getResources"]);
    
    
    // * contributor routes
    Route::middleware(["auth:sanctum","checkIsOnboard"])->group(function(){
        Route::post("logout",[UserController::class,"logout"]);
        Route::post("add-solution",[SolutionController::class,"addSolution"]);
        Route::delete("delete-own-account/{userUuid}", [UserController::class, "deleteOwnAccount"]);
        Route::delete("delete-own-solution/{solutionUuid}", [SolutionController::class, "deleteOwnSolution"]);
        Route::put("edit-own-solution/{solutionUuid}", [SolutionController::class, "updateOwnSolution"]);
    });
    
    // * crosschecker routes
    Route::middleware(["auth:sanctum","checkCrosschecker","checkIsOnboard"])->group(function(){ 
        Route::post("add-assignment",[AssignmentController::class,"addAssignment"]);
        Route::put("edit-assignment/{assignmentUuid}", [AssignmentController::class, "editAssignment"]);
        Route::delete("delete-assignment/{assignmentUuid}", [AssignmentController::class, "deleteAssignment"]);
    });
    
    // * admin routes
    Route::middleware(["auth:sanctum","checkAdmin"])->group(function(){
        Route::post("register-crosschecker",[UserController::class,"registerCrosschecker"]); // * remove in prod, use the panel
        Route::put("promote/{userUuid}",[UserController::class,"promote"]);
        Route::put("demote/{userUuid}",[UserController::class,"demote"]);
        
        Route::delete("delete-resource/{resourceUuid}", [ResourceController::class, "deleteResource"]);
        Route::post("add-resource",[ResourceController::class,"addResource"]);
        Route::post("add-tracker-resource",[ResourceController::class,"addTrackerResource"]);
        Route::put("edit-resource/{resourceUuid}",[ResourceController::class,"editResource"]);
        // todo: delete redundant routes
        Route::post("add-subject",[SubjectController::class,"addSubject"]);
        Route::delete("delete-subject/{subjectUuid}", [SubjectController::class, "deleteSubject"]);
    });
});

// todo: add search for subjects
Route::prefix("v2")->group(function(){
    Route::get("get-subjects",[SubjectController::class,"getSubjectsV2"]);
    Route::prefix("get")->group(function(){
        Route::get("tests",[TestController::class,"getTests"]);
        Route::get("subjects/{uuid}/tests",[TestController::class,"getTestsBySubject"]);
        Route::get("subjects/{uuid}/resources",[SubjectResourceController::class,"getSubjectResources"]);
        Route::get("test/{uuid}",[TestController::class,"getTest"]);
        Route::get("deadlines",[TestController::class,"getDeadlines"]);
        
    });
    
    Route::middleware(["auth:sanctum"])->group(function(){
        Route::post("onboard",[SelectedSubjectController::class,"onboard"]);
    });
    Route::middleware(["auth:sanctum","checkIsOnboard"])->group(function(){
        Route::prefix("get")->group(function(){
            Route::get("selected-subjects",[SelectedSubjectController::class,"getSelectedSubjects"]);
            Route::get("selected-subjects/all",[SelectedSubjectController::class,"getAllSelectedSubjects"]);
            Route::get("selected-subjects/assignments",[AssignmentController::class,"getAssignmentsWithSelectedSubjects"]);
            
            Route::get("subjects/search/{query}",[SubjectController::class,"searchSubjects"]);
            Route::get("selected-subjects/search/{query}",[SelectedSubjectController::class,"searchSelectedSubjects"]);
            
            Route::get("selected-subjects/tests",[TestController::class,"getTestsWithSelectedSubjects"]);
        });
        Route::prefix("add")->group(function(){
            Route::post("selected-subjects",[SelectedSubjectController::class,"selectSubjects"]);
            Route::post("selected-subject",[SelectedSubjectController::class,"selectSubject"]);
            
            Route::post("subject-resource",[SubjectResourceController::class,"addSubjectResource"]);
            Route::post("test-resource",[TestResourceController::class,"createTestResource"]);
        });
        Route::prefix("update")->group(function(){
            Route::put("test-resource/{uuid}",[TestResourceController::class,"updateOwnTestResource"]); 
            
            Route::put("subject-resource/{subjectResourceUuid}",[SubjectResourceController::class,"updateSubjectResource"]); 
        });
        Route::prefix("delete")->group(function(){
            Route::post("selected-subject/{uuid}",[SelectedSubjectController::class,"removeSelectedSubject"]);
            Route::post("selected-subjects",[SelectedSubjectController::class,"removeAllSelectedSubject"]);
            
            Route::delete("subject-resource/{subjectResourceUuid}",[SubjectResourceController::class,"removeSubjectResources"]); 

            Route::delete("test-resource/{uuid}", [TestResourceController::class, "deleteOwnTestResource"]);
            
        });
    });
    Route::middleware(["auth:sanctum","checkCrosschecker"])->group(function(){
        Route::prefix("add")->group(function(){
            Route::post("test",[TestController::class,"createTest"]);
        });
        Route::prefix("get")->group(function(){
        });
        Route::prefix("delete")->group(function(){
            Route::delete("test/{uuid}",[TestController::class,"deleteTest"]); 
        });
        Route::prefix("update")->group(function(){
            Route::put("test/{uuid}",[TestController::class,"updateTest"]); 
        });
    });
});
