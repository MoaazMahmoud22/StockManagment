<?php
require_once '../../Controllers/VaultController.php'; 
require_once '../../Controllers/PricesController.php'; 
require_once '../../Controllers/ReportCotnroller.php'; 

$VaultController = new VaultController(); 
$PricesController = new PricesController(); 
$ReportController = new ReportCotnroller(); 







$Vault = $VaultController->GetVault();

$Count_of_Customers = $ReportController->GetCount_of_Customers();
$Count_of_Suppliers = $ReportController->GetCount_of_Suppliers();
$Count_of_Invoices = $ReportController->GetCount_of_Invoices();
$TotalDebtCustomer = $ReportController->GetTotalDebtCustomer();
$TotalDebtToSuppliers = $ReportController->GetTotalDebtToSuppliers();
$calculateFinalTotalInventoryInvoice = $PricesController->calculateFinalTotalInventoryInvoice();
$TotalPriceForInvoices = $PricesController->TotalPriceForinvoices();
$TotalPayPriceForAllCustomers = $PricesController->TotalPayPriceForAllCustomers();
$TotalPayPriceForAllSuppliers = $PricesController->TotalPayPriceForAllSuppliers();
$TotalCash = ($Vault[0]['Cash']+$TotalPriceForInvoices+$TotalPayPriceForAllCustomers)-($calculateFinalTotalInventoryInvoice+$TotalPayPriceForAllSuppliers);
$All_Profits_Across_All_Invoices =($TotalPriceForInvoices+$TotalPayPriceForAllCustomers)-($calculateFinalTotalInventoryInvoice+$TotalPayPriceForAllSuppliers);

if(!$Count_of_Customers){
  $Count_of_Customers = 0;
}
if(!$TotalDebtToSuppliers){
  $TotalDebtToSuppliers = 0;
}
if(!$Count_of_Invoices){
  $Count_of_Invoices = 0;
}
if(!$TotalDebtToSuppliers){
  $TotalDebtToSuppliers = 0;
}
if(!$TotalDebtCustomer){
  $TotalDebtCustomer = 0;
}
if( $All_Profits_Across_All_Invoices<0 ){
  $All_Profits_Across_All_Invoices=0;
}

if(!$Count_of_Suppliers ){
  $Count_of_Suppliers=0;
}

// Fetch sales data by category
$SalesByCategories = $ReportController->SalesByCategories();

$categoryNames = [];
$salesPercentages = [];

if (isset($SalesByCategories['labels']) && isset($SalesByCategories['series'])) {
    $categoryNames = $SalesByCategories['labels'];

    // Ensure salesPercentages are floats
    $salesPercentages = array_map('floatval', $SalesByCategories['series']);
}


$dates = $PricesController->getMinMaxDates();

if ($dates) {
    $minYear = date('Y', strtotime($dates['min']));
    $maxYear = date('Y', strtotime($dates['max']));
} else {
    // Set defaults if no dates are found
    $minYear = date('Y');
    $maxYear = date('Y');
}

$currentMonth = date('m');
$currentYear = date('Y');
$profit = $PricesController->calculateProfitForSpecificMonthAndYear($currentMonth, $currentYear);

$TotalCashINMonth = $PricesController->TotalPriceForMonth($currentYear, $currentMonth);

if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];

    // Call the function to calculate profit
    $profit = $PricesController->calculateProfitForSpecificMonthAndYear($month, $year);
    $TotalCashINMonth = $PricesController->TotalPriceForMonth($year, $month);
}

if($profit<0){
  $profit=0;
}
if(!$TotalCashINMonth){
  $TotalCashINMonth=0;
}


?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>الرئيسية - التقارير</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/Elmostafa.ico" />
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
    
    <!-- Icons -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />
    
    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />
    
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />
    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />
    
    <!-- Helpers -->
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
                        <li class="menu-item active">
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
                       <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                                <img src="../assets/img/icons/unicons/wallet-info.png" alt="Credit Card" class="rounded">
                              </div>
                            </div>
                            <span>صافي ارباح المبيعات للبيع والاجل</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $All_Profits_Across_All_Invoices  ?> EG</h3>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-money'></i></span>
                              </div>
                            </div>
                            <span>اجمالي الاجل للعملاء</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $TotalDebtCustomer ?> EG</h3>
                          </div>
                        </div>
                      </div>
                       <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-money'></i></span>
                              </div>
                            </div>
                            <span>اجمالي الاجل للموردين</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $TotalDebtToSuppliers ?> EG</h3>
                          </div>
                        </div>
                      </div>
                      
                       <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-primary"><i class='bx bxs-user' style='color:#03c3ec'  ></i></span>
                              </div>
                            </div>
                            <span>عدد العملاء</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $Count_of_Customers ?></h3>
                          </div>
                        </div>
                      </div>
                       <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-primary"><i class='bx bxs-user' style='color:#03c3ec'  ></i></span>
                              </div>
                            </div>
                            <span>عدد الموردين</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $Count_of_Suppliers ?></h3>
                          </div>
                        </div>
                      </div>
                       <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-trending-up'></i></i></span>
                              </div>
                            </div>
                            <span>عدد عمليات البيع</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $Count_of_Invoices ?></h3>
                          </div>
                        </div>
                      </div>

                      <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="card">
                          <div class="card-body">
                            <div class="card-title d-flex align-items-start justify-content-between">
                              <div class="avatar flex-shrink-0">
                              <span class="avatar-initial rounded bg-label-primary"><i class='bx bx-trending-up'></i></i></span>
                              </div>
                            </div>
                            <span>البيع الشهري</span>
                            <h3 class="card-title text-nowrap mb-1"><?php echo $TotalCashINMonth ?> EG</h3>
                          </div>
                        </div>
                      </div>


                       <!-- outside -->
 <!-- Sales Statistics Card -->
