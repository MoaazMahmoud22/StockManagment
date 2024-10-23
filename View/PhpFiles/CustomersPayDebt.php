<?php
require_once '../../Controllers/VaultController.php'; 
require_once '../../Controllers/PricesController.php'; 
require_once '../../Controllers/CustomerController.php';

$CustomerController = new CustomerController();
$VaultController = new VaultController();
$customers = $CustomerController->getAllCustomers();

$errorMessage = null;
$CustomerID = isset($_GET['CustomerID']) ? intval($_GET['CustomerID']) : null;
$Customer = null;

if ($CustomerID) {
    $Customer = $CustomerController->getCustomer($CustomerID);
    $CustomerPayment = $Customer[0]['Payment'];
    $CustomerDebt = $CustomerController->Calculate_Customer_Debt($CustomerID);
    if(!$CustomerController->Calculate_Customer_Debt($CustomerID)){
      $CustomerDebt = 0;
    }
    $Collector_of_the_account = $CustomerDebt - $CustomerPayment ;
} else {
    echo "Invalid Customer ID.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['increase'])) {
  // Sanitize and validate input
  $Payment = (float)($_POST['Payment']);

  if (!empty($Payment)) {
      if($Collector_of_the_account>=$Payment){
        if ($CustomerController->IncreasePaymentCustomer($Payment,$CustomerID)) {
          // Redirect to the same page to refresh and show new category
          header("Location: CustomersPayDebt.php?CustomerID=$CustomerID");
          exit();
      }

      }else
      {
        $errorMessage = "لا يمكن تحصيل مبلغ زيادة من العميل";
      }
  } else {
      $errorMessage = " المبلغ مطلوب.";
  }
}

// Handle form submission for adding a category
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['decrease'])) {
  // Sanitize and validate input
  $Payment = (float)($_POST['Payment']);

  if (!empty($Payment)) {
      if ($CustomerController->DecreasePaymentCustomer($Payment,$CustomerID)) {
          // Redirect to the same page to refresh and show new category
          header("Location: CustomersPayDebt.php?CustomerID=$CustomerID");
          exit();
      }
      else{
          $errorMessage = "لا يمكن خصم التحصيل من العميل اكثر من الحد المطلوب";
      }
  } else {
      $errorMessage = " المبلغ مطلوب.";
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

    <title>Dashboard - Analytics | Sneat - Bootstrap 5 HTML Admin Template - Pro</title>

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
                          <li class="menu-item">
                            <a href="Suppliers.php" class="menu-link">
                              <div data-i18n="Without menu">اضافة موردين</div>
                            </a>
                          </li>

                          <li class="menu-item active">
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
                            <div class ="row">
                              <div class = "col-md-12">
                              <div class="card mb-4">
                              <h5 class="card-header">معلومات العميل</h5>
                              <!-- Account -->
                              <?php if ($Customer): ?>
                              <hr class="my-0">
                              <div class="card-body">
                                <form id="formAccountSettings">
                                  <div class="row">
                                    <div class="mb-3 col-md-6">
                                      <label for="firstName" class="form-label">اسم العميل</label>
                                      <input class="form-control" type="text" id="CustomerName" name="firstName" value="<?= htmlspecialchars($Customer[0]['CustomerName']) ?>" autofocus="" readonly>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                      <label for="lastName" class="form-label">رقم التليفون</label>
                                      <input class="form-control" type="text" name="phone_number" value="<?= htmlspecialchars($Customer[0]['phone_number']) ?>" readonly>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                      <label for="email" class="form-label"><?= $Collector_of_the_account<0 ? 'العميل لديه':'ديون العميل' ?></label>
                                      <input class="form-control" type="text" id="email" name="email" value="<?= htmlspecialchars($Collector_of_the_account)?>" readonly>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                      <label for="email" class="form-label">المدفوعات السابقة</label>
                                      <input class="form-control" type="text" id="email" name="email" value="<?= htmlspecialchars($CustomerPayment)?>" readonly>
                                    </div>
                                    <div class="mb-3 col-md-6">
                                      <label for="email" class="form-label">اجمالي المشتريات</label>
                                      <input class="form-control" type="text" id="email" name="email" value="<?= htmlspecialchars($CustomerDebt)?>" readonly>
                                    </div>

                                  </div>
                                  <?php endif; ?>
                                </form>
                              </div>
                              <!-- /Account -->
                            </div>
                              </div>
                            </div>
                              <!-- Add Category Form -->
                              <div class="row">
                                <div class="col-xl">
                                  <div class="card mb-4">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                      <h5 class="mb-0">
                                        <?php if ($Collector_of_the_account >= 0): ?>
                                          التحصيل من العميل
                                        <?php else: ?>
                                          اضافة مبلغ للعميل
                                        <?php endif; ?>
                                      </h5>
                                    </div>
                                    <div class="card-body">
                                      <form method="post" action="">
                                        <?php if ($Collector_of_the_account >= 0): ?>
                                          <!-- Form for تحصيل or خصم from التحصيل -->
                                          <div class="mb-3">
                                            <label class="form-label" for="basic-default-fullname">اضافة المبلغ</label>
                                            <input type="number" class="form-control" id="basic-default-fullname" name="Payment" placeholder="المبلغ" required>
                                          </div>
                                          <div class="mt-3">
                                            <button type="submit" class="btn rounded-pill btn-success" name="increase">تحصيل</button>
                                            <button type="submit" class="btn rounded-pill btn-danger" name="decrease">خصم من التحصيل</button>
                                          </div>
                                        <?php else: ?>
                                          <!-- Form for اضافة مبلغ للعميل -->
                                          <div class="mb-3">
                                            <label class="form-label" for="basic-default-addamount">اضافة مبلغ</label>
                                            <input type="number" class="form-control" id="basic-default-addamount" name="Payment" placeholder="المبلغ" required>
                                          </div>
                                          <div class="mt-3">
                                            <button type="submit" class="btn rounded-pill btn-danger" name="decrease">اضافة مبلغ للعميل</button>
                                          </div>
                                        <?php endif; ?>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                              </div>

                      <!-- Filter -->
                     

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

    
<!-- Include Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Include jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Include Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>


<script>
  $(document).ready(function() {
    $('#customerSelect').select2({
      placeholder: "اختر اسم العميل",
      allowClear: true
    });
  });
</script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
