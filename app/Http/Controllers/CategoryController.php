<?php
/**
 * NOTICE OF LICENSE.
 *
 * UNIT3D Community Edition is open-sourced software licensed under the GNU Affero General Public License v3.0
 * The details is bundled with this project in the file LICENSE.txt.
 *
 * @project    UNIT3D Community Edition
 *
 * @author     HDVinnie <hdinnovations@protonmail.com>
 * @license    https://www.gnu.org/licenses/agpl-3.0.en.html/ GNU Affero General Public License v3.0
 */

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\PersonalFreeleech;
use App\Models\Torrent;
use Illuminate\Http\Request;

/**
 * @see \Tests\Feature\Http\Controllers\CategoryControllerTest
 */
class CategoryController extends Controller
{
    /**
     * Display All Categories.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $categories = Category::withCount('torrents')->get()->sortBy('position');

        return \view('category.index', ['categories' => $categories]);
    }

    /**
     * Show A Category.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Category     $id
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        $movieScrapper = new \App\Services\MovieScrapper(\config('api-keys.tmdb'), \config('api-keys.tvdb'), \config('api-keys.omdb'));
        $category = Category::select(['id', 'name'])->findOrFail($id);
        $torrents = Torrent::with(['user', 'category', 'type'])->withCount(['thanks', 'comments'])->where('category_id', '=', $id)->orderBy('sticky', 'desc')->latest()->paginate(25);
        $personal_freeleech = PersonalFreeleech::where('user_id', '=', $user->id)->first();

        return \view('category.show', [
            'client'             => $movieScrapper,
            'torrents'           => $torrents,
            'user'               => $user,
            'category'           => $category,
            'personal_freeleech' => $personal_freeleech,
        ]);
    }
}
