<?php

namespace App\Http\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryFormRequest;
use App\Http\Resources\System\CategoryResource;
use App\Interfaces\CategoryInterface;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    private CategoryInterface $CategoryRepository;
    public function __construct(CategoryInterface $CategoryRepository)
    {
        $this->CategoryRepository = $CategoryRepository;
    }

    public function store(CategoryFormRequest $request)
    {
        $details = $request->input();
        $data = $this->CategoryRepository->createCategory($details);
        return $this->dataResponse(['category' => new CategoryResource($data)], 'OK', 200);
    }

    public function update(CategoryFormRequest $request, $id)
    {
        $details = $request->input();
       $data= $this->CategoryRepository->updateCategory($details, $id);
        return $this->dataResponse(['category' => $data], 'OK', 200);
    }

    public function index(Request $request)
    {
        return $this->paginateCollection($this->CategoryRepository->getCategories($request), $request->limit, 'category');
    }

    public function delete($id)
    {
        $this->CategoryRepository->deleteCategory($id);
        return $this->successResponse('deleted successfully', 200);
    }

    public function show($id)
    {
        return $this->dataResponse(['category' => $this->CategoryRepository->getCategory($id)], 'OK', 200);
    }
}
