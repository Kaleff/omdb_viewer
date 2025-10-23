<?php

namespace Tests\Unit;

use App\Models\MovieSearch;
use App\Services\MovieService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class MovieServiceTest extends TestCase
{
    use RefreshDatabase;

    private MovieService $movieService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->movieService = new MovieService();
        config(['app.omdb_api_key' => 'test-api-key']);
    }

    public function test_search_movies_returns_formatted_response_with_pagination()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [
                    ['Title' => 'Batman', 'Year' => '2022', 'imdbID' => 'tt1877830']
                ],
                'totalResults' => '25'
            ])
        ]);

        // Act
        $result = $this->movieService->searchMovies('batman', null, null, false, 2);

        // Assert
        $this->assertEquals('True', $result['Response']);
        $this->assertEquals(2, $result['current_page']);
        $this->assertEquals(3, $result['next']); // Should have next page
        $this->assertEquals(1, $result['prev']); // Should have previous page
    }

    public function test_search_movies_with_new_search_true_stores_search_and_cleanups()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '0'
            ])
        ]);

        $this->assertDatabaseCount('movie_searches', 0);

        // Act
        $this->movieService->searchMovies('batman', null, null, true, 1);

        // Assert
        $this->assertDatabaseCount('movie_searches', 1);
        $this->assertDatabaseHas('movie_searches', [
            'search_request' => 'batman'
        ]);
    }

    public function test_search_movies_with_new_search_false_does_not_store_search()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '0'
            ])
        ]);

        $this->assertDatabaseCount('movie_searches', 0);

        // Act
        $this->movieService->searchMovies('batman', null, null, false, 1);

        // Assert
        $this->assertDatabaseCount('movie_searches', 0);
    }

    public function test_search_movies_calculates_next_page_correctly()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '25' // 3 pages (10 results per page)
            ])
        ]);

        // Act
        $result = $this->movieService->searchMovies('batman', null, null, false, 2);

        // Assert
        $this->assertEquals(3, $result['next']); // Should have next page
    }

    public function test_search_movies_calculates_prev_page_correctly()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '25'
            ])
        ]);

        // Act
        $result = $this->movieService->searchMovies('batman', null, null, false, 3);

        // Assert
        $this->assertEquals(2, $result['prev']); // Should have previous page
    }

    public function test_search_movies_handles_first_page_prev_null()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '25'
            ])
        ]);

        // Act
        $result = $this->movieService->searchMovies('batman', null, null, false, 1);

        // Assert
        $this->assertNull($result['prev']); // First page should not have previous
        $this->assertEquals(2, $result['next']); // Should have next page
    }

    public function test_search_movies_handles_last_page_next_null()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '20' // Exactly 2 pages
            ])
        ]);

        // Act
        $result = $this->movieService->searchMovies('batman', null, null, false, 2);

        // Assert
        $this->assertNull($result['next']); // Last page should not have next
        $this->assertEquals(1, $result['prev']); // Should have previous page
    }

    public function test_get_latest_searches_returns_ordered_results()
    {
        // Arrange
        $oldSearch = MovieSearch::factory()->create([
            'search_request' => 'old movie',
            'created_at' => now()->subHours(2)
        ]);

        $newSearch = MovieSearch::factory()->create([
            'search_request' => 'new movie',
            'created_at' => now()->subHour()
        ]);

        // Act
        $result = $this->movieService->getLatestSearches();

        // Assert
        $this->assertCount(2, $result);
        $this->assertEquals('new movie', $result->first()->search_request);
        $this->assertEquals('old movie', $result->last()->search_request);
    }

    public function test_cleanup_old_searches_keeps_only_5_latest()
    {
        // Arrange - Create 8 searches
        MovieSearch::factory()->count(8)->create();
        $this->assertDatabaseCount('movie_searches', 8);

        // Act
        $this->movieService->searchMovies('test', null, null, true, 1);

        // Assert - Should have 5 searches (including the new one)
        $this->assertDatabaseCount('movie_searches', 5);
    }

    public function test_cleanup_old_searches_does_nothing_when_less_than_5_records()
    {
        // Arrange - Create only 3 searches
        MovieSearch::factory()->count(3)->create();
        $this->assertDatabaseCount('movie_searches', 3);

        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '0'
            ])
        ]);

        // Act
        $this->movieService->searchMovies('test', null, null, true, 1);

        // Assert - Should have 4 searches (3 + new one)
        $this->assertDatabaseCount('movie_searches', 4);
    }

    public function test_search_movies_with_type_filter()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '0'
            ])
        ]);

        // Act
        $this->movieService->searchMovies('batman', 'movie', null, false, 1);

        // Assert
        Http::assertSent(function ($request) {
            return $request['type'] === 'movie';
        });
    }

    public function test_search_movies_with_year_filter()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '0'
            ])
        ]);

        // Act
        $this->movieService->searchMovies('batman', null, 2022, false, 1);

        // Assert
        Http::assertSent(function ($request) {
            return $request['y'] === 2022;
        });
    }

    public function test_search_movies_handles_api_failure()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'False',
                'Error' => 'Movie not found!'
            ])
        ]);

        // Act
        $result = $this->movieService->searchMovies('nonexistent', null, null, false, 1);

        // Assert
        $this->assertEquals('False', $result['Response']);
        $this->assertEquals('Movie not found!', $result['Error']);
    }

    public function test_search_movies_stores_correct_ip_address()
    {
        // Arrange
        Http::fake([
            'http://www.omdbapi.com*' => Http::response([
                'Response' => 'True',
                'Search' => [],
                'totalResults' => '0'
            ])
        ]);

        $this->app->instance('request', request()->merge(['REMOTE_ADDR' => '127.0.0.1']));

        // Act
        $this->movieService->searchMovies('batman', null, null, true, 1);

        // Assert
        $this->assertDatabaseHas('movie_searches', [
            'search_request' => 'batman',
            'ip_address' => '127.0.0.1'
        ]);
    }
}
