<?php

class ProductController extends BaseController
{
    use UploadFile;
    protected $categoryModel;
    protected $productModel;
    protected $message;

    public function __construct()
    {
        $this->loadModel('ProductModel');
        $this->productModel = new ProductModel;
        $this->loadModel('CategoryModel');
        $this->categoryModel = new CategoryModel;
    }

    public function index()
    {
        $products = $this->productModel->getListProductPaginate();
        $page       = (isset($_GET['page'])) ? $_GET['page'] : 1;
        // $links      = (isset($_GET['links'])) ? $_GET['links'] : 2;
        // links: so page dc hien thi truoc hoac sau dau ...
        $links = 2;
        return $this->view('admin.product.index', [
            'data' => $products->getData(5, $page)->data,
            'pagination' => $products->createLinks($links, 'pagination')
        ]);
    }

    public function create()
    {
        $categories = $this->categoryModel->getAll();
        return $this->view('admin.product.create', [
            'cats' => $categories
        ]);
    }

    public function store()
    {
        $data = [
            'name' => $_POST['name'],
            'price' => $_POST['price'],
            'sale_price' => $_POST['sale_price'],
            'description' => $_POST['description'],
            'origin' => $_POST['origin'],
            'quantity' => $_POST['quantity'],
            'status' => $_POST['status'],
            'category_id' => $_POST['category_id']
        ];
        if ($this->getImage()) { //kiểm tra lấy ảnh
            $data['image'] = $this->getImage();
        }
        if (sizeof($this->productModel->findProductByName($_POST['name'])) > 0) {
            $this->message['error-name'] = 'This product is already existing';
        } else {
            $this->message['success-add'] = 'Product added successfully';
            $this->productModel->createData($data);
        }
        $categories = $this->categoryModel->getAll();
        return $this->view('admin.product.create', [
            'message' => $this->message,
            'cats' => $categories
        ]);
        // header('location: ./?module=admin&controller=product');
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        $product = $this->productModel->findProductById(['*'], $id);
        $categories = $this->categoryModel->getAll();
        return $this->view('admin.product.edit', [
            'product' => $product,
            'cats' => $categories
        ]);
    }

    public function update()
    {
        $data = [
            'name' => $_POST['name'],
            'price' => $_POST['price'],
            'sale_price' => $_POST['sale_price'],
            'description' => $_POST['description'],
            'origin' => $_POST['origin'],
            'quantity' => $_POST['quantity'],
            'status' => $_POST['status'],
            'category_id' => $_POST['category_id'],
            'updated_at' => date("Y-m-d", time())
        ];
        $product = $this->productModel->findProductById(['*'], $_GET['id']);
        if ($this->getImage()) { //kiểm tra lấy ảnh
            $data['image'] = $this->getImage();
        }
        if (sizeof($this->productModel->checkNameUnique($_POST['name'], $product['name'])) == 1) {
            $this->message['error-name'] = 'This product is already existing';
        } else {
            $this->message['success-edit'] = 'Product updated successfully';
            $this->productModel->updateData($_GET['id'], $data);
        }
        $product = $this->productModel->findProductById(['*'], $_GET['id']);
        $categories = $this->categoryModel->getAll();
        return $this->view('admin.product.edit', [
            'message' => $this->message,
            'product' => $product,
            'cats' => $categories
        ]);
    }
    public function delete()
    {
        $id = $_GET['id'] ?? null;
        $products = $this->productModel->getListProductPaginate();
        $page       = (isset($_GET['page'])) ? $_GET['page'] : 1;
        // $links      = (isset($_GET['links'])) ? $_GET['links'] : 2;
        // links: so page dc hien thi truoc hoac sau dau ...
        $links = 2;
        $count = $this->productModel->countOrders($id);

        if ($id && is_numeric($id) && $count[0]['count'] == 0) {

            $this->productModel->deleteData($id);
            $this->message['success-delete'] = 'Product deleted successfully';
        } else {
            $this->message['error-delete'] = "Can not delete this product. There are still products belonged to it";
            // header('location: ./?module=admin&controller=product');
        }
        return $this->view('admin.product.index', [
            'data' => $products->getData(5, $page)->data,
            'pagination' => $products->createLinks($links, 'pagination'),
            'message' => $this->message
        ]);
    }

    private function getImage()
    { //lấy ảnh
        $image = null;

        if (!empty($_FILES['image']['name'])) {
            $this->setFileName($_FILES['image']['name']);
            $this->setFolderUpload('./public/uploads');
            $this->setFileTemp($_FILES['image']['tmp_name']);
            $image = $this->upload();
        } else {
            $image = $_POST['current-image'];
        }
        return $image;
    }

    public function searchProductByname()
    {
        $searchData = (isset($_REQUEST['productSearch'])) ? $_REQUEST['productSearch'] : "";
        $products = $this->productModel->searchProduct($searchData);
        $page       = (isset($_GET['page'])) ? $_GET['page'] : 1;
        // $links      = (isset($_GET['links'])) ? $_GET['links'] : 2;
        // links: so page dc hien thi truoc hoac sau dau ...
        $links = 2;
        return $this->view('admin.product.index', [
            'data' => $products->getData(5, $page)->data,
            'pagination' => $products->createLinks($links, 'pagination')
        ]);
    }

