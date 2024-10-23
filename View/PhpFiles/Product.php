<?php
require_once '../../Controllers/CategoryController.php';
require_once '../../Controllers/ProductController.php';


$categoryController = new CategoryController();
$productController = new ProductController();


$categories = $categoryController->getAllCategories();
$products = $productController->getAllProducts();

$errorMessage = null;

// Handle form submission for adding a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_product'])) {
    $categoryID = $_POST['category_id'];
    $name = $_POST['product_name'];
    $buyPrice = $_POST['buy_price'];
    $salePrice = $_POST['sale_price'];
    
    if (!empty($categoryID) && !empty($name) && !empty($buyPrice) && !empty($salePrice)) {
        if ($productController->isProductUnique($categoryID, $name)) {
            // Add the product and get its ID
            $productID = $productController->addProduct($categoryID, $name, $buyPrice, $salePrice);
            
            if ($productID) {
                // Add inventory for the newly added product
                
                
                // Redirect to avoid resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errorMessage = "Error adding product.";
            }
        } else {
            $errorMessage = "هذا المنتج مع الصنف موجود بالفعل";
        }
    } else {
        $errorMessage = "All fields are required.";
    }
}


// Handle form submission for updating a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_product'])) {
    $productId = $_POST['product_id'];
    $categoryID = $_POST['category_id'];
    $name = $_POST['product_name'];
    $buyPrice = $_POST['buy_price'];
    $salePrice = $_POST['sale_price'];

    if (!empty($productId) && !empty($categoryID) && !empty($name) && !empty($buyPrice) && !empty($salePrice)) {
        if ($productController->isProductUnique($categoryID, $name, $productId)) {
            $productController->updateProduct($productId, $categoryID, $name, $buyPrice, $salePrice);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $errorMessage = "المنتج مع هذا الصنف موجود بالفعل.";
        }
    } else {
        $errorMessage = "جميع الحقول مطلوبة.";
    }
}

// Handle form submission for deleting a product
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $productId = $_POST['product_id'];

    if (!empty($productId)) {
        $productController->DeActiveProduct($productId);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $errorMessage = "رقم المنتج غير صحيح.";
    }
}
?>

