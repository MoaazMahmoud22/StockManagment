<?php
require_once '../../Controllers/SuppliersController.php';

$SupplierController = new SuppliersController();
$Suppliers = $SupplierController->getAllSupplier();
$errorMessage = null;

// Handle form submission for adding a customer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_customer'])) {
    $CustomerName = $_POST['Customer_name'];
    $phone_number = $_POST['Phone_number'];

    if (!empty($CustomerName) && !empty($phone_number)) {
        if (!$SupplierController->isSupplierNameExist($CustomerName)) {
            if ($SupplierController->addSupplier($CustomerName, $phone_number)) {
                // Redirect to avoid resubmission
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            } else {
                $errorMessage = "Error adding customer.";
            }
        } else {
            $errorMessage = "هذا العميل موجود بالفعل";
        }
    } else {
        $errorMessage = "كل الحقول مطلوبة";
    }
}

// Handle form submission for updating a customer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_customer'])) {
    $CustomerID = $_POST['CustomerId'];
    $CustomerName = $_POST['CustomerName'];
    $phone_number = $_POST['phone_number'];

    if (!empty($CustomerID) && !empty($CustomerName) && !empty($phone_number)) {
        if (!$SupplierController->isSupplierNameExist($CustomerName,$CustomerID)) {
            $SupplierController->updateSupplier($CustomerID, $CustomerName, $phone_number);
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            $errorMessage = "العميل بهذا الاسم موجود بالفعل.";
        }
    } else {
        $errorMessage = "جميع الحقول مطلوبة.";
    }
}

// Handle form submission for deleting a customer
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_customer'])) {
    $CustomerID = $_POST['CustomerId'];

    if (!empty($CustomerID)) {
        $SupplierController->DeActiveSupplier($CustomerID);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        $errorMessage = "رقم العميل غير صحيح.";
    }
}

$totalCustomers = count($Suppliers); // Total number of customers
$CustomerPerPage = 15; // Number of customers per page
$totalPages = ceil($totalCustomers / $CustomerPerPage);

// Get the current page from the URL, if not set, default to page 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($totalPages, $currentPage));

// Calculate the starting index for fetching the customers for the current page
$startIndex = ($currentPage - 1) * $CustomerPerPage;

// Slice the customers array to get the customers for the current page
$currentCustomers = array_slice($Suppliers, $startIndex, $CustomerPerPage);

