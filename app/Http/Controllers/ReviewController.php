<?php

namespace App\Http\Controllers;

use App\Services\ReviewService;
use App\Services\ReviewServiceInvalidArgumentException;
use App\Services\ReviewServiceModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * @var ReviewService
     */
    protected $service;

    /**
     * ReviewController constructor.
     *
     * @param ReviewService $service
     */
    public function __construct(ReviewService $service)
    {
        $this->middleware('api_auth');

        $this->service = $service;
    }

    /**
     * Display a listing of reviews.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Get(
     *     path="/api/reviews",
     *     description="Returns listing of reviews.",
     *     operationId="review.index",
     *     produces={"application/json"},
     *     tags={"review"},
     *     @SWG\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of reviews per page",
     *         required=false,
     *         type="integer"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of reviews."
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */
    public function index(Request $request)
    {
        $total = $this->service->count();
        $page = $request->get('page', 1);
        $per_page = $request->get('per_page', $this->service::DEFAULT_LIMIT);
        $skip = $per_page * ($page - 1);
        $pages = ceil($total / $per_page);
        $reviews = $this->service->paginate($per_page, $skip);

        return new JsonResponse(['data' => compact('reviews', 'total', 'page', 'pages', 'per_page')]);
    }

    /**
     * View a single review
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @SWG\Get(
     *     path="/api/reviews/{id}",
     *     description="Returns a single review.",
     *     operationId="review.view",
     *     produces={"application/json"},
     *     tags={"review"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of review",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="List of reviews."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Review not found",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */
    public function view(int $id)
    {
        try {
            return new JsonResponse(['data' => ['review' => $this->service->getById($id)]]);
        } catch (ReviewServiceModelNotFoundException $e) {
            return new JsonResponse(['message' => 'Review not found'], 404);
        }
    }

    /**
     * Create new review
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @SWG\Post(
     *     path="/api/reviews",
     *     description="Creates a review.",
     *     operationId="review.create",
     *     produces={"application/json"},
     *     tags={"review"},
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Review to be created",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Review")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Review created."
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Validation failed",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */
    public function create(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required',
        ]);

        $id = $this->service->create([
            'name' => $request->get('name'),
            'description' => $request->get('description'),
        ]);

        return new JsonResponse(['data' => ['id' => $id]], 201);
    }

    /**
     * Update an existing review by ID
     *
     * @param Request $request
     * @param int $id
     *
     * @return JsonResponse
     *
     * @SWG\Put(
     *     path="/api/reviews/{id}",
     *     description="Updates a review.",
     *     operationId="review.update",
     *     produces={"application/json"},
     *     tags={"review"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of review",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Parameter(
     *         name="body",
     *         in="body",
     *         description="Review to be created",
     *         required=true,
     *         @SWG\Schema(ref="#/definitions/Review")
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Review updated."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Review not found",
     *     ),
     *     @SWG\Response(
     *         response=422,
     *         description="Validation failed",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */
    public function update(Request $request, int $id)
    {
        try {
            $result = $this->service->update($id, [
                'name' => $request->get('name'),
                'description' => $request->get('description'),
            ]);

            return new JsonResponse(['success' => $result]);
        } catch (ReviewServiceModelNotFoundException $e) {
            return new JsonResponse(['message' => 'Review not found'], 404);
        } catch (ReviewServiceInvalidArgumentException $e) {
            return new JsonResponse(['message' => 'Unprocessable entity'], 422);
        }
    }

    /**
     * Delete an existing review by ID.
     *
     * @param int $id
     *
     * @return JsonResponse
     *
     * @SWG\Delete(
     *     path="/api/reviews/{id}",
     *     description="Deletes a review.",
     *     operationId="review.delete",
     *     produces={"application/json"},
     *     tags={"review"},
     *     @SWG\Parameter(
     *         name="id",
     *         in="path",
     *         description="Id of review",
     *         required=true,
     *         type="integer",
     *         format="int64"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Review updated."
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="Review not found",
     *     ),
     *     security={
     *       {"bearerAuth": {}}
     *     }
     * )
     */
    public function delete(int $id)
    {
        try {
            $result = $this->service->delete($id);

            return new JsonResponse(['success' => $result]);
        } catch (ReviewServiceModelNotFoundException $e) {
            return new JsonResponse(['message' => 'Review not found'], 404);
        }
    }
}