<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>المنتجات</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/Elmostafa.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->

    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="../assets/js/config.js"></script>
  </head>

  <body>
    <div class="layout-container">
      <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
          <!-- Menu -->
          <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
              <a href="index.php" class="app-brand-link">
              <span class="app-brand-logo demo">
                                <!-- Replace SVG with your new image -->
                                <img src="../assets/img/Elmostafa.png" alt="Logo" style="width: 170px; height: 160px;">
                            </span>
              </a>
              <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                <i class="bx bx-chevron-left bx-sm align-middle"></i>
              </a>
            </div>
            <div class="menu-inner-shadow"></div>
            <ul class="menu-inner py-1">
              <!-- Dashboard -->
              <li class="menu-item">
                    <a href="index.php" class="menu-link">
                      <i class="menu-icon tf-icons bx bx-home-circle"></i>
                      <div data-i18n="Analytics">الرئيسية-التقارير</div>
                    </a>
                </li>
                <li class="menu-item">
                <a href="invoices.php" class="menu-link">
                <i class='bx bx-spreadsheet'></i>
                  <div data-i18n="Analytics">الجرد والفواتير</div>
                </a>
              </li>
                   <li class="menu-item">
                      <a href="vault.php" class="menu-link">
                         <i class='bx bx-wallet'></i>
                          <div data-i18n="Analytics">الخزنة</div>
                      </a>
                  </li>
              <li class="menu-item" style="">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                          <i class="bx bx-category-alt"></i>
                          <div data-i18n="Layouts">الاصناف</div>
                        </a>

                        <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="Category.php" class="menu-link">
                              <div data-i18n="Without menu">الاصناف</div>
                            </a>
                          </li>

                          <li class="menu-item">
                            <a href="ArchiveCatgory.php" class="menu-link">
                              <div data-i18n="Without menu">الارشيف</div>
                            </a>
                        </li>
                          
                        </ul>
              </li>
                           <!-- Product -->
                           <li class="menu-item active open" style="">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                          <i class='bx bx-paint-roll'></i>
                          <div data-i18n="Layouts">المنتجات</div>
                        </a>

                        <ul class="menu-sub">
                        <li class="menu-item active">
                            <a href="Product.php" class="menu-link">
                              <div data-i18n="Without menu">المنتجات</div>
                            </a>
                          </li>

                          <li class="menu-item">
                            <a href="ArchiveProduct.php" class="menu-link">
                              <div data-i18n="Without menu">الارشيف</div>
                            </a>
                          </li>
                          
                        </ul>
                      </li>
                        <!-- Product -->
                        <!-- Inventory -->
                        <li class="menu-item" style="">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                          <i class='bx bx-data'></i>
                          <div data-i18n="Layouts">لمخزون</div>
                        </a>

                        <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="inventoryInvoice.php" class="menu-link">
                              <div data-i18n="Without menu">فواتير المشتريات</div>
                            </a>
                          </li>

                          <li class="menu-item">
                            <a href="inventory.php" class="menu-link">
                              <div data-i18n="Without menu">المخزون</div>
                            </a>
                          </li>
                          
                        </ul>
                      </li>
                        <!-- Inventory -->

                        <!-- Customer -->
                        <li class="menu-item" style="">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                            <i class='bx bxs-user-detail'></i>
                          <div data-i18n="Layouts">العملاء</div>
                        </a>

                        <ul class="menu-sub">
                        <li class="menu-item">
                            <a href="customers.php" class="menu-link">
                              <div data-i18n="Without menu">اضافة عملاء</div>
                            </a>
                          </li>
                          <li class="menu-item">
                            <a href="Suppliers.php" class="menu-link">
                              <div data-i18n="Without menu">اضافة موردين</div>
                            </a>
                          </li>

                          <li class="menu-item">
                            <a href="CustomersDebt.php" class="menu-link">
                              <div data-i18n="Without menu">ديون العملاء</div>
                            </a>
                          </li>
                          <li class="menu-item">
                            <a href="CustomerArchive.php" class="menu-link">
                              <div data-i18n="Without menu">ارشيف العملاء</div>
                            </a>
                          </li>
                          <li class="menu-item">
                            <a href="SupplierArchive.php" class="menu-link">
                              <div data-i18n="Without menu">ارشيف الموردين</div>
                            </a>
                          </li>
                          
                        </ul>
                      </li>
                        <!-- Customer -->
            </ul>
          </aside>
          <div class="layout-page">
              <div class = "content-wrapper">
                <div class="container-xxl flex-grow-1 container-p-y">
                  <div class = "row">
                      <!-- side Row -->
                     <!-- Error Message Display -->
                              <?php if ($errorMessage): ?>
                                <div class="alert alert-danger" role="alert">
                                  <?php echo htmlspecialchars($errorMessage); ?>
                                </div>
                              <?php endif; ?>

                              <!-- Add Product Form -->
                              <div class="row">
                                <div class="col-xl">
                                  <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                      <h5 class="mb-0">اضافة منتج جديد</h5>
                                      <small class="text-muted float-end">نموذج الاضافة</small>
                                    </div>
                                    <div class="card-body">
                                      <form method="post" action="">
                                        <div class="mb-3">
                                          <label for="exampleFormControlSelect1" class="form-label">نوع الصنف</label>
                                          <select class="form-select" id="exampleFormControlSelect1" name="category_id" required>
                                            <option value="" selected="">اختر الصنف</option>
                                            <?php foreach ($categories as $category): ?>
                                              <option value="<?php echo htmlspecialchars($category['CategoryId']); ?>"><?php echo htmlspecialchars($category['CategoryName']); ?></option>
                                            <?php endforeach; ?>
                                          </select>
                                        </div>
                                        <div class="mb-3">
                                          <label class="form-label" for="product_name">اسم الصنف</label>
                                          <input type="text" class="form-control" id="product_name" name="product_name" placeholder="اسم الصنف" required>
                                        </div>
                                        <div class="mb-3">
                                          <label class="form-label" for="buy_price">سعر الشراء</label>
                                          <input type="text" class="form-control" id="buy_price" name="buy_price" placeholder="سعر الشراء" required>
                                        </div>
                                        <div class="mb-3">
                                          <label class="form-label" for="sale_price">سعر البيع</label>
                                          <input type="text" class="form-control" id="sale_price" name="sale_price" placeholder="سعر البيع" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="add_product">حفظ</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                       <!-- outside -->
                      <!-- Table Content -->
                      <!-- Error Message Display -->
                              <?php if ($errorMessage): ?>
                                <div class="alert alert-danger" role="alert">
                                  <?php echo htmlspecialchars($errorMessage); ?>
                                </div>
                              <?php endif; ?>

                              <!-- Display Products Table -->
                              <div class="card">
                                <h5 class="card-header">المنتجات</h5>
                                <div class="table-responsive text-nowrap">
                                  <table class="table table-striped" id="productsTable">
                                    <thead>
                                      <tr>
                                        <th>ID</th>
                                        <th>اسم الصنف</th>
                                        <th>اسم المنتج</th>
                                        <th>سعر الشراء</th>
                                        <th>سعر البيع</th>
                                        <th>تفاصيل</th>
                                      </tr>
                                    </thead>
                                    <tbody class="table-border-bottom-0">
                                      <?php if ($products):
                                        $index = 0; ?>
                                        <?php foreach ($products as $product): 
                                          $index++;?>
                                          <tr>
                                            <td><?php echo htmlspecialchars($index); ?></td>
                                            <td><?php echo htmlspecialchars($product['CategoryName']); ?></td>
                                            <td><?php echo htmlspecialchars($product['ProductName']); ?></td>
                                            <td><?php echo htmlspecialchars($product['BuyPrice']); ?></td>
                                            <td><?php echo htmlspecialchars($product['SalePrice']); ?></td>
                                            <td>
                                              <div class="dropdown">
                                                <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                                  <i class="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div class="dropdown-menu">
                                                  <!-- Modal Trigger -->
                                                  <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editProductModal-<?php echo htmlspecialchars($product['productId']); ?>">
                                                    <i class="bx bx-edit-alt me-1"></i> تعديل
                                                  </button>
                                                  <form method="post" action="" style="display: inline;">
                                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['productId']); ?>">
                                                    <button type="submit" class="dropdown-item" name="delete_product" onclick="return confirm('هل انت متاكد من تعطيل او مسح ذلك المنتج ؟');">
                                                      <i class="bx bx-trash me-1"></i> تعطيل
                                                    </button>
                                                  </form>
                                                </div>
                                              </div>

                                              <!-- Edit Product Modal -->
                                              <div class="modal fade" id="editProductModal-<?php echo htmlspecialchars($product['productId']); ?>" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                  <form class="modal-content" method="post" action="">
                                                    <div class="modal-header">
                                                      <h5 class="modal-title">تعديل المنتج</h5>
                                                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                      <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['productId']); ?>">
                                                      <div class="mb-3">
                                                        <label for="product_name" class="form-label">اسم المنتج</label>
                                                        <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo htmlspecialchars($product['ProductName']); ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="category_id" class="form-label">نوع الصنف</label>
                                                        <select id="category_id" name="category_id" class="form-select" required>
                                                          <?php foreach ($categories as $category): ?>
                                                            <option value="<?php echo htmlspecialchars($category['CategoryId']); ?>" <?php echo $category['CategoryId'] == $product['CategoryID'] ? 'selected' : ''; ?>>
                                                              <?php echo htmlspecialchars($category['CategoryName']); ?>
                                                            </option>
                                                          <?php endforeach; ?>
                                                        </select>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="buy_price" class="form-label">سعر الشراء</label>
                                                        <input type="text" id="buy_price" name="buy_price" class="form-control" value="<?php echo htmlspecialchars($product['BuyPrice']); ?>" required>
                                                      </div>
                                                      <div class="mb-3">
                                                        <label for="sale_price" class="form-label">سعر البيع</label>
                                                        <input type="text" id="sale_price" name="sale_price" class="form-control" value="<?php echo htmlspecialchars($product['SalePrice']); ?>" required>
                                                      </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                      <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">الغاء</button>
                                                      <button type="submit" class="btn btn-primary" name="update_product">حفظ</button>
                                                    </div>
                                                  </form>
                                                </div>
                                              </div>
                                            </td>
                                          </tr>
                                        <?php endforeach; ?>
                                      <?php else: ?>
                                        <tr>
                                          <td colspan="6">لا يوجد منتجات حتي الان</td>
                                        </tr>
                                      <?php endif; ?>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                       <!-- outside -->
                        <!-- Pagination Controls -->
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-center" id="paginationControls">
                            <!-- Pagination items will be dynamically added here -->
                        </ul>
                    </nav>
                  </div>
                </div>
              </div>
            </div>
        </div> <!-- Closing for layout-container -->
      </div> <!-- Closing for layout-wrapper layout-content-navbar -->
    </div> <!-- Closing for layout-container -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>
    <script>
