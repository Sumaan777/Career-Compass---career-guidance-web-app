<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Controller Imports
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\LogicOnboardingController;
use App\Http\Controllers\AIQuizController;
use App\Http\Controllers\CareerSuggestionController;
use App\Http\Controllers\ResumeController;
use App\Http\Controllers\RoadmapController;
use App\Http\Controllers\TypeProfileController;
use App\Http\Controllers\CareerTrendController;
use App\Http\Controllers\AIChatController;
use App\Http\Controllers\LiveInterviewController;
use App\Http\Controllers\JobRecommenderController;
use App\Http\Controllers\SkillGapController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\LearningResourceController;
use App\Http\Controllers\AcademicPathController;
use App\Http\Controllers\CareerReportController;
use App\Http\Controllers\CareerProgressController;
use App\Http\Controllers\AISimilarUserController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OtpController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\ContactController;
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'));
/*
|--------------------------------------------------------------------------
| user contact route through landing page
|--------------------------------------------------------------------------
*/
Route::post('/contact-submit', [ContactController::class, 'submit'])
    ->name('contact.submit');


Route::post('/profile/photo/remove', [ProfileController::class, 'removePhoto'])
    ->name('profile.photo.remove');

/*
|--------------------------------------------------------------------------
| AUTH ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
Route::get('/login', [LoginController::class,'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class,'login'])->name('login.store');

Route::get('/register', fn() => view('auth.signup'))->name('signup');
Route::post('/register', [RegisterController::class,'store'])->name('register');
});

/*
|--------------------------------------------------------------------------
| OTP ROUTES (NO AUTH REQUIRED)
|--------------------------------------------------------------------------
*/
Route::get('/verify-email-otp', [OtpController::class,'showOtpForm'])->name('otp.form');
Route::post('/verify-email-otp', [OtpController::class,'verifyOtp'])->name('otp.verify');
Route::post('/resend-email-otp', [OtpController::class,'resendOtp'])->name('otp.resend');
Route::get('/signup-otp-success', function () {
    return view('auth.otp-success');
})->name('otp.signup.success');

// Forgot Password - Request OTP
Route::get('/forgot-password',[ForgotPasswordController::class, 'showEmailForm'])
 ->name('password.forgot');

 Route::post('/forgot-password',[ForgotPasswordController::class, 'sendOtp'])
 ->name('password.sendOtp');


// OTP verification for forgot password
Route::get('/forgot-password/verify',[ForgotPasswordController::class, 'showOtpForm'])
->name('password.otp.form');

Route::post('/forgot-password/verify', [OtpController::class, 'verifyOtp'])
    ->name('password.otp.verify');

Route::post('/forgot-password/verify',[ForgotPasswordController::class, 'verifyOtp']
)->name('password.otp.verify');


// Reset password form
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])
    ->name('password.reset.form');

// Reset password submit
Route::post('/reset-password', [ForgotPasswordController::class, 'resetPassword'])
    ->name('password.reset.submit');


/*
|--------------------------------------------------------------------------
| SOCIAL AUTH
|--------------------------------------------------------------------------
*/
Route::prefix('auth')->group(function () {
    Route::get('/google', [SocialAuthController::class,'redirectToGoogle'])->name('google.redirect');
    Route::get('/google/callback', [SocialAuthController::class,'handleGoogleCallback']);
});

/*
|--------------------------------------------------------------------------
| AUTHENTICATED ROUTES
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | ONBOARDING (must be before ensureOnboarding middleware)
    |--------------------------------------------------------------------------
    */
    Route::get('/onboarding', [OnboardingController::class,'show'])->name('onboarding.show');
    Route::post('/onboarding', [OnboardingController::class,'store'])->name('onboarding.store');

    /*
    |--------------------------------------------------------------------------
    | PROTECTED ROUTES — user_type must be selected
    |--------------------------------------------------------------------------
    */
    Route::middleware(['ensureOnboarding'])->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class,'index'])->name('dashboard');

        // Logic onboarding
        Route::prefix('onboarding/logic')->group(function () {
            Route::get('/', [LogicOnboardingController::class,'show'])->name('onboarding.logic');
            Route::get('/fetch', [LogicOnboardingController::class,'fetch'])->name('onboarding.logic.fetch');
            Route::post('/store', [LogicOnboardingController::class,'store'])->name('onboarding.logic.store');
        });

        // AI Quiz
