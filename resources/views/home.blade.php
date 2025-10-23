@extends('layouts.app')

@section('title', 'Movie Search - ' . config('app.name', 'Laravel'))

@section('content')
    <div class="text-center mb-6">
        <h1 class="text-4xl lg:text-5xl font-bold mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Movie Search</h1>
        <p class="text-[#706f6c] dark:text-[#A1A09A]">Discover your favorite movies and TV shows</p>
    </div>

    <!-- Search Form -->
    <div
        class="bg-white dark:bg-[#161615] p-6 lg:p-8 rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] mb-6">
        <h2 class="text-lg font-medium mb-4 text-[#1b1b18] dark:text-[#EDEDEC]">Search for Movies</h2>
        <form action="{{ route('movies.search') }}" method="GET" class="flex flex-col lg:flex-row gap-4 items-end">
            <input type="hidden" name="newSearch" value="1">
            <div class="flex flex-col w-full">
                <label for="search" class="text-sm mb-2 text-[#706f6c] dark:text-[#A1A09A]">Movie Title:</label>
                <input type="text" id="search" name="search"
                    class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm text-sm bg-[#FDFDFC] dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:border-[#f53003] dark:focus:border-[#FF4433]"
                    placeholder="Enter movie title..." value="{{ request('search') }}" required>
            </div>

            <div class="flex flex-col w-full">
                <label for="type" class="text-sm mb-2 text-[#706f6c] dark:text-[#A1A09A]">Type:</label>
                <select id="type" name="type"
                    class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm text-sm bg-[#FDFDFC] dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:border-[#f53003] dark:focus:border-[#FF4433]">
                    <option value="">All Types</option>
                    <option value="movie" {{ request('type') == 'movie' ? 'selected' : '' }}>Movie</option>
                    <option value="series" {{ request('type') == 'series' ? 'selected' : '' }}>TV Series</option>
                    <option value="episode" {{ request('type') == 'episode' ? 'selected' : '' }}>Episode</option>
                </select>
            </div>

            <div class="flex flex-col w-full">
                <label for="year" class="text-sm mb-2 text-[#706f6c] dark:text-[#A1A09A]">Year:</label>
                <input type="number" id="year" name="year"
                    class="px-4 py-2 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm text-sm bg-[#FDFDFC] dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC] focus:outline-none focus:border-[#f53003] dark:focus:border-[#FF4433]"
                    placeholder="e.g., 2023" min="1900" max="{{ date('Y') + 1 }}" value="{{ request('year') }}">
            </div>

            <div class="w-full">
                <button type="submit"
                    class="w-full lg:w-auto px-5 py-2 bg-[#1b1b18] dark:bg-[#eeeeec] dark:border-[#eeeeec] dark:text-[#1C1C1A] dark:hover:bg-white dark:hover:border-white hover:bg-black hover:border-black border border-black text-white text-sm rounded-sm transition-all duration-200">
                    Search Movies
                </button>
            </div>
        </form>
    </div>

    <!-- Recent Search Requests Table -->
    <div
        class="bg-white dark:bg-[#161615] rounded-lg shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] overflow-hidden">
        <div class="p-6">
            <h2 class="text-lg font-medium text-[#1b1b18] dark:text-[#EDEDEC]">Recent Search Requests</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-[#1b1b18] dark:bg-[#3E3E3A]">
                    <tr class="text-center">
                        <th class="text-white dark:text-[#EDEDEC] p-4 text-sm font-medium">Search Term</th>
                        <th class="text-white dark:text-[#EDEDEC] p-4 text-sm font-medium">IP Address</th>
                        <th class="text-white dark:text-[#EDEDEC] p-4 text-sm font-medium">Search Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentSearches ?? [] as $search)
                        <tr
                            class="border-b border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f8f9fa] dark:hover:bg-[#1D1D1B] transition-colors duration-200">
                            <td class="p-4">
                                <div class="font-medium text-[#1b1b18] dark:text-[#EDEDEC] text-sm">
                                    {{ $search->search_request }}</div>
                                @if ($search->search_term && $search->search_term != $search->search_request)
                                    <div class="text-xs text-[#706f6c] dark:text-[#A1A09A] mt-1">Result:
                                        {{ $search->search_term }}</div>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-[#706f6c] dark:text-[#A1A09A] font-mono">{{ $search->ip_address }}
                            </td>
                            <td class="p-4 text-sm text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $search->created_at }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="p-8 text-center text-[#706f6c] dark:text-[#A1A09A] text-sm">
                                No search requests yet. Try searching for a movie above!
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
