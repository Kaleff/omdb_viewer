@extends('layouts.app')

@section('title', 'Search Results - ' . config('app.name', 'Laravel'))

@section('content')
    <div class="mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
            <div>
                <h1 class="text-3xl lg:text-4xl font-bold text-[#1b1b18] dark:text-[#EDEDEC]">Search Results</h1>
                @if(request('search'))
                    <p class="text-[#706f6c] dark:text-[#A1A09A] mt-1">
                        Searching for: <span class="font-medium">"{{ request('search') }}"</span>
                        @if(request('type'))
                            <span class="text-xs bg-[#f8f9fa] dark:bg-[#3E3E3A] px-2 py-1 rounded-sm ml-2">{{ ucfirst(request('type')) }}</span>
                        @endif
                        @if(request('year'))
                            <span class="text-xs bg-[#f8f9fa] dark:bg-[#3E3E3A] px-2 py-1 rounded-sm ml-2">{{ request('year') }}</span>
                        @endif
                    </p>
                @endif
            </div>

            <div class="flex gap-2">
                <a href="{{ route('home') }}"
                   class="inline-block px-4 py-2 border border-[#19140035] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b] text-[#1b1b18] dark:text-[#EDEDEC] rounded-sm text-sm transition-all duration-200">
                    New Search
                </a>
            </div>
        </div>
    </div>

    <!-- Movie Search Results -->
    @if (isset($movies) && $movies['Response'] == 'True')
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-medium text-[#1b1b18] dark:text-[#EDEDEC]">
                    {{ number_format($movies['totalResults']) }} Results Found
                </h2>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                @foreach ($movies['Search'] as $movie)
                    <a href="{{ route('movies.show', $movie['imdbID']) }}" class="block">
                        <div class="bg-white dark:bg-[#161615] p-4 rounded-lg border border-[#e3e3e0] dark:border-[#3E3E3A] hover:border-[#1915014a] dark:hover:border-[#62605b] transition-all duration-200 flex gap-4 cursor-pointer hover:shadow-md">
                            @if ($movie['Poster'] != 'N/A')
                                <img src="{{ $movie['Poster'] }}" alt="{{ $movie['Title'] }}"
                                    class="w-20 h-28 object-cover rounded-sm flex-shrink-0">
                            @else
                                <div class="w-20 h-28 bg-[#dbdbd7] dark:bg-[#3E3E3A] rounded-sm flex items-center justify-center text-xs text-[#706f6c] dark:text-[#A1A09A] flex-shrink-0">
                                    No Image
                                </div>
                            @endif

                            <div class="flex-1 min-w-0">
                                <h3 class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2 truncate">{{ $movie['Title'] }}</h3>
                                <div class="space-y-1">
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        <span class="font-medium">Year:</span> {{ $movie['Year'] }}
                                    </p>
                                    <p class="text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                        <span class="font-medium">Type:</span> {{ ucfirst($movie['Type']) }}
                                    </p>
                                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                        <span class="font-medium">IMDb:</span> {{ $movie['imdbID'] }}
                                    </p>
                                </div>
                                <div class="mt-2">
                                    <span class="text-xs text-[#f53003] dark:text-[#FF4433] font-medium">View Details →</span>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            @if (isset($movies['next']) || isset($movies['prev']))
                <div class="flex flex-col sm:flex-row items-center justify-between mt-8 gap-4">
                    @if (isset($movies['prev']) && $movies['prev'])
                        <a href="{{ route('movies.search', array_merge(request()->except('newSearch'), ['page' => $movies['current_page'] - 1, 'newSearch' => '0'])) }}"
                            class="inline-block px-6 py-2 border border-[#19140035] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b] text-[#706f6c] dark:text-[#A1A09A] rounded-sm text-sm transition-all duration-200">
                            ← Previous
                        </a>
                    @else
                        <div></div> {{-- Spacer for layout --}}
                    @endif

                    <span class="text-sm text-[#706f6c] dark:text-[#A1A09A]">Page {{ $movies['current_page'] }}</span>

                    @if (isset($movies['next']) && $movies['next'])
                        <a href="{{ route('movies.search', array_merge(request()->except('newSearch'), ['page' => $movies['current_page'] + 1, 'newSearch' => '0'])) }}"
                            class="inline-block px-6 py-2 border border-[#19140035] hover:border-[#1915014a] dark:border-[#3E3E3A] dark:hover:border-[#62605b] text-[#706f6c] dark:text-[#A1A09A] rounded-sm text-sm transition-all duration-200">
                            Next →
                        </a>
                    @else
                        <div></div> {{-- Spacer for layout --}}
                    @endif
                </div>
            @endif
        </div>
    @elseif(isset($movies) && $movies['Response'] == 'False')
        <div class="text-center">
            <div class="max-w-md mx-auto">
                <div class="bg-[#fff2f2] dark:bg-[#1D0002] border border-[#f53003] dark:border-[#F61500] p-6 rounded-lg mb-10">
                    <h3 class="font-medium text-[#f53003] dark:text-[#F61500] mb-2">No Results Found</h3>
                    <p class="text-[#f53003] dark:text-[#F61500] text-sm">
                        {{ $movies['Error'] ?? 'No movies found for your search.' }}
                    </p>
                </div>

                <div class="text-sm text-[#706f6c] dark:text-[#A1A09A] space-y-2">
                    <p>Try adjusting your search:</p>
                    <ul class="list-disc list-inside space-y-1">
                        <li>Check spelling of the movie title</li>
                        <li>Remove year or type filters</li>
                        <li>Try searching with fewer words</li>
                        <li>Use alternative titles or original language titles</li>
                    </ul>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <div class="max-w-md mx-auto">
                <h3 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC] mb-2">Start Your Search</h3>
                <p class="text-[#706f6c] dark:text-[#A1A09A] text-sm mb-4">
                    Use the search form above to discover movies and TV shows.
                </p>
                <a href="{{ route('home') }}"
                   class="inline-block px-6 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black border border-black text-white text-sm rounded-sm transition-all duration-200">
                    Go to Home Page
                </a>
            </div>
        </div>
    @endif
@endsection