// ---------------- AI QUIZ ROUTES ----------------
Route::prefix('ai-quiz')->group(function () {

    // Start screen
    Route::get('/', fn() => view('dashboard.features.ai_quiz.start'))
        ->name('ai.quiz.start');

    // Take quiz page (same view as startPage)
    Route::get('/take', [AIQuizController::class, 'startPage'])
        ->name('ai.quiz.take');

    // Generate questions
    Route::get('/generate', [AIQuizController::class, 'generateQuestions'])
        ->name('ai.quiz.generate');

    // Fetch saved questions (AJAX)
    Route::get('/questions', [AIQuizController::class, 'fetchQuestions'])
        ->name('ai.quiz.fetch');

    // Save answers (POST)
    Route::post('/answers', [AIQuizController::class, 'saveAnswers'])
        ->name('ai.quiz.answers');

    // Final AI result (AJAX)
    Route::post('/final', [AIQuizController::class, 'generateFinalResult'])
        ->name('ai.quiz.final');

});


        // Career features
        Route::prefix('career')->group(function () {
            Route::get('/suggestions', [CareerSuggestionController::class,'show'])->name('career.suggestions');
            Route::get('/roadmap', [RoadmapController::class,'index'])->name('career.roadmap');
            Route::view('/skillgap', 'dashboard.features.skillgap')->name('career.skillgap');
            Route::view('/resume', 'dashboard.features.resume')->name('career.resume');
            Route::view('/interview', 'dashboard.features.interview')->name('career.interview');
            Route::get('/tracker', [CareerProgressController::class,'index'])->name('career.tracker');
        });

        // Interview Simulator
        Route::get('/interview', [LiveInterviewController::class,'singlePage'])->name('interview.page');
        Route::post('/interview/start', [LiveInterviewController::class,'start'])->name('interview.start');
        Route::post('/interview/chat/{session}', [LiveInterviewController::class,'chat'])->name('interview.chat');

        // Profile
        Route::prefix('profile')->group(function () {
            Route::post('/update', [ProfileController::class,'update'])->name('profile.update');
            Route::post('/education/store', [ProfileController::class,'addEducation'])->name('education.store');
            Route::post('/experience/store', [ProfileController::class,'addExperience'])->name('experience.store');
            Route::post('/highschool/save', [TypeProfileController::class,'saveHighSchool'])->name('profile.highschool.save');
            Route::post('/university/save', [TypeProfileController::class,'saveUniversity'])->name('profile.university.save');
            Route::post('/graduate/save', [TypeProfileController::class,'saveGraduate'])->name('profile.graduate.save');
            Route::post('/switcher/save', [TypeProfileController::class,'saveSwitcher'])->name('profile.switcher.save');
            Route::post('/undecided/save', [TypeProfileController::class,'saveUndecided'])->name('profile.undecided.save');
            Route::post('/skills/add', [SkillController::class,'store'])->name('skills.store');
            Route::put('/skills/{skill}', [SkillController::class,'update'])->name('skills.update');
            Route::delete('/skills/{skill}', [SkillController::class,'destroy'])->name('skills.delete');
        });

        Route::post('/profile/photo/update', [ProfileController::class, 'updateProfilePhoto'])
    ->name('profile.photo.update');


        // Resume
        Route::get('/resume', [ResumeController::class,'show'])->name('career.resume');
        Route::post('/resume/upload', [ResumeController::class,'upload'])->name('career.resume.upload');
        Route::post('/career/resume/analyze', [ResumeController::class,'analyze'])->name('career.resume.analyze');
        Route::delete('/career/resume/delete', [ResumeController::class,'delete'])->name('career.resume.delete');

        // Roadmap
        Route::post('/roadmap/generate', [RoadmapController::class,'generate'])->name('roadmap.generate');
        Route::get('/career/search', [RoadmapController::class,'searchCareers'])->name('career.search');

        // Career Trends
        Route::get('/career/trends', [CareerTrendController::class,'show'])->name('career.trends');
        Route::post('/career/trends/refresh', [CareerTrendController::class,'refresh'])->name('career.trends.refresh');

        // AI Chat
        Route::get('/career/chat', [AIChatController::class,'index'])->name('chat.index');
        Route::post('/career/chat/send', [AIChatController::class,'send'])->name('chat.send');

        // Job recommender
        Route::get('/jobs/recommender', [JobRecommenderController::class,'index'])->name('jobs.recommender');
        Route::post('/jobs/recommend', [JobRecommenderController::class,'recommend'])->name('jobs.recommend');
        Route::get('/jobs/recommend/history', [JobRecommenderController::class,'history'])->name('jobs.recommend.history');

        // Skill gap
        Route::get('/skill-gap', [SkillGapController::class,'index'])->name('skill-gap.index');
        Route::post('/skill-gap/analyze', [SkillGapController::class,'analyze'])->name('skill-gap.analyze');

        // Learning resources
        Route::get('/learning-resources', [LearningResourceController::class,'index'])->name('learning.resources');
        Route::post('/learning-resources/generate', [LearningResourceController::class,'generate'])->name('learning.resources.generate');

        // Academic Path
        Route::get('/academic-path', [AcademicPathController::class,'index'])->name('academic.path');
        Route::post('/academic-path/validate', [AcademicPathController::class,'validatePath'])->name('academic.path.validate');

        // Career Report
        Route::get('/career-report', [CareerReportController::class,'index'])->name('career.report');
        Route::post('/career-report/generate', [CareerReportController::class,'generate'])->name('career.report.generate');
        Route::get('/career-report/view/{id}', [CareerReportController::class,'view'])->name('career.report.view');
        Route::delete('/career-report/{id}/delete', [CareerReportController::class,'delete'])->name('career.report.delete');

        // Progress Tracker
        Route::post('/career/tracker/task/add', [CareerProgressController::class,'addTask'])->name('career.tracker.task.add');
        Route::post('/career/tracker/task/toggle/{id}', [CareerProgressController::class,'toggleTask'])->name('career.tracker.task.toggle');
        Route::post('/career/tracker/add-from-roadmap', [CareerProgressController::class,'addFromRoadmap'])->name('career.tracker.addFromRoadmap');
        Route::post('/career/tasks/generate-ai', [CareerProgressController::class,'generateTasksFromAI'])->name('career.tasks.generateAI');
        Route::delete('/career/tracker/task/{id}/delete', [CareerProgressController::class,'deleteTask'])->name('career.tracker.task.delete');

        // Similar Users
        Route::get('/dashboard/similar-users', [AISimilarUserController::class,'fetchAjax'])->name('similar.users.ajax');

        // Settings
        Route::get('/settings', [SettingsController::class,'index'])->name('settings');
        Route::get('/profile/settings', [ProfileController::class,'settings'])->name('profile.settings');

        // Notifications
        Route::get('/notifications', [NotificationController::class,'index'])->name('notifications.index');
        Route::post('/notifications/mark-all-read', [NotificationController::class,'markAllRead'])->name('notifications.markAllRead');

        // AI Demo
        Route::post('/ai-demo/chat', [AIChatController::class,'demo'])->name('ai.demo.chat');

        // Search
        Route::get('/dashboard/search', [DashboardController::class,'search']);
    });
        /*
    |--------------------------------------------------------------------------
    | Forgot Password
    |--------------------------------------------------------------------------
    */

    /*
    |--------------------------------------------------------------------------
    | LOGOUT
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', [LoginController::class,'logout'])->name('logout');
});
