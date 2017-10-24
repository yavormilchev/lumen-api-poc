<?php

namespace App\Services;

use App\Review;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReviewService
{
    const DEFAULT_LIMIT = 100;

    /**
     * Get collection of review models
     *
     * @param int $limit
     * @param int $skip
     *
     * @return Collection
     */
    public function paginate(int $limit = self::DEFAULT_LIMIT, int $skip = 0): Collection
    {
        return DB::table('reviews')->skip($skip)->take($limit)->get();
    }

    /**
     * Get reviews count
     *
     * @return int
     */
    public function count(): int
    {
        return DB::table('reviews')->count();
    }

    /**
     * Find a review model by ID
     *
     * @param int $id
     *
     * @return Model|null
     */
    public function findById(int $id)
    {
        return (new Review())->find($id);
    }

    /**
     * Get a review model by ID, throw exception on failure
     *
     * @param int $id
     *
     * @return Model
     * @throws ReviewServiceModelNotFoundException
     */
    public function getById(int $id): Model
    {
        try {
            return (new Review())->findOrFail($id);
        } catch (ModelNotFoundException $e) {
            throw new ReviewServiceModelNotFoundException();
        }
    }

    /**
     * Create review record
     *
     * @param array $properties
     *
     * @return int
     * @throws ReviewServiceInvalidArgumentException
     */
    public function create(array $properties): int
    {
        if (empty($properties['name']) || empty($properties['description'])) {
            throw new ReviewServiceInvalidArgumentException('Name and description are required');
        }

        return (new Review())->create([
            'name' => $properties['name'],
            'description' => $properties['description'],
        ])->id;
    }

    /**
     * Update review model
     *
     * @param int $id
     * @param array $properties
     *
     * @return bool
     * @throws ReviewServiceInvalidArgumentException
     * @throws ReviewServiceModelNotFoundException
     */
    public function update(int $id, array $properties): bool
    {
        if (empty($properties['name']) && empty($properties['description'])) {
            throw new ReviewServiceInvalidArgumentException('A name or description is required');
        }

        $review = $this->getById($id);

        return $review->update(array_filter($properties));
    }

    /**
     * Deletes a review record by ID
     *
     * @param int $id
     *
     * @return bool|null
     * @throws ReviewServiceModelNotFoundException
     */
    public function delete(int $id)
    {
        return $this->getById($id)->delete();
    }
}

class ReviewServiceException extends \Exception {};
class ReviewServiceInvalidArgumentException extends ReviewServiceException {};
class ReviewServiceModelNotFoundException extends ReviewServiceException {};