<div class="col-md-6 col-lg-4 col-xl-4 order-0 mb-4">
    <div class="card h-100">
        <div class="card-header d-flex align-items-center justify-content-between pb-0">
            <div class="card-title mb-0">
                <h5 class="m-0 me-2">احصائيات الاصناف</h5>
                <small class="text-muted">
                    <?php 
                        $totalSales = array_sum($salesPercentages);
                        echo number_format($totalSales);
                    ?>
                </small>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($salesPercentages) || empty($SalesByCategories['labels'])): ?>
                <!-- No Data Message -->
                <div class="text-center">
                    <p class="text-muted">لا توجد بيانات بعد</p>
                </div>
            <?php else: ?>
                <!-- Chart and Data List -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex flex-column align-items-center gap-1">
                        <h2 class="mb-2"><?php echo number_format($totalSales); ?></h2>
                        <span>اجمالي النسبة</span>
                    </div>
                    <div id="orderStatisticsChart"></div>
                </div>
                <ul class="p-0 m-0">
                    <?php foreach ($SalesByCategories['labels'] as $index => $category): ?>
                        <li class="d-flex mb-4 pb-1">
                            <div class="avatar flex-shrink-0 me-3">
                                <span class="avatar-initial rounded bg-label-primary"><i class="bx bx-category-alt"></i></span>
                            </div>
                            <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                                <div class="me-2">
                                    <h6 class="mb-0"><?php echo $category; ?></h6>
                                </div>
                                <div class="user-progress">
                                    <small class="fw-semibold"><?php echo number_format($salesPercentages[$index]); ?>%</small>
                                </div>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<!--/ Sales Statistics Card -->

                            <!-- Profit Fiter -->

<!-- Profit Form -->
<div class="col-12 col-md-8 col-lg-4 order-3 order-md-2">
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title">تصفية الأرباح حسب الشهر والسنة</h5>
        </div>
        <div class="card-body">
            <form method="get" action="">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="month">الشهر</label>
                        <select id="month" name="month" class="form-control">
                            <?php 
                            // Get the selected month from the form submission or use the default current month
                            $selectedMonth = isset($_GET['month']) ? (int)$_GET['month'] : $currentMonth;
                            for ($i = 1; $i <= 12; $i++) {
                                $selected = ($i == $selectedMonth) ? 'selected' : '';
                                echo "<option value='$i' $selected>$i</option>";
                            } 
                            ?>
                        </select>
                    </div>
                    <div class="form-group col-md-6">
                        <label for="year">السنة</label>
                        <select id="year" name="year" class="form-control">
                            <?php 
                            // Get the selected year from the form submission or use the default current year
                            $selectedYear = isset($_GET['year']) ? (int)$_GET['year'] : $currentYear;
                            for ($i = $minYear; $i <= $maxYear; $i++) {
                                $selected = ($i == $selectedYear) ? 'selected' : '';
                                echo "<option value='$i' $selected>$i</option>";
                            } 
                            ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary mt-3">Filter</button>
            </form>
        </div>
        <div class="card-footer">
            <?php if (isset($_GET['month']) && isset($_GET['year'])) { ?>
                <p>Profit for <?php echo "$selectedMonth/$selectedYear"; ?>: <?php echo $profit; ?> EG</p>
            <?php } else { ?>
                <p>Current Profit: <?php echo $profit; ?> EG</p>
            <?php } ?>
        </div>
    </div>
</div>

                  

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
    <!-- <script src="../assets/js/dashboards-analytics.js"></script> -->

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
        <script>
            // Retrieve category names and sales percentages from PHP
            const categoryNames = <?php echo json_encode($categoryNames); ?>;
            const salesPercentages = <?php echo json_encode($salesPercentages); ?>;

            // Validate data
            if (!Array.isArray(categoryNames) || !Array.isArray(salesPercentages)) {
                console.error('Invalid data arrays');
            } else if (categoryNames.length === 0 || salesPercentages.length === 0) {
                console.error('Data arrays are empty');
            } else if (salesPercentages.some(isNaN)) {
                console.error('Sales percentages contain invalid numbers');
            } else {
                // Create the pie chart
                var options = {
                    series: salesPercentages,
                    chart: {
                        type: 'pie',
                    },
                    labels: categoryNames,
                    responsive: [{
                        breakpoint: 480,
                        options: {
                            chart: {
                                width: 200
                            },
                            legend: {
                                position: 'bottom'
                            }
                        }
                    }]
                };

                var chart = new ApexCharts(document.querySelector("#orderStatisticsChart"), options);
                chart.render();
            }
        </script>
    </body>
</html>
