<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use DataTables;

class ProductController extends Controller {

    /**
     * Get product list view.
     */
    public function index() {
        $categoryList = Category::get();
        return view('product-list')->with('categoryList', $categoryList);
    }

    /**
     * Get view to import Csv/Excel file.
     */
    public function importFile() {
        return view('import');
    }

    /**
     * Import file to process further and show field mapping screen.
     * @param Request $request
     */
    public function importFileMapping(Request $request) {
        $validator = \Validator::make($request->all(), [
                    'file' => ['required', 'mimes:csv,xls,xlsx', 'max:1500']
        ]);
        if ($validator->fails()) {
            //check all validations are fine, if not then redirect and show error messages
            return redirect()->back()->withInput()->withErrors($validator->errors());
        }
        $arr_file = explode('.', $_FILES['file']['name']);
        $extension = end($arr_file);
        // Check file extension for file reader.
        $reader = $this->checkFileExtensionForReader($extension);
        // Read file
        $spreadsheet = $reader->load($_FILES['file']['tmp_name']);
        $sheetData = $spreadsheet->getActiveSheet()->toArray();
        // Temporary store file to process further
        $file = request()->file('file');
        $filePath = $file->storeAs('products/' . \Auth::user()->id, $file->getClientOriginalName(), ['disk' => 'public']);
        // Storing saved file details to be used in later step
        \Session::put('filePath', $filePath);
        \Session::put('fileExtension', $file->getClientOriginalExtension());
        // Get table columns to be mapped with
        $table = new Product;
        $tableColumns = $table->getTableColumns();
        return view('field-mapping', ['sheetCoulmns' => $sheetData[0], 'tableCoulmns' => $tableColumns]);
    }

    /**
     * Save data into database
     * @param Request $request
     */
    public function saveImportData(Request $request) {
        // Merge system and field mapping columns
        $mappedData = array_combine($request->systemFields, $request->tableCoumns);
        // Read file from the storage
        $filename = \Storage::disk('public')->path(\Session::get('filePath'));
        // Check file extension for file reader.
        $reader = $this->checkFileExtensionForReader(\Session::get('fileExtension'));
        $reader->setReadDataOnly(true);
        $worksheetData = $reader->listWorksheetInfo($filename);
        $sheetName = $worksheetData[0]['worksheetName'];
        $reader->setLoadSheetsOnly($sheetName);
        $spreadsheet = $reader->load($filename);
        $worksheet = $spreadsheet->getActiveSheet();
        $fileData = $worksheet->toArray();
        foreach ($fileData as $i => $item) {
            if ($i > 0) {
                // Check if SKU already exists then update row, if not then create a new record.
                $product = Product::firstOrNew(['sku' => trim($fileData[$i][$mappedData['sku']])]);
                $product->sku = $fileData[$i][$mappedData['sku']];
                $product->title = $fileData[$i][$mappedData['title']];
                $product->description = $fileData[$i][$mappedData['description']];
                $product->price = $fileData[$i][$mappedData['price']];
                // Here quantity can be summed up to previous value based on the requirement.(oldQty + newQty)
                $product->quantity = $fileData[$i][$mappedData['quantity']];
                $product->category_id = $this->getCategoryIdByName($fileData[$i][$mappedData['category_id']]);
                if ($product->save()) {
                    // Delete file from storage and remove session
                    \Storage::disk('public')->delete(\Session::get('filePath'));
                }
            }
        }
        return redirect()->route('products');
    }

    /**
     * Get category Id by name.
     * @param type $category
     * @return string
     */
    public function getCategoryIdByName($category) {
        $multipleCategories = [];
        // Loop through categories separated by '|'
        foreach (explode('|', $category) as $categoryName) {
            // Check if category already exists then return it's Id, if not add new category
            if (!empty($category = Category::where('title', trim($categoryName))->first())) {
                $multipleCategories[] = $category->id;
            } else {
                $category = new Category();
                $category->title = $categoryName;
                $category->save();
                $multipleCategories[] = $category->id;
            }
        }
        // Adding multiple categories for a product
        return implode(',', $multipleCategories);
    }

    /**
     * Return file reader based on the extension
     * @param type $extension
     */
    public function checkFileExtensionForReader($extension) {
        if ('csv' == $extension) {
            return $reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
        } else if ('xls' == $extension) {
            return $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xls();
        } else {
            return $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        }
    }

    /**
     * Get all the products list.
     * @param Request $request
     */
    public function productList(Request $request) {

        if ($request->ajax()) {
            $data = \DB::table("products")
                ->select("products.*", \DB::raw("GROUP_CONCAT(categories.title) as category"))
                ->leftjoin("categories", \DB::raw("FIND_IN_SET(categories.id,products.category_id)"), ">", \DB::raw("'0'"))
                ->groupBy("products.id");
            return Datatables::of($data)
                ->addColumn('status', function ($row) {
                    $status = ($row->status == 1) ? 'Active' : 'Inactive';
                    return $status;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->categoryId != 'All') {
                        $instance->whereRaw("FIND_IN_SET('$request->categoryId',category_id) > 0");
                        $instance->groupBy("products.id");
                    }
                })
                ->rawColumns(['categoryId'])
                ->make(true);
        }
    }

}
