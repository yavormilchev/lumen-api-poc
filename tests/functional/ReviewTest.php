<?php

namespace App\Tests\Functional;

use App\Review;
use App\Tests\Helpers\ApiRouteUrlsTrait;
use Laravel\Lumen\Testing\DatabaseTransactions;
use TestCase;

/**
 * Functional tests which verify entire review data flow and confirm error handling.
 */
class ReviewTest extends TestCase
{
    use ApiRouteUrlsTrait;

    use DatabaseTransactions;

    private $headers;

    private $review_data;

    public function setUp()
    {
        parent::setUp();

        $this->headers = [
            'Authorization' => 'Bearer ' . env('API_KEY'),
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];
        $this->review_data = [
            'name' => 'Some test review 4444',
            'description' => 'Some test review 4444',
        ];
    }

    /** @test */
    public function it_creates_reviews()
    {
        // Given
        $this->notSeeInDatabase('reviews', $this->review_data);

        // When
        $this->post($this->getApiRouteUrl('review.create'), $this->review_data, $this->headers)
            ->assertResponseStatus(201);

        // Then
        $this->seeInDatabase('reviews', $this->review_data);
        $response = json_decode($this->response->content());
        $this->assertTrue($response->data->id > 0);
    }

    /** @test */
    public function it_reads_reviews()
    {
        // Given
        $created_review = factory(Review::class)->create($this->review_data);

        // When
        $response_1 = $this->get($this->getApiRouteUrl('review.index'), $this->headers);
        $response_2 = $this->get($this->getApiRouteUrl('review.view', ['id' => $created_review->id]), $this->headers);

            // Then
        $response_1->seeJsonContains($this->review_data);
        $response_2->seeJsonContains($this->review_data);
    }

    /** @test */
    public function it_updates_reviews()
    {
        // Given
        $created_review = factory(Review::class)->create($this->review_data);
        $new_name = 'Some test review 7777';

        // When
        $response = $this->put(
            $this->getApiRouteUrl('review.update', ['id' => $created_review->id]),
            ['name' => $new_name],
            $this->headers
        );

        // Then
        $response->assertResponseStatus(200);
        $this->seeInDatabase('reviews', [
            'id' => $created_review->id,
            'name' => $new_name
        ]);
    }

    /** @test */
    public function it_deletes_reviews()
    {
        // Given
        $created_review = factory(Review::class)->create($this->review_data);

        // When
        $response = $this->delete(
            $this->getApiRouteUrl('review.delete', ['id' => $created_review->id]),
            [],
            $this->headers
        );

        // Then
        $response->assertResponseStatus(200);
        $this->notSeeInDatabase('reviews', ['id' => $created_review->id]);
    }

    /** @test */
    public function it_fails_with_invalid_authentication()
    {
        // Given
        $invalid_headers = [
            'Authorization' => 'INVALID',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        // When
        $response = $this->post($this->getApiRouteUrl('review.create'), $this->review_data, $invalid_headers);

        // Then
        $response->assertResponseStatus(401);
    }

    /** @test */
    public function it_fails_to_get_missing_reviews()
    {
        // Given
        $invalid_id = 0;

        // When
        $response = $this->get($this->getApiRouteUrl('review.view', ['id' => $invalid_id]), $this->headers);

        // Then
        $response->assertResponseStatus(404);
    }

    /** @test */
    public function it_fails_to_create_with_invalid_data()
    {
        // Given
        $review_data_missing_name = [
            'description' => 'Some test review 4444',
        ];

        // When
        $response = $this->post($this->getApiRouteUrl('review.create'), $review_data_missing_name, $this->headers);

        // Then
        $response->assertResponseStatus(422);
    }

    /** @test */
    public function it_fails_to_update_with_invalid_data()
    {
        // Given
        $created_review = factory(Review::class)->create($this->review_data);
        $bad_review_data = [
            'something' => 'bad',
        ];

        // When
        $response = $this->put(
            $this->getApiRouteUrl('review.update', ['id' => $created_review->id]),
            $bad_review_data,
            $this->headers
        );

        // Then
        $response->assertResponseStatus(422);
    }

    /** @test */
    public function it_fails_to_delete_missing_review()
    {
        // Given
        $invalid_id = 0;

        // When
        $response = $this->delete($this->getApiRouteUrl('review.delete', ['id' => $invalid_id]), [], $this->headers);

        // Then
        $response->assertResponseStatus(404);
    }
}
