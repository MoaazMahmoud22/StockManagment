<?php
require_once '../../Controllers/VaultController.php'; // Include your PHP file where the CategoryController class is defined
require_once '../../Controllers/PricesController.php'; 

$VaultController = new VaultController(); // Create an instance of CategoryController
$PricesController = new PricesController(); // Create an instance of CategoryController
$Vault = $VaultController->GetVault();
$TotalPriceForInvoices = $PricesController->TotalPriceForinvoices();
$VaultInfo = $VaultController->GetAllVaultInfo();

$errorMessage = null;
$calculateFinalTotalInventoryInvoice = $PricesController->calculateFinalTotalInventoryInvoice();

$TotalPayPriceForAllCustomers = $PricesController->TotalPayPriceForAllCustomers();
$TotalPayPriceForAllSuppliers = $PricesController->TotalPayPriceForAllSuppliers();
$TotalCash = ($Vault[0]['Cash']+$TotalPriceForInvoices+$TotalPayPriceForAllCustomers)-($calculateFinalTotalInventoryInvoice+$TotalPayPriceForAllSuppliers);
$All_Profits_Across_All_Invoices =($TotalPriceForInvoices+$TotalPayPriceForAllCustomers)-($calculateFinalTotalInventoryInvoice+$TotalPayPriceForAllSuppliers);
// Handle form submission for adding a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['increase'])) {
    // Sanitize and validate input
    $Cash = ($_POST['Cash']);
    $details = $_POST['details'];
    $date = date("Y-m-d H:i:s");
    if (!empty($Cash)) {
        if ($VaultController->IncreaseCash($Cash,$details,$date)) {
            // Redirect to the same page to refresh and show new category
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    } else {
        $errorMessage = " المبلغ مطلوب.";
    }
}

// Handle form submission for adding a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decrease'])) {
    // Sanitize and validate input
    $Cash = ($_POST['Cash']);
    $details = $_POST['details'];
    $date = date("Y-m-d H:i:s");
    if (!empty($Cash)) {
      if($Cash<=$TotalCash){
        if ($VaultController->DecreaseCash($Cash,$details,$date)) {
          // Redirect to the same page to refresh and show new category
          header("Location: " . $_SERVER['PHP_SELF']);
          exit();
      }
      } else{
            $errorMessage = "المبلغ في الخزنة اقل ";
        }
    } else {
        $errorMessage = " المبلغ مطلوب.";
    }
}

// Pagination logic
$totalVaultInfo = count($VaultInfo); // Total number of customers
$InfoPerPage = 15; // Number of customers per page
$totalPages = ceil($totalVaultInfo / $InfoPerPage);

// Get the current page from the URL, if not set, default to page 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$currentPage = max(1, min($totalPages, $currentPage));

// Calculate the starting index for fetching the customers for the current page
$startIndex = ($currentPage - 1) * $InfoPerPage;

// Slice the customers array to get the customers for the current page
$currentInfo = array_slice($VaultInfo, $startIndex, $InfoPerPage);

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

    <title>الخزنة</title>

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
                        <li class="menu-item active">
                            <a href="vault.php" class="menu-link">
                            <i class='bx bx-wallet'></i>
                                <div data-i18n="Analytics">الخزنة</div>
                            </a>
                        </li>
                        <!-- Category -->
                        <li class="menu-item" style="">
                        <a href="Category.php" class="menu-link menu-toggle">
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
                        <!-- Category -->
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

                              <!-- Add Category Form -->
                              <div class="row">
                                <div class="col-xl">
                                  <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                      <h5 class="mb-0">الخزنة</h5>
                                      <small class="text-muted float-end">الاصناف المتاحة</small>
                                    </div>
                                    <div class="card-body">
                                      <form method="post" action="">
                                        <div class="mb-3">
                                          <label class="form-label" for="basic-default-fullname">اضافة المبلغ</label>
                                          <input type="number" class="form-control" id="basic-default-fullname" name="Cash" placeholder="المبلغ" required>
                                        </div>
                                        <div class="mb-3">
                                          <label class="form-label" for="basic-default-fullname">الوصف</label>
                                          <input type="text" class="form-control" id="basic-default-fullname" name="details" placeholder="الوصف" required>
                                        </div>
                                        <button type="submit" class="btn rounded-pill btn-success" name="increase">[+] اضافة</button>
                                        <button type="submit" class="btn rounded-pill btn-danger" name="decrease">[-] خصم</button>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>
                       <!-- outside -->
                       <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                                <img src="../assets/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded">
                              </div>
                            </div>
                            <span>النقود في الخزنة</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $TotalCash  ?> EG</h3>
                          </div>
                        </div>
                      </div>

                       <!-- outside -->
                       <!-- outside -->


                       <!-- outside -->
                      <!-- Filter -->
                                            <!-- Table Content -->
                                            <div class="card">
                        <h5 class="card-header">العملاء</h5>
                        <div class="table-responsive text-nowrap">
                          <table class="table table-striped">
                            <thead>
                              <tr>
                            <th>ID</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                            <th>تفاصيل</th>
                            <th>المبلغ</th>
                              </tr>
                            </thead>
                            <tbody class="table-border-bottom-0">
                            <?php if ($currentInfo):
                                        $index = 0; ?>
                                        <?php foreach ($currentInfo as $Info): 
                                          $index++;?>
                                                  <tr>
                                                    <td><?php echo htmlspecialchars($index); ?></td>
                                                    <td><?php echo $Info['Date']; ?></td>
                                                    <td><?php echo $Info['details']; ?></td>
                                                    <td><?php if($Info['Status']=='decrease'){
                                                      echo 'خصم';
                                                    }else{
                                                      echo 'اضافة';
                                                    }
                                                     ?></td>
                                                     <td><?php echo $Info['Amount']; ?></td>
                                </tr>
                                <?php endforeach; ?>
                                      <?php else: ?>
                                        <tr>
                                          <td colspan="6">لا يوجد بيانات للخزنة حتي الان</td>
                                        </tr>
                                      <?php endif; ?>
                              <!-- Add more rows as needed -->
                            </tbody>
                          </table>
                        </div>
                    </div>

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

                      <!-- after Filter -->
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
