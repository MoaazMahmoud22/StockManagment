<?php
require_once '../../Controllers/InvoicesController.php';
require_once '../../Controllers/CustomerController.php';
require_once '../../Controllers/ProductController.php';
require_once '../../Controllers/PricesController.php';
require_once '../../Controllers/VaultController.php';

$Alert = null;
$invoicesController = new InvoicesController();
$VaultController = new VaultController();
$customerController = new CustomerController();
$productController = new ProductController();
$pricesController = new PricesController();
$errorMessage=null;
$invoiceID = isset($_GET['InvoiceID']) ? intval($_GET['InvoiceID']) : null;
$invoice = null;

if ($invoiceID) {
    $invoice = $invoicesController->getInvoiceById($invoiceID); // Assume you have a method to get the invoice by ID
    $products = $productController->getAllProducts(); // Get all products
    $customers = $customerController->getAllCustomers();
    $old_quantity =$invoice[0]['Quantity']; // Get all customers
    $SalePrice = $invoice[0]['SalePrice'];
} else {
    echo "Invalid Invoice ID.";
    exit;
}

        $CustomerID = $invoice[0]['CustomerId'];
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

      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $productID = intval($_POST['productID']);
        $newQuantity = intval($_POST['quantity']);
        $status = $_POST['status'];
        $discount = $_POST['discount'];
        $result = null;
      
        // Ensure required fields are filled in
        if ($productID && $newQuantity && $status) {
            $customerId = ($status === 'بيع' || $status === 'مسترجع') ? 1 : intval($_POST['CustomerId']);
            
            if ($status === 'اجل' && !$customerId) {
                $errorMessage = "من فضلك اختر اسم العميل ";
            } else {
                $totalQuantityInvoiceProduct = $pricesController->TotalQuantity_for_invoice_product($productID);
                $totalQuantityInvoices = $pricesController->TotalQuantity_for_invoices($productID);
                $AlreadyQuantity = $totalQuantityInvoiceProduct - $totalQuantityInvoices;
                $newQuantityDifference = $newQuantity - $old_quantity;
                $totalPrice = $newQuantity * $SalePrice;
                $totalPrice -= $discount;
                if ($old_quantity < $newQuantity) {
                    if ($totalQuantityInvoiceProduct > $totalQuantityInvoices && 
                        $totalQuantityInvoiceProduct >= ($totalQuantityInvoices + $newQuantityDifference)) {

                            $result = $invoicesController->updateInvoice($invoiceID, $productID, $newQuantity, $status, $customerId, $totalPrice, $discount);
                    } else {
                        $errorMessage = "$AlreadyQuantity: الكمية غير كافية في المخزون.";
                    }
                } else {
                        $result = $invoicesController->updateInvoice($invoiceID, $productID, $newQuantity, $status, $customerId, $totalPrice, $discount);
                }
                
                if ($result === "Invoice updated successfully") {
                    header("Location: invoices-edit.php?InvoiceID=$invoiceID");
                    exit();
                }
            }
        } else {
            $errorMessage = "All required fields must be filled in.";
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
                                <div class="alert alert-danger" role="alert">
                                  <?php echo htmlspecialchars($errorMessage); ?>
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
                              <form method="post" action="invoices-edit.php?InvoiceID=<?= htmlspecialchars($invoiceID) ?>">
                              <label for="productSelect" class="form-label">اسم المنتج</label>
                                <div class="mb-3">
                                    <select class="form-select" id="productSelect" name="productID" required>
                                        <option disabled="">اختر اسم المنتج</option>
                                        <?php foreach ($products as $product): ?>
                                            <option value="<?= $product['productId'] ?>" <?= $product['productId'] == $invoice[0]['ProductId'] ? 'selected' : '' ?>>
                                                <?= $product['ProductName'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="quantityInput" class="form-label">الكمية</label>
                                    <input type="number" class="form-control" id="quantityInput" name="quantity" value="<?= htmlspecialchars($invoice[0]['Quantity']) ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="DiscountInput" class="form-label">الخصم</label>
                                    <input type="number" class="form-control" id="DiscountInput" name="discount" value="<?= htmlspecialchars($invoice[0]['Discount']) ?>" required>
                                </div>
                                <?php if($invoice[0]['Status'] != 'اجل'): ?>
                                    <div class="form-check form-check-inline mt-3">
                                        <input class="form-check-input" type="radio" name="status" id="inlineRadio1" value="بيع" <?= $invoice[0]['Status'] == 'بيع' ? 'checked' : '' ?> required />
                                        <label class="form-check-label" for="inlineRadio1">بيع</label>
                                    </div>
                                <?php endif; ?>

                                <!-- <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="inlineRadio2" value="مسترجع" required />
                                    <label class="form-check-label" for="inlineRadio2">مسترجع</label>
                                </div> -->
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="status" id="inlineRadio3" value="اجل" <?= $invoice[0]['Status'] == 'اجل' ? 'checked' : '' ?> required />
                                    <label class="form-check-label" for="inlineRadio3">اجل</label>
                                </div>
                                <div class="mt-3" id="additional-info" style="display: <?= $invoice[0]['Status'] == 'اجل' ? 'block' : 'none' ?>;">
                                    <label for="customerSelect" class="form-label">اسم العميل</label>
                                    <select class="form-select" id="customerSelect" name="CustomerId">
                                        <option>اختر اسم العميل</option>
                                        <?php foreach ($customers as $customer): ?>
                                            <option value="<?= $customer['CustomerId'] ?>" <?= $customer['CustomerId'] == $invoice[0]['CustomerId'] ? 'selected' : '' ?>>
                                                <?= $customer['CustomerName'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mt-3">
                                    <button type="submit" class="btn btn-primary">تحديث</button>
                                </div>
                            </form>

                              </div>
                            </div>
                          </div>

                      
                      <!-- outside -->
                        <!-- Filter -->
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
    <!-- Final Script -->
    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    
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

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
  </body>
</html>
