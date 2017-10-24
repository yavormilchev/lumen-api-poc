<?php

namespace App\Tests\Unit\Services;

use App\Services\ReviewService;
use App\Services\ReviewServiceInvalidArgumentException;
use TestCase;

/**
 * ReviewService tests which verify its database interactions.
 */
class ReviewServiceTest extends TestCase
{
    /**
     * @var ReviewService
     */
    public $service;

    public function setUp()
    {
        parent::setUp();

        $this->service = new ReviewService();
    }

    /** @test */
    public function it_throws_when_create_with_missing_name()
    {
        // Given
        $invalid_data_no_name = [
            'name INVALID' => 'Test user',
            'description' => 'Review of awesome company',
        ];

        // Expect
        $this->expectException(ReviewServiceInvalidArgumentException::class);

        // When
        $this->service->create($invalid_data_no_name);
    }

    /** @test */
    public function it_throws_when_create_with_missing_description()
    {
        // Given
        $invalid_data_no_description = [
            'name' => 'Test user',
            'description INVALID' => 'Review of awesome company',
        ];

        // Expect
        $this->expectException(ReviewServiceInvalidArgumentException::class);

        // When
        $this->service->create($invalid_data_no_description);
    }
}
