<?php

namespace App\Services;

use App\Models\MovieSearch;
use Illuminate\Support\Facades\Http;

class MovieService
{
    private $websiteUrl = 'http://www.omdbapi.com';

    public function searchMovies(string $searchRequest, ?string $type = null, ?int $year = null, ?bool $newSearch = false, ?int $page = 1): array
    {
        $response = $this->getMovieData($searchRequest, $type, $year, $page);
        if($newSearch) {
            $this->storeMovieSearch($searchRequest);
            $this->cleanupOldSearches();
        }
        $response['current_page'] = $page;
        $response['next'] =
            $response['Response'] === 'True'
                && (isset($response['totalResults']) ? ($response['totalResults'] > ($page * 10)) : false)
                    ? $page + 1
                    : null;
        $response['prev'] = $page > 1 ? $page - 1 : null;

        return $response;
    }

    public function getMovieDetails(string $imdbID): array
    {
        return Http::get($this->websiteUrl, [
            'apikey' => env('OMDB_API_KEY'),
            'i' => $imdbID,
            'plot' => 'full',
        ])->json();
    }

    public function getLatestSearches()
    {
        return MovieSearch::orderBy('created_at', 'desc')->get();
    }

    private function getMovieData(string $searchRequest, ?string $type = null, ?int $year = null, ?int $page = 1): array
    {
        return Http::get($this->websiteUrl, [
            'apikey' => env('OMDB_API_KEY'),
            's' => $searchRequest,
            'type' => $type ?? '',
            'y' => $year ?? '',
            'page' => $page,
        ])->json();
    }

    private function storeMovieSearch(string $searchRequest): void
    {
        $search = new MovieSearch();
        $search->ip_address = request()->ip();
        $search->search_request = $searchRequest;
        $search->save();
    }

    private function cleanupOldSearches(): void
    {
        $latestIds = MovieSearch::latest('created_at')
            ->limit(5)
            ->pluck('id');

        MovieSearch::whereNotIn('id', $latestIds)->delete();
    }
}
