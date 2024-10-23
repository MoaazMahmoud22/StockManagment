<?php

require_once '../../Controllers/DBController.php';
require_once '../../Controllers/ProductController.php';
require_once '../../Controllers/InventoryInvoiceController.php';
require_once '../../Controllers/PricesController.php';
require_once '../../Controllers/VaultController.php';
require_once '../../Controllers/SuppliersController.php';


$vaultController = new VaultController(); // Create an instance of CategoryController
$Prices = new PricesController(); // Create an instance of CategoryController
$Vault = $vaultController->GetVault();
$TotalPriceForInvoices = $Prices->TotalPriceForinvoices();

$All_Profits_Across_All_Invoices = $Prices->Sum_All_Profits_Across_All_Invoices();

$TotalPayPriceForAllCustomers = $Prices->TotalPayPriceForAllCustomers();
$TotalPayPriceForAllSuppliers = $Prices->TotalPayPriceForAllSuppliers();

$calculateFinalTotalInventoryInvoice = $Prices->calculateFinalTotalInventoryInvoice();
$calculateFinalTotalInventoryInvoice += $TotalPayPriceForAllSuppliers;
$TotalCashInVault = ($Vault[0]['Cash']+$TotalPriceForInvoices+$TotalPayPriceForAllCustomers)-($calculateFinalTotalInventoryInvoice+$TotalPayPriceForAllSuppliers);

$SupplierController = new SuppliersController();
$Suppliers = $SupplierController->getAllSupplier();
$inventoryInvoiceController = new InventoryInvoiceController();

$invoices = $inventoryInvoiceController->getAllInventoryInvoice();
$status = isset($_GET['status']) ? $_GET['status'] : null;
$currentInvoices = $inventoryInvoiceController->getAllInventoryInvoice($status);

$errorMessage = null;
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_inventory'])) {

  // Check if required POST parameters are set
  if (isset($_POST['inventory_status']) && isset($_POST['CustomerId']) && !empty($_POST['CustomerId'])) {
    
    $date = date("Y-m-d");
    $inventory_status = $_POST['inventory_status'];
    $GetcustomerId = $_POST['CustomerId'];

    // Fetch vault details
    $Vault = $vaultController->GetVault();

    // Validate customer ID
    if (empty($GetcustomerId)) {
      $errorMessage = "يرجى اختيار المورد.";
    } else {
      if ($inventory_status == 'بيع' && $TotalCashInVault > 0) {
        // Add new inventory invoice
        $inventoryInvoiceID = $inventoryInvoiceController->addInventoryInvoice($date, $inventory_status, $GetcustomerId);
        header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=" . $inventoryInvoiceID);
        exit();
      } elseif ($inventory_status == 'اجل') {
        // Add new inventory invoice
        $inventoryInvoiceID = $inventoryInvoiceController->addInventoryInvoice($date, $inventory_status, $GetcustomerId);
        header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=" . $inventoryInvoiceID);
        exit();
      } else {
        $errorMessage = "لا يوجد مبلغ كافي في الخزنة";
      }
    }
  } else {
    $errorMessage = "يرجى ملء جميع الحقول المطلوبة.";
  }
}

  
  if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_inventory'])) {
    // Handle form submission for deleting an inventory
    $InventoryInvoiceID = $_POST['InventoryInvoiceID'];
  
    if (!empty($InventoryInvoiceID)) {
      $inventoryInvoiceController->deleteInventoryInvoice($InventoryInvoiceID);
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();
    } else {
      $errorMessage = "رقم المنتج غير صحيح.";
    }
  }
//   if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_inventory'])) {
//     $InventoryInvoiceID = $_POST['InventoryInvoiceID'];
  
//     if (!empty($InventoryInvoiceID)) {
//         $InventoryInvoice = $inventoryInvoiceController->getInventoryInvoiceByID($InventoryInvoiceID);
//         $Vault = $vaultController->GetVault();
//         $cash = $Vault[0]['Cash'];

//         $TotalPriceForInvoices = $Prices->TotalPriceForinvoices(); // Fetch total price for all invoices
//         $TotalPriceForInventoryInvoice = $Prices->TotalPrice_for_inventoryinvoice();
//         $TotalCash = ($Vault[0]['Cash'] + $TotalPriceForInvoices) - $TotalPriceForInventoryInvoice;