    public function searchProductFull()
    {
        $searchData = (isset($_REQUEST['productSearch'])) ? $_REQUEST['productSearch'] : "";
        $products = $this->productModel->searchProductFull($searchData);
        $page       = (isset($_GET['page'])) ? $_GET['page'] : 1;
        // $links      = (isset($_GET['links'])) ? $_GET['links'] : 2;
        // links: so page dc hien thi truoc hoac sau dau ...
        $links = 2;
        return $this->view('admin.product.index', [
            'data' => $products->getData(5, $page)->data,
            'pagination' => $products->createLinks($links, 'pagination')
        ]);
    }

    // thêm nhiều sản phẩm từ file csv hoặc excel
    public function import()
    {
        // Hiển thị form import
        return $this->view('admin.product.import');
    }

    public function processImport()
    {
        $message = [];
        
        // Kiểm tra file upload
        if (!isset($_FILES['import_file']) || $_FILES['import_file']['error'] != 0) {
            $message['error'] = 'Please select a valid file to import';
            return $this->view('admin.product.import', ['message' => $message]);
        }
        
        $file = $_FILES['import_file'];
        $fileName = $file['name'];
        $fileTmpName = $file['tmp_name'];
        $fileSize = $file['size'];
        $fileType = $file['type'];
        
        // Kiểm tra kích thước file (2MB max)
        if ($fileSize > 2 * 1024 * 1024) {
            $message['error'] = 'File size exceeds the limit (2MB)';
            return $this->view('admin.product.import', ['message' => $message]);
        }
        
        // Kiểm tra định dạng file
        $validTypes = [ // cho phép các định dạng file csv, excel, xlsx
            'text/csv',
            'application/vnd.ms-excel',
            'application/csv', 
            'application/excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];
        
        if (!in_array($fileType, $validTypes) && !preg_match('/\.csv$/i', $fileName)) {
            $message['error'] = 'Invalid file format. Please upload a CSV or Excel file.';
            return $this->view('admin.product.import', ['message' => $message]);
        }
        
        // Xử lý file CSV
        $hasHeader = isset($_POST['header_row']) ? true : false;
        $products = [];
        $errors = [];
        
        if (($handle = fopen($fileTmpName, 'r')) !== false) {
            // Bỏ qua dòng header nếu có
            if ($hasHeader) {
                fgetcsv($handle, 1000, ',');
            }
            
            $lineNumber = $hasHeader ? 2 : 1; // Để theo dõi dòng lỗi
            $successCount = 0;
            
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // Kiểm tra số lượng cột
                if (count($data) < 7) {
                    $errors[] = "Line $lineNumber: Not enough columns";
                    $lineNumber++;
                    continue;
                }
                
                // Xử lý dữ liệu
                $product = [
                    'name' => trim($data[0]),
                    'price' => floatval(trim($data[1])),
                    'sale_price' => floatval(trim($data[2])),
                    'description' => trim($data[3]),
                    'origin' => trim($data[4]),
                    'quantity' => intval(trim($data[5])),
                    'category_id' => intval(trim($data[6])),
                    'status' => isset($data[7]) ? intval(trim($data[7])) : 1,
                    'image' => isset($data[8]) ? trim($data[8]) : 'no-image.jpg' // Mặc định nếu không có ảnh
                ];
                
                // Kiểm tra dữ liệu
                if (empty($product['name'])) {
                    $errors[] = "Line $lineNumber: Product name is required";
                    $lineNumber++;
                    continue;
                }
                
                if ($product['price'] <= 0) {
                    $errors[] = "Line $lineNumber: Price must be greater than 0";
                    $lineNumber++;
                    continue;
                }
                
                // Thêm sản phẩm vào danh sách
                try {
                    // Kiểm tra sản phẩm tồn tại
                    if (sizeof($this->productModel->findProductByName($product['name'])) > 0) {
                        $errors[] = "Line $lineNumber: Product '{$product['name']}' already exists";
                        $lineNumber++;
                        continue;
                    }
                    
                    // Thêm sản phẩm vào database
                    $this->productModel->createData($product);
                    $successCount++;
                } catch (Exception $e) {
                    $errors[] = "Line $lineNumber: Error adding product - " . $e->getMessage();
                }
                
                $lineNumber++;
            }
            fclose($handle);
            
            // Thông báo kết quả
            if ($successCount > 0) {
                $message['success'] = "Successfully imported $successCount products";
            }
            
            if (!empty($errors)) {
                $message['error'] = "Encountered the following errors:<br>" . implode("<br>", $errors);
            }
        } else {
            $message['error'] = 'Could not open the file for reading';
        }
        
        return $this->view('admin.product.import', ['message' => $message]);
    }
}