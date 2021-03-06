<?php

namespace App\Http\Controllers;

use App\Http\Resources\MarkerCategoryResource;
use App\Models\Marker;
use App\Models\MarkerCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;

class MarkerCategoryController extends Controller
{
    public function __construct()
    {
//        $this->middleware('auth:admin')->except([]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index()
    {
        $search_query = Input::has('q') ? Input::get('q') : false;
        $order_by = Input::has('order') ? 'desc' : 'asc';
        $per_page = Input::has('per_page') ? (int) Input::get('per_page') : 5;

        return MarkerCategoryResource::collection(MarkerCategory::pagination($search_query, $order_by, $per_page));
    }

    public function trashed()
    {
        $search_query = Input::has('q') ? Input::get('q') : false;
        $order_by = Input::has('order') ? 'desc' : 'asc';
        $per_page = Input::has('per_page') ? (int) Input::get('per_page') : 5;

        return MarkerCategoryResource::collection(MarkerCategory::paginateTrashed($search_query, $order_by, $per_page));
    }

    /**
     * List all categories
     *
     * @return \Illuminate\Http\Response
     */
    public function listOfCategories()
    {
        return response()->json([
            'success' => true,
            'data' => MarkerCategory::select(['marker_categories.id','icon'])
                ->join('marker_category_translations as t', 'marker_categories.id', '=', 't.marker_category_id')
                ->groupBy('marker_categories.id')
                ->get()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'translations' => 'required',
                'icon' => 'max:500'
            ], [
                'translations.required' => 'Translations array is required!',
                'icon.max' => 'Icon max size 500 character!'
            ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $validator->valid();
        // Create new category
        $marker_category = new MarkerCategory();
        $marker_category->icon = $data['icon'];
        $marker_category->save();
        // Translate category
        foreach ($data['translations'] as $array) {
            $marker_category->translateOrNew($array['locale'])->name = $array['name'];
        }
        $marker_category->save();

        return response()->json([
            'success' => true,
            'data' => $marker_category,
        ], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        try {
            $marker_category = MarkerCategory::findOrFail($request->input('id'));
            return response()->json([
                'success' => true,
                'data' => $marker_category
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Data not found!'
            ], 400);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(),
            [
                'id' => 'required|numeric',
                'translations' => 'required',
                'icon' => 'max:500'
            ], [
                'id.required' => 'Category ID required!',
                'id.numeric' => 'Category ID must be numeric!',
                'translations.required' => 'Translations array is required!',
                'icon.max' => 'Icon max size 500 character!'
            ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        $data = $validator->valid();
        try {
            $marker_category = MarkerCategory::findOrFail($data['id']);
            // Edit category
            $marker_category->icon = $data['icon'];
            $marker_category->save();
            // Translate category
            foreach ($data['translations'] as $array) {
                    $marker_category->translateOrNew($array['locale'])->name = $array['name'];
            }
            $marker_category->save();

            return response()->json([
                'success' => true,
                'data' => $marker_category,
            ], 202);
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Data not found!'
                ],
                400
            );
        }
    }

    public function restoreTrashed(Request $request)
    {
        try {
            $marker = MarkerCategory::onlyTrashed()->findOrFail($request->input('id'));
            $marker->deleted_at = null;
            $marker->save();
            return response()->json([
                'success' => true,
                'data' => $marker
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'error' => 'Data not found!'
            ], 400);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        try {
            $id = $request->input('id');
            $marker_category = MarkerCategory::findOrFail($id);
            $marker_category->delete();

            Marker::where('marker_category_id',$id)
                ->update(['marker_category_id' => 0]);

            return response()->json(
                ['success' => true],
                200
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Data not found!'
                ],
                400
            );
        }
    }

    public function destroyForever(Request $request)
    {
        try {
            $marker_category = MarkerCategory::onlyTrashed()->findOrFail($request->input('id'));
            $marker_category->forcedelete();
            return response()->json(
                ['success' => true],
                200
            );
        } catch (ModelNotFoundException $e) {
            return response()->json(
                [
                    'success' => false,
                    'error' => 'Data not found!'
                ],
                400
            );
        }
    }
}
