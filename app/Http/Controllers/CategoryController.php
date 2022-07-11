<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 *
 */
class CategoryController extends ApiController
{
    /**
     * @param Request $request
     */
    public function getAll(Request $request)
    {
        try {
            $categories = Category::all();
            return $this->sendResponse($categories->toArray());
        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Something went wrong, please contact administrator!');
        }
    }
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50',
                'parent_id' => 'nullable|exists:categories,id'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Bad request!', $validator->messages()->toArray());
            }

            if ($request->get('parent_id')) {
                $parent = Category::find($request->get('parent_id'));

                if ($parent->parent?->parent) {
                    return $this->sendError('You can\'t add a 3rd level subcategory!');
                }
            }

            $category = new Category();
            $category->name = $request->get('name');
            $category->parent_id = $request->get('parent_id');
            $category->save();

            return $this->sendResponse($category->toArray());
        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Something went wrong, please contact administrator!');
        }
    }

    /**
     * @param $id
     */
    public function get($id)
    {
        $find = Category::find($id);
        return $this->sendResponse($find->toArray());
    }

    /**
     * @param $id
     * @param Request $request
     */
    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|max:50',
                'parent_id' => 'nullable|exists:categories,id'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Bad request!', $validator->messages()->toArray());
            }

            $category = Category::find($id);
            $category->name = $request->get('name');
            $category->parent_id = $request->get('parent_id');
            $category->updated_at = now();
            $category->save();
            return $this->sendResponse($category->toArray());
        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Something went wrong, please contact administrator!');
        }
    }

    /**
     * @param $id
     */
    public function delete($id)
    {
        try {
            $category = Category::find($id);
            $category->delete();
            return ("Succes");
        } catch (\Exception $exception) {
            Log::error($exception);

            return $this->sendError('Something went wrong, please contact administrator!');
        }
    }
}
