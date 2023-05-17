    <?php

    use App\Http\Controllers\GithubController;
    use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/repos', [GithubController::class, 'index']);

Route::get('/repos/next', [GithubController::class, 'goToNextPage'])->name('nextPage');
Route::get('/repos/prev', [GithubController::class, 'goToPreviousPage'])->name('previousPage');

Route::get('/selectedRepo', [GithubController::class, 'openRepo'])->name('callControllerMethod');
Route::get('/downloadRepo', [GithubController::class, 'downloadRepo'])->name('downloadZip');

