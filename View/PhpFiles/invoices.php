<?php
require_once '../../Controllers/InvoicesController.php';
require_once '../../Controllers/CustomerController.php';
require_once '../../Controllers/ProductController.php';
require_once '../../Controllers/PricesController.php';
require_once '../../Controllers/VaultController.php'; 


$Alert = null;
$errorMessage = null;
$customerController = new CustomerController();
$productController = new ProductController();
$pricesController = new PricesController();
$VaultController = new VaultController();

$customers = $customerController->getAllCustomers();
$products = $productController->getAllProducts();

$invoicesController = new InvoicesController();

// Check if a date filter is set, otherwise use the current date
$selectedDate = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

// Fetch invoices filtered by the selected date
$invoices = $invoicesController->getAllInvoices($selectedDate);
if(!$pricesController->TotalPriceForinvoicesEachDay($selectedDate)){
  $TotalPriceForDay = 0;
}else{
  $TotalPriceForDay = $pricesController->TotalPriceForinvoicesEachDay($selectedDate);
}

// Handle adding a new invoice
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_invoice'])) {
    $productID = isset($_POST['productID']) ? trim($_POST['productID']) : '';
    $quantity = isset($_POST['quantity']) ? trim($_POST['quantity']) : '';
    $status = isset($_POST['status']) ? trim($_POST['status']) : '';
    $GetcustomerId = isset($_POST['CustomerId']) ? trim($_POST['CustomerId']) : '';
    $date = date("Y-m-d H:i:s");
    $discount = $_POST['discount'];
    // Check that all required fields are filled in
    if (empty($productID)) {
        $errorMessage = "يرجى اختيار المنتج.";
    } elseif (empty($quantity)) {
        $errorMessage = "يرجى إدخال الكمية.";
    } elseif (empty($status)) {
        $errorMessage = "يرجى اختيار الحالة.";
    } elseif ($status == 'اجل' && empty($GetcustomerId)) {
        $errorMessage = "يرجى اختيار العميل في حالة الاجل.";
    } else {
        // Process the form if all fields are filled
        $My_product = $productController->getProduct($productID);
        $totalPrice = ($quantity * $My_product[0]['SalePrice']);

        if ($status == 'بيع' || $status == 'مسترجع') {
            $GetcustomerId = 1; // Default customer ID for sale or returned products
        }
        $totalQuantityInvoiceProduct = $pricesController->TotalQuantity_for_invoice_product($productID);
        $totalQuantityInvoices = $pricesController->TotalQuantity_for_invoices($productID);
        $AlreadyQuantity = $totalQuantityInvoiceProduct - $totalQuantityInvoices;

        if ($totalQuantityInvoiceProduct > $totalQuantityInvoices && $totalQuantityInvoiceProduct >= ($totalQuantityInvoices + $quantity)) {
            $totalPrice = $totalPrice-$discount;
            $BuyPrice = $My_product[0]['BuyPrice'];
            $SalePrice = $My_product[0]['SalePrice'];
            $invoicesController->addInvoice($productID, $quantity, $status, $GetcustomerId, $date, $totalPrice,$discount,$BuyPrice,$SalePrice);
            // Redirect to the same page to prevent resubmission
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
          

        } else {
            $errorMessage = "الكمية غير كافية في المخزون ,الكمية في المخزون هي $AlreadyQuantity";
        }
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_invoice'])) {
    $invoiceId = $_POST['invoiceId'];

    if (!empty($invoiceId)) {
        $Deletedinvoice = $invoicesController->getInvoiceById($invoiceId);
        $invoicesController->deleteInvoice($invoiceId);
        $CustomerID = $Deletedinvoice[0]['CustomerId'];
        $Customer = $customerController->getCustomer($CustomerID);
        $CustomerPayment = $Customer[0]['Payment'];
        $CustomerDebt = $customerController->Calculate_Customer_Debt($CustomerID);
        if(!$customerController->Calculate_Customer_Debt($CustomerID)){
          $CustomerDebt = 0;
        }
        $Collector_of_the_account = $CustomerDebt - $CustomerPayment ;
        if($Collector_of_the_account<0){
          $payment = abs($Collector_of_the_account);
          $customerController->DecreasePaymentCustomer($payment,$CustomerID);
          $Alert = "لقد تم خصم $payment لحساب هذا العميل";
      }
      header("Location: " . $_SERVER['PHP_SELF']);
      exit();

    } else {
        $errorMessage = "رقم المنتج غير صحيح.";
    }
}

