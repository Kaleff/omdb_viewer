<?php

namespace Tests\Unit;

use App\Http\Requests\MovieSearchRequest;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class MovieSearchRequestTest extends TestCase
{
    private function validateRequest(array $data): \Illuminate\Validation\Validator
    {
        $request = new MovieSearchRequest();
        return Validator::make($data, $request->rules());
    }

    public function test_search_is_required()
    {
        $data = [];

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('search', $validator->errors()->toArray());
    }

    public function test_search_must_be_string()
    {
        $data = ['search' => 123];

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('search', $validator->errors()->toArray());
    }

    public function test_search_has_max_length_255()
    {
        $data = ['search' => str_repeat('a', 256)]; // 256 characters

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('search', $validator->errors()->toArray());
    }

    public function test_search_passes_with_valid_string()
    {
        $data = ['search' => 'batman'];

        $validator = $this->validateRequest($data);

        $this->assertTrue($validator->passes());
    }

    public function test_type_can_be_null()
    {
        $data = ['search' => 'batman'];

        $validator = $this->validateRequest($data);

        $this->assertTrue($validator->passes());
    }

    public function test_type_must_be_valid_enum_value()
    {
        $validTypes = ['movie', 'series', 'episode'];

        foreach ($validTypes as $type) {
            $data = ['search' => 'batman', 'type' => $type];

            $validator = $this->validateRequest($data);

            $this->assertTrue($validator->passes(), "Type '$type' should be valid");
        }
    }

    public function test_type_rejects_invalid_values()
    {
        $data = ['search' => 'batman', 'type' => 'invalid'];

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('type', $validator->errors()->toArray());
    }

    public function test_year_can_be_null()
    {
        $data = ['search' => 'batman'];

        $validator = $this->validateRequest($data);

        $this->assertTrue($validator->passes());
    }

    public function test_year_must_be_integer()
    {
        $data = ['search' => 'batman', 'year' => 'not-a-year'];

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('year', $validator->errors()->toArray());
    }

    public function test_year_has_min_value_1900()
    {
        $data = ['search' => 'batman', 'year' => 1899];

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('year', $validator->errors()->toArray());
    }

    public function test_year_has_max_value_next_year()
    {
        $nextYear = date('Y') + 1;
        $data = ['search' => 'batman', 'year' => $nextYear + 1];

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());
        $this->assertArrayHasKey('year', $validator->errors()->toArray());
    }

    public function test_year_passes_with_valid_values()
    {
        $validYears = [1900, 2023, date('Y'), date('Y') + 1];

        foreach ($validYears as $year) {
            $data = ['search' => 'batman', 'year' => $year];

            $validator = $this->validateRequest($data);

            $this->assertTrue($validator->passes(), "Year '$year' should be valid");
        }
    }

    public function test_page_can_be_null()
    {
        $data = ['search' => 'batman'];

        $validator = $this->validateRequest($data);

        $this->assertTrue($validator->passes());
    }

    public function test_page_must_be_positive_integer()
    {
        $invalidPages = [0, -1, 'not-a-number'];

        foreach ($invalidPages as $page) {
            $data = ['search' => 'batman', 'page' => $page];

            $validator = $this->validateRequest($data);

            $this->assertFalse($validator->passes(), "Page '$page' should be invalid");
        }
    }

    public function test_page_passes_with_valid_positive_integers()
    {
        $validPages = [1, 2, 10, 100];

        foreach ($validPages as $page) {
            $data = ['search' => 'batman', 'page' => $page];

            $validator = $this->validateRequest($data);

            $this->assertTrue($validator->passes(), "Page '$page' should be valid");
        }
    }

    public function test_new_search_can_be_null()
    {
        $data = ['search' => 'batman'];

        $validator = $this->validateRequest($data);

        $this->assertTrue($validator->passes());
    }

    public function test_new_search_accepts_boolean_values()
    {
        $validBooleans = [true, false, 1, 0, '1', '0'];

        foreach ($validBooleans as $newSearch) {
            $data = ['search' => 'batman', 'newSearch' => $newSearch];

            $validator = $this->validateRequest($data);

            $this->assertTrue($validator->passes(), "newSearch '$newSearch' should be valid");
        }
    }

    public function test_complete_valid_request()
    {
        $data = [
            'search' => 'The Dark Knight',
            'type' => 'movie',
            'year' => 2008,
            'page' => 1,
            'newSearch' => true
        ];

        $validator = $this->validateRequest($data);

        $this->assertTrue($validator->passes());
        $this->assertEmpty($validator->errors()->toArray());
    }

    public function test_multiple_validation_errors()
    {
        $data = [
            'search' => '', // Required
            'type' => 'invalid', // Must be in enum
            'year' => 1800, // Below minimum
            'page' => 0 // Below minimum
        ];

        $validator = $this->validateRequest($data);

        $this->assertFalse($validator->passes());

        $errors = $validator->errors()->toArray();
        $this->assertArrayHasKey('search', $errors);
        $this->assertArrayHasKey('type', $errors);
        $this->assertArrayHasKey('year', $errors);
        $this->assertArrayHasKey('page', $errors);
    }
}
