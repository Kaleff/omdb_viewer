@extends('layouts.app')

@section('title', ($movieDetails['Title'] ?? 'Movie Details') . ' - ' . config('app.name', 'Laravel'))

@section('content')
    @if (isset($movieDetails) && $movieDetails['Response'] == 'True')
        <div class="mb-6">
            <div class="flex items-center gap-4 mb-4">
                <a href="javascript:history.back()"
                   class="inline-flex items-center px-4 py-2 border border-[#19140035] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm text-sm transition-all duration-200">
                    ← Back
                </a>
                <a href="{{ route('home') }}"
                   class="inline-flex items-center px-4 py-2 border border-[#19140035] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm text-sm transition-all duration-200">
                    New Search
                </a>
            </div>
        </div>

        <div class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden">
            <!-- Movie Header -->
            <div class="flex flex-col lg:flex-row gap-6 p-6 lg:p-8">
                <!-- Movie Poster -->
                <div class="flex-shrink-0">
                    @if ($movieDetails['Poster'] != 'N/A')
                        <img src="{{ $movieDetails['Poster'] }}" alt="{{ $movieDetails['Title'] }}"
                             class="w-64 h-96 object-cover rounded-lg shadow-lg mx-auto lg:mx-0">
                    @else
                        <div class="w-64 h-96 bg-[#dbdbd7] dark:bg-[#3E3E3A] rounded-lg flex items-center justify-center text-[#706f6c] dark:text-[#A1A09A] shadow-lg mx-auto lg:mx-0">
                            <div class="text-center">
                                <svg class="w-16 h-16 mx-auto mb-2 opacity-50" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd" />
                                </svg>
                                <p class="text-sm">No Poster Available</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Movie Information -->
                <div class="flex-1 min-w-0">
                    <div class="mb-4">
                        <h1 class="text-3xl lg:text-4xl font-bold text-[#1b1b18] dark:text-[#EDEDEC] mb-2">
                            {{ $movieDetails['Title'] }}
                        </h1>
                        <div class="flex flex-wrap gap-2 mb-3">
                            @if ($movieDetails['Year'] != 'N/A')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-sm text-xs font-medium bg-[#f8f9fa] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $movieDetails['Year'] }}
                                </span>
                            @endif
                            @if ($movieDetails['Rated'] != 'N/A')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-sm text-xs font-medium bg-[#f8f9fa] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $movieDetails['Rated'] }}
                                </span>
                            @endif
                            @if ($movieDetails['Runtime'] != 'N/A')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-sm text-xs font-medium bg-[#f8f9fa] dark:bg-[#3E3E3A] text-[#1b1b18] dark:text-[#EDEDEC]">
                                    {{ $movieDetails['Runtime'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Ratings -->
                    @if (isset($movieDetails['Ratings']) && !empty($movieDetails['Ratings']))
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-3">Ratings</h3>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach ($movieDetails['Ratings'] as $rating)
                                    <div class="bg-[#f8f9fa] dark:bg-[#3E3E3A] p-3 rounded-sm">
                                        <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mb-1">{{ $rating['Source'] }}</div>
                                        <div class="text-lg font-semibold text-[#1b1b18] dark:text-[#EDEDEC]">{{ $rating['Value'] }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Plot -->
                    @if ($movieDetails['Plot'] != 'N/A')
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-3">Plot</h3>
                            <p class="text-[#706f6c] dark:text-[#A1A09A] leading-relaxed">{{ $movieDetails['Plot'] }}</p>
                        </div>
                    @endif

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="space-y-4">
                            @if ($movieDetails['Genre'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Genre</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Genre'] }}</p>
                                </div>
                            @endif

                            @if ($movieDetails['Director'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Director</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Director'] }}</p>
                                </div>
                            @endif

                            @if ($movieDetails['Writer'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Writer</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Writer'] }}</p>
                                </div>
                            @endif

                            @if ($movieDetails['Actors'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Actors</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Actors'] }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="space-y-4">
                            @if ($movieDetails['Released'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Released</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Released'] }}</p>
                                </div>
                            @endif

                            @if ($movieDetails['Language'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Language</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Language'] }}</p>
                                </div>
                            @endif

                            @if ($movieDetails['Country'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Country</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Country'] }}</p>
                                </div>
                            @endif

                            @if ($movieDetails['Awards'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Awards</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['Awards'] }}</p>
                                </div>
                            @endif

                            @if (isset($movieDetails['totalSeasons']) && $movieDetails['totalSeasons'] != 'N/A')
                                <div>
                                    <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">Total Seasons</h4>
                                    <p class="text-[#706f6c] dark:text-[#A1A09A]">{{ $movieDetails['totalSeasons'] }}</p>
                                </div>
                            @endif

                            <div>
                                <h4 class="text-sm font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-1">IMDb ID</h4>
                                <p class="text-[#706f6c] dark:text-[#A1A09A] font-mono text-sm">{{ $movieDetails['imdbID'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else
        <!-- Error State -->
        <div class="text-center">
            <div class="max-w-md mx-auto">
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] border border-[#f53003] dark:border-[#F61500] p-6 rounded-lg mb-10">
                    <h3 class="font-medium text-[#f53003] dark:text-[#F61500] mb-2">Movie Not Found</h3>
                    <p class="text-[#f53003] dark:text-[#F61500] text-sm">
                        {{ $movieDetails['Error'] ?? 'Unable to fetch movie details. Please try again later.' }}
                    </p>
                </div>

                <div class="flex gap-2 justify-center">
                    <a href="javascript:history.back()"
                       class="inline-block px-6 py-2 border border-[#19140035] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm text-sm transition-all duration-200">
                        Go Back
                    </a>
                    <a href="{{ route('home') }}"
                       class="inline-block px-6 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black border border-black text-white text-sm rounded-sm transition-all duration-200">
                        Go to Home Page
                    </a>
                </div>
            </div>
        </div>
    @endif
@endsection
