<?php
require_once '../../Controllers/CategoryController.php'; // Include your PHP file where the CategoryController class is defined

$categoryController = new CategoryController(); // Create an instance of CategoryController
$categories = $categoryController->getAllCategories();

$errorMessage = null;

// Handle form submission for adding a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
    // Sanitize and validate input
    $categoryName = trim($_POST['category_name']);
    
    if (!empty($categoryName)) {
        if ($categoryController->isCategoryNameExist($categoryName)) {
            $errorMessage = "The category name already exists.";
        } else {
            // Call the addCategory function from CategoryController
            $categoryController->addCategory($categoryName);

            // Redirect to the same page to refresh and show new category
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $errorMessage = "اسم الصنف مطلوب.";
    }
}

// Handle form submission for updating a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
  // Sanitize and validate input
  $categoryId = trim($_POST['category_id']);
  $categoryName = trim($_POST['category_name']);

  if (!empty($categoryId) && !empty($categoryName)) {
      if ($categoryController->isCategoryNameExist($categoryName, $categoryId)) {
          $errorMessage = "The category name already exists.";
      } else {
          // Call the updateCategory function from CategoryController
          $categoryController->updateCategory($categoryId, $categoryName);

          // Redirect to the same page to refresh and show updated category
          header("Location: " . $_SERVER['PHP_SELF']);
          exit();
      }
  } else {
      $errorMessage = "اسم الصنف و معرف الصنف مطلوبان.";
  }
}


// Handle form submission for deleting a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    // Sanitize and validate input
    $categoryId = trim($_POST['category_id']);

    if (!empty($categoryId)) {
        // Call the deleteCategory function from CategoryController
        $categoryController->DeActiveCategory($categoryId);

        // Redirect to the same page to refresh and show updated list
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $errorMessage = "معرف الصنف مطلوب.";
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

    <title>الاصناف العامة</title>

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
              <!-- New -->
              <li class="menu-item active open" style="">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="bx bx-category-alt"></i>
                <div data-i18n="Layouts">الاصناف</div>
              </a>

              <ul class="menu-sub">
              <li class="menu-item active">
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
                           <li class="menu-item" style="">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                          <i class='bx bx-paint-roll'></i>
                          <div data-i18n="Layouts">المنتجات</div>
                        </a>

                        <ul class="menu-sub">
                        <li class="menu-item">
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
                            <a href="inventory.php" class="menu-link">
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

                              <!-- Add Category Form -->
                              <div class="row">
                                <div class="col-xl">
                                  <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                      <h5 class="mb-0">اضافة صنف جديد</h5>
                                      <small class="text-muted float-end">الاصناف المتاحة</small>
                                    </div>
                                    <div class="card-body">
                                      <form method="post" action="">
                                        <div class="mb-3">
                                          <label class="form-label" for="basic-default-fullname">اسم الصنف</label>
                                          <input type="text" class="form-control" id="basic-default-fullname" name="category_name" placeholder="اسم الصنف" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="add">حفظ</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                       <!-- outside -->
                                 <!-- Table Content -->
                                    <div class="card">
                                      <h5 class="card-header">الاصناف</h5>
                                      <div class="table-responsive text-nowrap">
                                        <table class="table table-striped">
                                          <thead>
                                            <tr>
                                              <th>ID</th>
                                              <th>اسم الصنف</th>
                                              <th>Actions</th>
                                            </tr>
                                          </thead>
                                          <tbody class="table-border-bottom-0">
                                            <?php if ($categories):
                                              $index = 0; ?>
                                              <?php foreach ($categories as $category):
                                                $index++ ?>
                                                <tr>
                                                  <td><?php echo htmlspecialchars($index); ?></td>
                                                  <td><?php echo htmlspecialchars($category['CategoryName']); ?></td>
                                                  <td>
                                                    <div class="dropdown">
                                                      <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                      </button>
                                                      <div class="dropdown-menu">
                                                        <!-- Modal Trigger -->
                                                        <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editCategoryModal-<?php echo htmlspecialchars($category['CategoryId']); ?>">
                                                          <i class="bx bx-edit-alt me-1"></i> Edit
                                                        </button>
                                                        <form method="post" action="" style="display: inline;">
                                                          <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['CategoryId']); ?>">
                                                          <button type="submit" class="dropdown-item" name="delete" onclick="return confirm('Are you sure you want to delete this category?');">
                                                            <i class="bx bx-trash me-1"></i> Delete
                                                          </button>
                                                        </form>
                                                      </div>
                                                    </div>

                                                    <!-- Edit Category Modal -->
                                                    <div class="modal fade" id="editCategoryModal-<?php echo htmlspecialchars($category['CategoryId']); ?>" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                                      <div class="modal-dialog">
                                                        <form class="modal-content" method="post" action="">
                                                          <div class="modal-header">
                                                            <h5 class="modal-title" id="backDropModalTitle">Edit Category</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                          </div>
                                                          <div class="modal-body">
                                                            <div class="row">
                                                              <div class="col mb-3">
                                                                <label for="nameBackdrop-<?php echo htmlspecialchars($category['CategoryId']); ?>" class="form-label">اسم الصنف</label>
                                                                <input type="text" id="nameBackdrop-<?php echo htmlspecialchars($category['CategoryId']); ?>" class="form-control" name="category_name" value="<?php echo htmlspecialchars($category['CategoryName']); ?>" required>
                                                              </div>
                                                            </div>
                                                          </div>
                                                          <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                              Close
                                                            </button>
                                                            <button type="submit" class="btn btn-primary" name="update">Save</button>
                                                          </div>
                                                          <input type="hidden" name="category_id" value="<?php echo htmlspecialchars($category['CategoryId']); ?>">
                                                        </form>
                                                      </div>
                                                    </div>
                                                  </td>
                                                </tr>
                                              <?php endforeach; ?>
                                            <?php else: ?>
                                              <tr>
                                                <td colspan="3">No categories found.</td>
                                              </tr>
                                            <?php endif; ?>
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>
                        <!-- Table -->
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

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