$totalInvoices = count($invoices); // Total number of invoices
$invoicesPerPage = 15; // Number of invoices per page
$totalPages = ceil($totalInvoices / $invoicesPerPage);

// Get the current page from the URL, if not set, default to page 1
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the starting invoice index
$startIndex = ($currentPage - 1) * $invoicesPerPage;

// Slice the invoices array to get the invoices for the current page
$currentInvoices = array_slice($invoices, $startIndex, $invoicesPerPage);

// Calculate pagination range (3 pages before and after current page)
$startPage = max(1, $currentPage - 1);
$endPage = min($totalPages, $currentPage + 1);

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

    <title>فواتير البيع</title>
    
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
                <li class="menu-item active">
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
                      <?php if ($errorMessage): ?>
                                <div class="alert alert-danger alert-dismissible" role="alert">
                                  <?php echo htmlspecialchars($errorMessage); ?>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                              <?php endif; ?>
                              <?php if ($Alert): ?>
                                <div class="alert alert-warning alert-dismissible" role="alert">
                                  <?php echo htmlspecialchars($Alert); ?>
                                  <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                              <?php endif; ?>
                          <!-- side Row -->
                          <div class="col-xl">
                            <div class="card mb-4">
                              <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">الجرد </h5>
                                <small class="text-muted float-end">الجرد والفواتير</small>
                              </div>
                              <div class="card-body">
                              <form method="post" action="invoices.php">
                              <label for="productSelect" class="form-label">اسم المنتج</label>
                                <div class="mb-3">
                                    <select class="form-select" id="productSelect" name="productID" required>
                                      <option selected="" disabled="">اختر اسم المنتج</option>
                                      <!-- Loop through products -->
                                      <?php foreach ($products as $product): ?>
                                          <option value="<?= $product['productId'] ?>"><?= $product['ProductName'] ?></option>
                                      <?php endforeach; ?>
                                  </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantityInput" class="form-label">الكمية</label>
                                    <input type="number" class="form-control" id="quantityInput" name="quantity" placeholder="اضف الكمية" required>
                                </div>
                                <div class="mb-3">
                                    <label for="discountInput" class="form-label">خصم</label>
                                    <input type="number" class="form-control" id="discountInput" name="discount" placeholder="اضف الخصم" required>
                                </div>
                                <div class="form-check form-check-inline mt-3">
                                    <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="بيع" required />
                                    <label class="form-check-label" for="inlineRadio1">بيع</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="inlineRadio3" value="اجل" required />
                                    <label class="form-check-label" for="inlineRadio3">اجل</label>
                                </div>
                                <div class="mt-3" id="additional-info" style="display: none;">
                                    <label for="customerSelect" class="form-label">اسم العميل</label>
                                    <select class="form-select" id="customerSelect" name="CustomerId">
                                        <option>اختر اسم العميل</option>
                                        <!-- Loop through customers -->
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['CustomerId'] ?>"><?= $customer['CustomerName'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" name="add_invoice" class="btn btn-primary">حفظ</button>
                                </div>
                            </form>
                              </div>
                            </div>
                          </div>
                          
                      <h5>اجمالي المبيعات: <?php echo htmlspecialchars($TotalPriceForDay); ?></h5>
                      <!-- outside -->
                        <!-- Filter -->
                      <!-- Date Filter Form -->
                  <form method="get" action="">
                      <div class="input-group mb-3">
                          <input type="date" class="form-control" name="date" value="<?php echo htmlspecialchars(isset($_GET['date']) ? $_GET['date'] : date('Y-m-d')); ?>">
                          <button class="btn btn-primary" type="submit">Filter</button>
                      </div>
                  </form>

                      <!-- after Filter -->
                     <!-- Table Content -->