//         $SummitionPrice = $Prices->TotalPrice_for_each_product($InventoryInvoiceID);
//         if($TotalCash >= $SummitionPrice){
//             $inventoryInvoiceController->updateStatusInventoryInvoice($InventoryInvoiceID);
//             header("Location: " . $_SERVER['PHP_SELF']);
//             exit();
//         } else {
//             $errorMessage = "المبلغ المطلوب لتسديد الفاتورة $SummitionPrice والمبلغ المتاح في الخزنة هو $TotalCash";
//         }
//     } else {
//         $errorMessage = "رقم المنتج غير صحيح.";
//     }
// }

$totalInvoices = count($currentInvoices); // Total number of invoices
$invoicesPerPage = 15; // Number of invoices per page
$totalPages = ceil($totalInvoices / $invoicesPerPage);

// Get the current page from the URL, if not set, default to page 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting invoice index
$startIndex = ($currentPage - 1) * $invoicesPerPage;

// Slice the invoices array to get the invoices for the current page
$currentInvoices = array_slice($currentInvoices, $startIndex, $invoicesPerPage);

// Calculate pagination range (3 pages before and after current page)
$startPage = max(1, $currentPage - 1);
$endPage = min($totalPages, $currentPage + 1);



?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
  <title>فواتير المشتريات</title>
  <meta name="description" content="" />
  <link rel="icon" type="image/x-icon" href="../assets/img/Elmostafa.ico" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
  <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
  <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
  <link rel="stylesheet" href="../assets/css/demo.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
  <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
  <script src="../assets/vendor/js/helpers.js"></script>
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
                        <li class="menu-item active open" style="">
                        <a href="javascript:void(0);" class="menu-link menu-toggle">
                          <i class='bx bx-data'></i>
                          <div data-i18n="Layouts">لمخزون</div>
                        </a>

                        <ul class="menu-sub">
                        <li class="menu-item active">
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
          <div class="content-wrapper">
            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
                <!-- Side Row -->
                <?php if ($errorMessage): ?>
                                <div class="alert alert-danger" role="alert">
                                  <?php echo htmlspecialchars($errorMessage); ?>
                                </div>
                              <?php endif; ?>

                <div class="col-md-6">
                  <div class="card mb-4">
                    <h5 class="card-header">اضافة فاتورة مشتريات</h5>
                    <div class="card-body">
                      <form method="post" action="inventoryInvoice.php">
                        <small class="text-light fw-semibold d-block">حالة الفاتورة</small>
                        <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="inventory_status" id="inlineRadio1" value="بيع" checked />
                                    <label class="form-check-label" for="inlineRadio1">بيع</label>
                                </div>

                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="inventory_status" id="inlineRadio3" value="اجل" required />
                                    <label class="form-check-label" for="inlineRadio3">اجل</label>
                                </div>
                                <div class="mt-3" id="additional-info">
                                  <label for="customerSelect" class="form-label">اسم المورد</label>
                                  <div class="mb-3">
                                      <select class="form-select w-100" id="customerSelect" name="CustomerId">
                                          <option selected disabled>اختر اسم المورد</option>
                                          <!-- Loop through customers -->
                                          <?php foreach ($Suppliers as $Supplier): ?>
                                              <option value="<?= $Supplier['CustomerId'] ?>"><?= $Supplier['CustomerName'] ?></option>
                                          <?php endforeach; ?>
                                      </select>
                                  </div>
                              </div>

                                
                                <div class="mt-3">
                                  <button name="add_inventory" class="btn btn-info" type="submit">اضافة فاتورة مشتريات جديده</button>
                                </div>
                      </form>
                    </div>
                  </div>
                </div>
                <!-- Outside -->
                <h5 class="card-header">اجمالي الفواتير <?php echo $calculateFinalTotalInventoryInvoice ?></h5>
                <!-- OutSide -->

<!-- Filter -->
<form method="get" action="inventoryInvoice.php">
<label for="status" class="me-2">فلتر بحالة البيع</label>
  <div class="mb-3 d-flex align-items-center">
    <select name="status" id="status" class="form-select me-2">
        <option value="" <?php if (!isset($_GET['status']) || $_GET['status'] == '') echo 'selected'; ?>>الجميع</option>
        <option value="اجل" <?php if (isset($_GET['status']) && $_GET['status'] == 'اجل') echo 'selected'; ?>>اجل</option>
        <option value="بيع" <?php if (isset($_GET['status']) && $_GET['status'] == 'بيع') echo 'selected'; ?>>بيع</option>
    </select>
    <button class="btn rounded-pill btn-success" type="submit">فلتر</button>
  </div>
