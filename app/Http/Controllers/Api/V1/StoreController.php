<?php

namespace app\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class StoreController extends Controller
{

    public function getStore()
    {
        return response()->json([
            'message' => 'tamo aqui meu joaia!'
        ], 200);
    }

    public function createStore(Request $request)
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
            $store->max_distance = $request->input('max_distance');
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
