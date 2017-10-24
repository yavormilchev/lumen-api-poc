<?php

namespace App\Tests\Integration\Services;

use App\Review;
use App\Services\ReviewService;
use App\Services\ReviewServiceInvalidArgumentException;
use App\Services\ReviewServiceModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseTransactions;
use TestCase;

/**
 * ReviewService tests which verify its database interactions.
 */
class ReviewServiceTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * @var ReviewService
     */
    public $service;

    private $review_data;

    public function setUp()
    {
        parent::setUp();

        $this->service = new ReviewService();

        $this->review_data = [
            'name' => 'Some test review 3333',
            'description' => 'Some test review 3333',
        ];
    }

    /** @test */
    public function it_creates_reviews()
    {
        // Given
        $this->notSeeInDatabase('reviews', $this->review_data);

        // When
        $id = $this->service->create($this->review_data);

        // Then
        $this->assertTrue($id > 0);
        $this->seeInDatabase('reviews', $this->review_data);
    }

    /** @test */
    public function it_reads_reviews()
    {
        // Given
        $created_review = factory(Review::class)->create($this->review_data);

        // When
        $review = $this->service->getById($created_review->id)->toArray();
        $reviews = $this->service->paginate();
        $count= $this->service->count();

        // Then
        $this->assertEquals($this->review_data['name'], $review['name']);
        $this->assertEquals($this->review_data['description'], $review['description']);
        $this->assertTrue(count($reviews) > 0);
        $this->assertTrue($count > 0);
    }

    /** @test */
    public function it_updates_reviews()
    {
        // Given
        $created_review = factory(Review::class)->create($this->review_data);

        // When
        $response = $this->service->update($created_review->id, ['name' => 'Test user 234234']);

        // Then
        $this->assertTrue($response);
        $this->seeInDatabase('reviews', [
            'id' => $created_review->id,
            'name' => 'Test user 234234'
        ]);
    }

    /** @test */
    public function it_deletes_reviews()
    {
        // Given
        $created_review = factory(Review::class)->create($this->review_data);

        // When
        $this->service->delete($created_review->id);

        // Then
        $this->notSeeInDatabase('reviews', ['id' => $created_review->id]);
    }

    /** @test */
    public function it_attempts_to_find_missing_reviews()
    {
        // Given
        $invalid_id = 0;

        // When
        $review = $this->service->findById($invalid_id);

        // Then
        $this->assertNull($review);
    }

    /** @test */
    public function it_fails_to_get_missing_reviews()
    {
        // Given
        $invalid_id = 0;

        // Expect
        $this->expectException(ReviewServiceModelNotFoundException::class);

        // When
        $this->service->getById($invalid_id);
    }

    /** @test */
    public function it_fails_to_update_missing_reviews()
    {
        // Given
        $invalid_id = 0;

        // Expect
        $this->expectException(ReviewServiceModelNotFoundException::class);

        // When
        $this->service->update($invalid_id, $this->review_data);
    }

    /** @test */
    public function it_fails_to_update_with_missing_data()
    {
        // Given
        $id = $this->service->create($this->review_data);
        $invalid_data_no_name = [
            'name INVALID' => 'Test user',
            'description INVALID' => 'Review of awesome company',
        ];

        // Expect
        $this->expectException(ReviewServiceInvalidArgumentException::class);

        // When
        $this->service->update($id, $invalid_data_no_name);
    }

    /** @test */
    public function it_fails_to_delete_missing_review()
    {
        // Given
        $invalid_id = 0;

        // Expect
        $this->expectException(ReviewServiceModelNotFoundException::class);

        // When
        $this->service->delete($invalid_id);
    }
}
