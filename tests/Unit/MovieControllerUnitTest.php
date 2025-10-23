<?php

namespace Tests\Unit;

use App\Http\Controllers\MovieController;
use App\Http\Requests\MovieSearchRequest;
use App\Services\MovieService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Mockery;
use Tests\TestCase;

class MovieControllerUnitTest extends TestCase
{
    public function test_search_calls_movie_service_with_correct_parameters()
    {
        $mockService = Mockery::mock(MovieService::class);
        $mockRequest = Mockery::mock(MovieSearchRequest::class);

        $mockRequest->shouldReceive('input')
            ->with('search')
            ->andReturn('batman');

        $mockRequest->shouldReceive('input')
            ->with('type')
            ->andReturn('movie');

        $mockRequest->shouldReceive('input')
            ->with('year')
            ->andReturn(2022);

        $mockRequest->shouldReceive('input')
            ->with('page', 1)
            ->andReturn(2);

        $mockRequest->shouldReceive('boolean')
            ->with('newSearch', false)
            ->andReturn(true);

        $expectedMovies = [
            'Response' => 'True',
            'Search' => [['Title' => 'Batman']],
            'current_page' => 2
        ];

        $mockService->shouldReceive('searchMovies')
            ->once()
            ->with('batman', 'movie', 2022, true, 2)
            ->andReturn($expectedMovies);

        $controller = new MovieController($mockService);
        $response = $controller->search($mockRequest);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('search-results', $response->name());
        $this->assertEquals($expectedMovies, $response->getData()['movies']);
    }

    public function test_search_handles_service_exception()
    {
        $mockService = Mockery::mock(MovieService::class);
        $mockRequest = Mockery::mock(MovieSearchRequest::class);

        $mockRequest->shouldReceive('input')
            ->with('search')
            ->andReturn('batman');

        $mockRequest->shouldReceive('input')
            ->with('type')
            ->andReturn(null);

        $mockRequest->shouldReceive('input')
            ->with('year')
            ->andReturn(null);

        $mockRequest->shouldReceive('input')
            ->with('page', 1)
            ->andReturn(1);

        $mockRequest->shouldReceive('boolean')
            ->with('newSearch', false)
            ->andReturn(false);

        $mockService->shouldReceive('searchMovies')
            ->andThrow(new \Exception('API Error'));

        $controller = new MovieController($mockService);
        $response = $controller->search($mockRequest);

        $this->assertInstanceOf(View::class, $response);
        $movies = $response->getData()['movies'];
        $this->assertEquals('False', $movies['Response']);
        $this->assertStringContainsString('Unable to fetch movie data', $movies['Error']);
    }

    public function test_show_calls_get_movie_details_with_imdb_id()
    {
        $mockService = Mockery::mock(MovieService::class);
        $imdbId = 'tt1877830';

        $expectedDetails = [
            'Response' => 'True',
            'Title' => 'Batman',
            'imdbID' => $imdbId
        ];

        $mockService->shouldReceive('getMovieDetails')
            ->once()
            ->with($imdbId)
            ->andReturn($expectedDetails);

        $controller = new MovieController($mockService);
        $response = $controller->show($imdbId);

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('movie-details', $response->name());
        $this->assertEquals($expectedDetails, $response->getData()['movieDetails']);
    }

    public function test_show_handles_service_exception()
    {
        $mockService = Mockery::mock(MovieService::class);
        $imdbId = 'invalid-id';

        $mockService->shouldReceive('getMovieDetails')
            ->with($imdbId)
            ->andThrow(new \Exception('Movie not found'));

        $controller = new MovieController($mockService);
        $response = $controller->show($imdbId);

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertEquals(route('home'), $response->getTargetUrl());
    }

    public function test_index_calls_get_latest_searches()
    {
        $mockService = Mockery::mock(MovieService::class);
        $expectedSearches = collect([
            ['search_request' => 'batman', 'created_at' => now()],
            ['search_request' => 'superman', 'created_at' => now()->subHour()]
        ]);

        $mockService->shouldReceive('getLatestSearches')
            ->once()
            ->andReturn($expectedSearches);

        $controller = new MovieController($mockService);
        $response = $controller->index();

        $this->assertInstanceOf(View::class, $response);
        $this->assertEquals('home', $response->name());
        $this->assertEquals($expectedSearches, $response->getData()['recentSearches']);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