// Calculate pagination range (3 pages before and after current page)
$startPage = max(1, $currentPage - 3);
$endPage = min($totalPages, $currentPage + 3);
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

    <title>اضافة الموردين</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

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
                          <li class="menu-item active open" style="">
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

                          <li class="menu-item active">
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
                                      <h5 class="mb-0">اضافة عميل جديد</h5>
                                      <small class="text-muted float-end">نموذج الاضافة</small>
                                    </div>
                                    <div class="card-body">
                                      <form method="post" action="">
                                        <div class="mb-3">
                                          <label class="form-label" for="Customer_name">اسم المورد</label>
                                          <input type="text" class="form-control" id="Customer_name" name="Customer_name" placeholder="اسم المورد" required>
                                        </div>
                                        <div class="mb-3">
                                          <label class="form-label" for="Phone_number">رقم الهاتف</label>
                                          <input type="text" class="form-control" id="Phone_number" name="Phone_number" placeholder="رقم الهاتف" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary" name="add_customer">حفظ</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                       <!-- outside -->
                        <!-- Filter -->

                      <!-- after Filter -->
                        <!-- Table Content -->
                      <div class="card">
                        <h5 class="card-header">الموردين</h5>
                        <div class="table-responsive text-nowrap">
                          <table class="table table-striped">
                            <thead>
                              <tr>
                            <th>ID</th>
                            <th>اسم المورد</th>
                            <th>رقم الهاتف</th>
                            <th>تفاصيل</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            <?php if ($currentCustomers):
                                        $index = 0; ?>
                                        <?php foreach ($currentCustomers as $Supplier): 
                                          $index++;?>
                                                  <tr>
                                                    <td><?php echo htmlspecialchars($index); ?></td>
                                                    <td><?php echo $Supplier['CustomerName']; ?></td>
                                                    <td><?php echo $Supplier['phone_number']; ?></td>
                                                    <td>
                                  <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                      <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                      <!-- Modal Trigger -->
                                      <button type="button" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editCategoryModal-<?php echo htmlspecialchars($Supplier['CustomerId']); ?>">
                                      <i class="bx bx-edit-alt me-1"></i> تعديل
                                      </button>
                                      <form method="post" action="" style="display: inline;">
                                                          <input type="hidden" name="CustomerId" value="<?php echo htmlspecialchars($Supplier['CustomerId']); ?>">
                                                          <button type="submit" class="dropdown-item" name="delete_customer" onclick="return confirm('هل انت متاكد من حذف هذا المورد ؟');">
                                                            <i class="bx bx-trash me-1"></i> حذف
                                                          </button>
                                      </form>
                                    </div>
                                    <div class="modal fade" id="editCategoryModal-<?php echo htmlspecialchars($Supplier['CustomerId']); ?>" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                        <form class="modal-content" method="post" action="">
                                            <div class="modal-header">
                                              <h5 class="modal-title" id="backDropModalTitle">Modal title</h5>
                                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                              <div class="row">
                                                <div class="col mb-3">
                                                      <label for="nameBackdrop-<?php echo htmlspecialchars($Supplier['CustomerId']); ?>" class="form-label">اسم المورد</label>
                                                      <input type="text" id="nameBackdrop-<?php echo htmlspecialchars($Supplier['CustomerId']); ?>" class="form-control" name="CustomerName" value="<?php echo htmlspecialchars($Supplier['CustomerName']); ?>" required>
                                                  </div>
                                                </div>
                                              <div class="row">
                                                <div class="col mb-3">
                                                      <label for="nameBackdrop-<?php echo htmlspecialchars($Supplier['phone_number']); ?>" class="form-label">رقم الهاتف</label>
                                                      <input type="text" id="nameBackdrop-<?php echo htmlspecialchars($Supplier['phone_number']); ?>" class="form-control" name="phone_number" value="<?php echo htmlspecialchars($Supplier['phone_number']); ?>" required>
                                                  </div>
                                                </div>
                                              </div>
                                                <div class="modal-footer">
                                                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                                              Close
                                                            </button>
                                                            <button type="submit" class="btn btn-primary" name="update_customer">حفظ</button>
                                                  </div>
                                                    <input type="hidden" name="CustomerId" value="<?php echo htmlspecialchars($Supplier['CustomerId']); ?>">
                                                
                                            </div>
                                          </form>
                                        </div>
                                      </div>
                                  </div>
                                </td>
                                                </tr>
                              <?php endforeach; ?>
                                      <?php else: ?>
                                        <tr>
                                          <td colspan="6">لا يوجد موردين حتي الان</td>
                                        </tr>
                                      <?php endif; ?>
                              <!-- Add more rows as needed -->
                            </tbody>
                          </table>
                        </div>
                    </div>

                    <!-- Table -->
                     <!-- Pagination Controls -->
<nav aria-label="Page navigation">
    <ul class="pagination justify-content-center">
        <?php if ($currentPage > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $currentPage - 1; ?>" aria-label="Previous">
                    <span aria-hidden="true">&laquo; السابق</span>
                </a>
            </li>
        <?php endif; ?>
        
        <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
            <li class="page-item <?php echo $i === $currentPage ? 'active' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor; ?>
        
        <?php if ($currentPage < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $currentPage + 1; ?>" aria-label="Next">
                    <span aria-hidden="true">التالي &raquo;</span>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
 <!-- Pagination Controls -->
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
