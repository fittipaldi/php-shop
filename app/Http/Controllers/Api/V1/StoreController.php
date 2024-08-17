<?php

namespace app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Postcode;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{

    const KM_TO_MILE = 0.621371;

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
            ], 422);
        }
    }
}
