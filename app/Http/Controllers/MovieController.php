<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieSearchRequest;
use App\Models\MovieSearch;
use App\Services\MovieService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    private MovieService $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function search(MovieSearchRequest $request)
    {
        // Get search parameters
        $searchRequest = $request->input('search');
        $type = $request->input('type');
        $year = $request->input('year');
        $page = $request->input('page', 1);
        $newSearch = $request->boolean('newSearch', false);

        try {
            $movies = $this->movieService->searchMovies(
                searchRequest: $searchRequest,
                type: $type,
                year: $year,
                newSearch: $newSearch,
                page: $page
            );

            return view('search-results', compact('movies'));
        } catch (\Exception $e) {
            // Handle API errors gracefully
            $movies = [
                'Response' => 'False',
                'Error' => 'Unable to fetch movie data. Please try again later.'
            ];

            return view('search-results', compact('movies'))
                ->with('error', 'Search failed: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $recentSearches = $this->movieService->getLatestSearches();

        return view('home', compact('recentSearches'));
    }

    public function show(string $imdbID)
    {
        try {
            $movieDetails = $this->movieService->getMovieDetails($imdbID);

            return view('movie-details', compact('movieDetails'));
        } catch (\Exception $e) {
            return redirect()->route('home')
                ->with('error', 'Unable to fetch movie details. Please try again later.');
        }
    }
}