document.addEventListener('DOMContentLoaded', function () {
    const rowsPerPage = 10;
    const table = document.getElementById('productsTable');
    const tbody = table.querySelector('tbody');
    const rows = tbody.getElementsByTagName('tr');
    const totalRows = rows.length;
    const totalPages = Math.ceil(totalRows / rowsPerPage);
    const paginationControls = document.getElementById('paginationControls');

    function displayRows(page) {
        const start = (page - 1) * rowsPerPage;
        const end = start + rowsPerPage;
        
        for (let i = 0; i < totalRows; i++) {
            rows[i].style.display = (i >= start && i < end) ? '' : 'none';
        }
    }

    function createPagination() {
        for (let i = 1; i <= totalPages; i++) {
            const li = document.createElement('li');
            li.className = 'page-item ' + (i === 1 ? 'active' : '');
            const a = document.createElement('a');
            a.className = 'page-link';
            a.textContent = i;
            a.href = '#';
            a.dataset.page = i;
            li.appendChild(a);
            paginationControls.appendChild(li);

            a.addEventListener('click', function (e) {
                e.preventDefault();
                const page = parseInt(this.dataset.page);
                displayRows(page);

                // Update active state
                const currentActive = paginationControls.querySelector('.page-item.active');
                if (currentActive) currentActive.classList.remove('active');
                li.classList.add('active');
            });
        }
    }

    // Initialize pagination
    displayRows(1);
    createPagination();
});
</script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    
  </body>
</html>
