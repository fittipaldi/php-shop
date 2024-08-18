<?php

namespace app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="Store API Documentation",
 *     description="This is the API documentation for store API."
 * )
 */
class StoreController extends Controller
{

    const KM_TO_MILE = 0.621371;

    /**
     * @OA\Get(
     *     path="/api/v1/store",
     *     summary="Retrieve store information by postcode",
     *     description="This endpoint retrieves store information based on the provided postcode.",
     *     operationId="getStoreByPostcode",
     *     tags={"Store"},
     *     @OA\Parameter(
     *         name="postcode",
     *         in="query",
     *         description="Postcode to search for stores",
     *         required=true,
     *         @OA\Schema(
     *             type="string",
     *             example="eh39gu"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response with store data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="message", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing Bearer token"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Store not found"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     }
     * )
     */
    public function getStore(Request $request): Response
    {
        try {
            $pc = $request->input('postcode');

            $pcRow = Postcode::where('postcode', '=', $pc)->first();
            if (!$pcRow) {
                return response()->json([
                    'success' => false,
                    'message' => 'Postcode not found'
                ], 404);
            }

            $storesNear = Store::getNearStoresByLongLat($pcRow->latitude, $pcRow->longitude);

            $stores = [];
            foreach ($storesNear as $sn) {
                $stores[] = [
                    'name' => $sn->name,
                    'latitude' => $sn->latitude,
                    'longitude' => $sn->longitude,
                    'status' => $sn->status,
                    'store_type' => $sn->store_type,
                    'max_distance' => $sn->max_distance,
                    'distance' => $sn->distance_km * self::KM_TO_MILE,
                ];
            }

            $data = [
                'postcode' => $pcRow->postcode,
                'latitude' => $pcRow->latitude,
                'longitude' => $pcRow->longitude,
                'stores' => $stores,
            ];
            return response()->json([
                'status' => true,
                'data' => $data,
            ]);

        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/v1/add-store",
     *     summary="Add a new store",
     *     description="This endpoint allows the user to add a new store with specific details.",
     *     operationId="addStore",
     *     tags={"Store"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="The Best Food bbb"),
     *             @OA\Property(property="latitude", type="number", format="float", example=55.9409192),
     *             @OA\Property(property="longitude", type="number", format="float", example=-3.1973917),
     *             @OA\Property(property="status", type="string", example="closeooo"),
     *             @OA\Property(property="store_type", type="string", example="Restaurant"),
     *             @OA\Property(property="max_distance", type="integer", example=5)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Store created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Store added successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad Request - Invalid input"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized - Invalid or missing Bearer token"
     *     ),
     *     security={
     *         {"bearerAuth": {}}
     *     },
     *     @OA\Header(
     *         header="Content-Type",
     *         description="Content-Type header",
     *         @OA\Schema(
     *             type="string",
     *             example="application/json"
     *         )
     *     )
     * )
     */
    public function createStore(Request $request): Response
    {
        try {

            if (!Store::validateRequestData($request)) {
                throw new \Exception('Request data invalid.');
            }

            $store = new Store();
            $store->name = $request->input('name');
            $store->longitude = $request->input('longitude');
            $store->latitude = $request->input('latitude');
            $store->status = $request->input('status');
            $store->store_type = $request->input('store_type');
            $store->max_distance = $request->input('max_distance'); // max distance in miles
            $store->save();

            return response()->json([
                'status' => true,
                'message' => 'Store Created Successfully!'
            ], 201);
        } catch (QueryException $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