<div class="card">
    <h5 class="card-header">المخزون</h5>
    <div class="table-responsive text-nowrap">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>اسم المنتج</th>
                    <th>الكمية</th>
                    <th>التاريخ</th>
                    <th>الخصم</th>
                    <th>اجمالي الربح</th>
                    <th>سعر البيع</th>
                    <th>اجمالي السعر</th>
                    <th>اسم العميل</th>
                    <th>الحالة</th>
                    <th>تفاصيل</th> <!-- Added Actions column header -->
                </tr>
            </thead>
            <tbody class="table-border-bottom-0">
                <?php if ($currentInvoices): ?>
                    <?php foreach ($currentInvoices as $index => $invoice): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($startIndex + $index + 1); ?></td>
                            <td><?php echo htmlspecialchars($invoice['ProductName']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['Quantity']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['Date']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['Discount']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['TotalProfit']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['SalePrice']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['TotalPrice']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['CustomerName']); ?></td>
                            <td><?php echo htmlspecialchars($invoice['Status']); ?></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" href="invoices-edit.php?InvoiceID=<?php echo htmlspecialchars($invoice['InvoiceID']); ?>">
                                            <i class="bx bx-edit-alt me-1"></i> تعديل
                                        </a>
                                        <form method="post" action="" style="display: inline;">
                                            <input type="hidden" name="invoiceId" value="<?php echo htmlspecialchars($invoice['InvoiceID']); ?>">
                                            <button type="submit" class="dropdown-item" name="delete_invoice" onclick="return confirm('هل انت متأكد من حذف هذه الفاتورة؟');">
                                                <i class="bx bx-trash me-1"></i> حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11">لا يوجد فواتير في هذا اليوم</td>
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
    <!-- Script For Product -->

    <script>
  document.addEventListener('DOMContentLoaded', (event) => {
    const radioAjal = document.getElementById('inlineRadio3');
    const additionalInfoDiv = document.getElementById('additional-info');

    radioAjal.addEventListener('change', (event) => {
      if (event.target.checked) {
        additionalInfoDiv.style.display = 'block';
      }
    });

    const otherRadios = document.querySelectorAll('input[name="status"]:not(#inlineRadio3)');
    otherRadios.forEach(radio => {
      radio.addEventListener('change', (event) => {
        if (event.target.checked) {
          additionalInfoDiv.style.display = 'none';
        }
      });
    });
  });
</script>
<script>
function populateModal(invoiceID, productName, quantity, customerID) {
  // Set product name
  var productSelect = document.getElementById('productSelect-' + invoiceID);
  for (var i = 0; i < productSelect.options.length; i++) {
    if (productSelect.options[i].text === productName) {
      productSelect.selectedIndex = i;
      break;
    }
  }
  
  // Set quantity
  document.getElementById('quantityInput-' + invoiceID).value = quantity;

  // If you have a customer ID, you can also set it like this:
  // document.getElementById('customerSelect-' + invoiceID).value = customerID;
}
</script>

<!-- Include Select2 CSS -->
<link href="../assets/css/select2.min.css" rel="stylesheet" />

<!-- Include jQuery -->
<script src="../assets/js/jquery-3.6.0.min.js"></script>

<!-- Include Select2 JS -->
<script src="../assets/js/select2.min.js"></script>

<script>
  $(document).ready(function() {
    $('#productSelect').select2({
      placeholder: "اختر اسم المنتج",
      allowClear: true
    });
  });
</script>
<script>
  $(document).ready(function() {
    $('#customerSelect').select2({
      placeholder: "اختر اسم العميل",
      allowClear: true
    });
  });
</script>

    <!-- Final Script -->
    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