</form>
<!-- Filter -->



                <!-- Table Content -->
                <div class="card">
                  <h5 class="card-header">فواتير المشتريات</h5>
                  <div class="table-responsive text-nowrap">
                    <table class="table table-striped">
                      <thead>
                        <tr>
                          <th>ID</th>
                          <th>تاريخ</th>
                          <th>الخصم</th>
                          <th>اجمالي السعر</th>
                          <th>اسم المورد</th>
                          <th>الحالة</th>
                          <th>تفاصيل</th> <!-- Added Actions column header -->
                        </tr>
                      </thead>
                      <tbody class="table-border-bottom-0">
                        <?php if ($currentInvoices): ?>
                          <?php $index = 0; ?>
                          <?php foreach ($currentInvoices as $inv):
                            $CustomerID = $inv['customerID'];
                            $Customer = $SupplierController->getSupplier($CustomerID);
                            $CustomerPayment = $inv['Payment'];
                            $TotalPayment = $Prices->TotalPrice_for_each_product($inv['InventoryInvoiceID']);
                            $rest_of_the_payment = $TotalPayment - $CustomerPayment - $inv['Discount'];
                             ?>
                            <?php $index++; ?>
                            <tr>
                              <td><?php echo htmlspecialchars($index); ?></td>
                              <td><?php echo htmlspecialchars($inv['Date']); ?></td>
                              <td><?php echo htmlspecialchars($inv['Discount']); ?></td>
                              <td><?php echo htmlspecialchars($Prices->TotalPriceForEachInventoryInvoice_AfterDiscount($inv['InventoryInvoiceID'])); ?></td>
                              <td><?php echo htmlspecialchars($inv['CustomerName']); ?></td>
                              <td><?php
                              if($rest_of_the_payment == 0){
                                echo "تم الدفع";
                              }elseif($inv['inventory_status'] =='اجل' && $rest_of_the_payment>0 ){
                                echo htmlspecialchars($rest_of_the_payment);
                                echo htmlspecialchars($inv['inventory_status']);
                                
                               } else{
                                echo htmlspecialchars($inv['inventory_status']);
                               }?></td>
                              <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <!-- First Option: Redirect to add_new_Inventory_invoice.php with InventoryInvoiceID -->
                                        <a class="dropdown-item" href="add_new_Inventory_invoice.php?InventoryInvoiceID=<?php echo htmlspecialchars($inv['InventoryInvoiceID']); ?>">
                                            <i class="bx bx-edit-alt me-1"></i> اضافة او تعديل
                                        </a>
                                        <!-- Second Option: Delete form -->
                                         <?php if(!$inventoryInvoiceController->isInventoryInvoiceExist($inv['InventoryInvoiceID'])): ?>
                                         <form method="post" action="inventoryInvoice.php" style="display: inline;">
                                         <input type="hidden" name="InventoryInvoiceID" value="<?php echo htmlspecialchars($inv['InventoryInvoiceID']); ?>" />
                                         <button class="dropdown-item" type="submit" name="delete_inventory" onclick="return confirm('هل أنت متأكد من حذف هذه الفاتورة؟');">
                                             <i class="bx bx-trash me-1"></i> حذف
                                         </button>
                                        </form>
                                        <?php endif; ?>
                                        

                                        <!-- New Button: Show only if inventory_status is 'اجل' -->
                                        <?php if ($inv['inventory_status'] === 'اجل'): ?>

                                          <a class="dropdown-item" href="SuppliersPayDebt.php?InventoryInvoiceID=<?php echo htmlspecialchars($inv['InventoryInvoiceID']); ?>">
                                          <i class="bx bx-money me-1"></i> سدد المبلغ
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="4">لا يوجد فواتير حتي الان</td>
                          </tr>
                        <?php endif; ?>
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

              </div>
            </div>
          </div>
          <!-- / Content -->


          <div class="content-backdrop fade"></div>
        </div>
      </div>
    </div>
  </div>
  <!-- Core JS -->
  <script src="../assets/vendor/libs/jquery/jquery.js"></script>
  <script src="../assets/vendor/libs/popper/popper.js"></script>
  <script src="../assets/vendor/js/bootstrap.js"></script>
  <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>
  <script src="../assets/vendor/js/menu.js"></script>
  <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>
  <script src="../assets/js/main.js"></script>
  <script src="../assets/js/dashboards-analytics.js"></script>
  <!-- Include Select2 CSS -->
<!-- Include Select2 CSS -->
<link href="../assets/css/select2.min.css" rel="stylesheet" />

<!-- Include jQuery -->
<script src="../assets/js/jquery-3.6.0.min.js"></script>

<!-- Include Select2 JS -->
<script src="../assets/js/select2.min.js"></script>
  <script>
  $(document).ready(function() {
    $('#customerSelect').select2({
      placeholder: "اختر اسم العميل",
      allowClear: true
    });
  });
</script>

  <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>