<?php
require_once '../../Controllers/ProductController.php';
require_once '../../Controllers/InventoryInvoiceController.php';
require_once '../../Controllers/PricesController.php';
require_once '../../Controllers/VaultController.php';
require_once '../../Controllers/SuppliersController.php';

$errorMessage = null;
$Alert = null;
$productController = new ProductController();
$inventoryInvoiceController = new InventoryInvoiceController();
$vaultController = new VaultController();
$pricesController = new PricesController();
$suppliersController = new SuppliersController();
$inventoryInvoiceID = $_GET['InventoryInvoiceID'];

$products = $productController->getAllProducts();
$TotalPriceForInvoices = $pricesController->TotalPriceForinvoices();

$InventoryInvoice = $inventoryInvoiceController->getInventoryInvoiceByID($inventoryInvoiceID);
$Vault = $vaultController->GetVault();
$calculateFinalTotalInventoryInvoice = $pricesController->calculateFinalTotalInventoryInvoice();

$TotalPayPriceForAllCustomers = $pricesController->TotalPayPriceForAllCustomers();
$TotalPayPriceForAllSuppliers = $pricesController->TotalPayPriceForAllSuppliers();
$TotalCash = ($Vault[0]['Cash']+$TotalPriceForInvoices+$TotalPayPriceForAllCustomers)-($calculateFinalTotalInventoryInvoice+$TotalPayPriceForAllSuppliers);

//For Debtor
$CustomerID = $InventoryInvoice[0]['customerID'];
$Customer = $suppliersController->getSupplier($CustomerID);
$CustomerPayment = $InventoryInvoice[0]['Payment'];
$TotalPayment = $pricesController->TotalPrice_for_each_product($InventoryInvoice[0]['InventoryInvoiceID']);
$rest_of_the_payment = $TotalPayment - $CustomerPayment; 

if($rest_of_the_payment<0){
    $payment = abs($rest_of_the_payment);
    $suppliersController->DecreasePaymentSupplier($payment,$CustomerID,$inventoryInvoiceID);
    $Alert = "لقد تم خصم $payment لحساب هذا العميل";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['add_row'])) {
        $productID = $_POST['ProductID'];
        $quantity = $_POST['quantity'];
        $productInfo = $productController->getProduct($productID);
        $totalPriceForProduct =  $productInfo[0]['BuyPrice'] * $quantity;
        $BuyPrice= $productInfo[0]['BuyPrice'];
        $SalePrice = $productInfo[0]['SalePrice'];
        if($InventoryInvoice[0]['inventory_status'] =='بيع'){
            if($TotalCash>=$totalPriceForProduct){
                $inventoryInvoiceController->addProductToInvoice($inventoryInvoiceID, $productID, $quantity,$totalPriceForProduct,$BuyPrice,$SalePrice);
                header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=$inventoryInvoiceID");
                exit();
            }else{
                $errorMessage= "الخزنة يوجد بها $TotalCash المبلغ غير موجود في الخزنة واجمالي هذا المنتج $totalPriceForProduct";
            }
        }else{
            $inventoryInvoiceController->addProductToInvoice($inventoryInvoiceID, $productID, $quantity,$totalPriceForProduct,$BuyPrice,$SalePrice);
            header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=$inventoryInvoiceID");
            exit();
        }

  
        
    } elseif (isset($_POST['update_row'])) {
        $id = $_POST['id'];
        $productID = $_POST['ProductID'];
        $quantity = $_POST['quantity'];
        $productInfo = $productController->getProduct($productID);

        $invoice_product = $inventoryInvoiceController->getinvoice_productByID($id);
        $productID = $invoice_product[0]['productID'];

        $totalQuantityInvoiceProduct = $pricesController->TotalQuantity_for_invoice_product($productID);
        $totalQuantityInvoices = $pricesController->TotalQuantity_for_invoices($productID);

        $AlreadyQuantity =$totalQuantityInvoiceProduct-$totalQuantityInvoices;
        $OldQuantity = $invoice_product[0]['Quantity'];
        $totalPriceForProduct =  $invoice_product[0]['BuyPrice'] * $quantity;
        if($quantity<$OldQuantity){
            $NewQuantity = $OldQuantity - $quantity;
            if($totalQuantityInvoices<=($totalQuantityInvoiceProduct-$NewQuantity) ){
                
                if($InventoryInvoice[0]['inventory_status'] =='بيع'){
                    if($TotalCash>=$totalPriceForProduct){
                        $inventoryInvoiceController->updateProductInInvoice($id, $productID, $quantity,$totalPriceForProduct);
                        header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=$inventoryInvoiceID");
                        exit();
                        
                    }else{
                        $errorMessage= "الخزنة يوجد بها $TotalCash المبلغ غير موجود في الخزنة واجمالي هذا المنتج $totalPriceForProduct";
                    }
                }else{
                    $inventoryInvoiceController->updateProductInInvoice($id, $productID, $quantity,$totalPriceForProduct);
                    header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=$inventoryInvoiceID");
                    exit();
                }
            }else{
                $errorMessage = "لا يمكن تعديل هذا المنتج لعدم وجود كمية كافيه"; 
            }
            
        }else{
            $inventoryInvoiceController->updateProductInInvoice($id, $productID, $quantity,$totalPriceForProduct);
            header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=$inventoryInvoiceID");
            exit();
        }
    

        
    } elseif (isset($_POST['delete_row'])) {
        $id = $_POST['id'];
        $Inventory_invoice = $inventoryInvoiceController->getinvoice_productByID($id);

        $productID = $Inventory_invoice[0]['productID'];

        $totalQuantityInvoiceProduct = $pricesController->TotalQuantity_for_invoice_product($productID);

        $totalQuantityInvoices = $pricesController->TotalQuantity_for_invoices($productID);

        $AlreadyQuantity =$totalQuantityInvoiceProduct-$totalQuantityInvoices;

        $DeleteQuantity = $Inventory_invoice[0]['Quantity'];

        if($totalQuantityInvoices<=($totalQuantityInvoiceProduct-$DeleteQuantity))
        {
            if($inventoryInvoiceController->deleteProductFromInvoice($id))
            {
                $TotalPayment = $pricesController->TotalPrice_for_each_product($inventoryInvoiceID);

                if($TotalPayment <=0)
                {
                    $inventoryInvoiceController->DiscountInventoryInvoice($inventoryInvoiceID,0);
                    header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=$inventoryInvoiceID");
                    exit();
                }
            }       
        }else{
            $errorMessage = "لا يمكن حذف هذا المنتج لعدم وجود كمية كافيه";
        }
        
    }

    // Calculate and update total price
    $productsInInvoice = $inventoryInvoiceController->getProductsByInvoiceID($inventoryInvoiceID);
    $totalPrice = 0;
    foreach ($productsInInvoice as $productInInvoice) {
        $productInfo = $productController->getProduct($productInInvoice['productID']);

        $totalPrice += $productInfo[0]['BuyPrice'] * $productInInvoice['Quantity'];
    }
    if (isset($_POST['Discount'])&&isset($_POST['DiscountValue'])){
        $Discount = $_POST['DiscountValue'];
        $TotalPayment = $pricesController->TotalPrice_for_each_product($inventoryInvoiceID);
        if($Discount>=$TotalPayment){
            $errorMessage = "لا يمكن خصم مبلغ من هذه الفاتورة اجمالي الفاتورة $TotalPayment";
        }else{
            $inventoryInvoiceController->DiscountInventoryInvoice($inventoryInvoiceID,$Discount);
            header("Location: add_new_Inventory_invoice.php?InventoryInvoiceID=$inventoryInvoiceID");
            exit();
        }
    }

}


// Get current products in the invoice
$productsInInvoice = $inventoryInvoiceController->getProductsByInvoiceID($inventoryInvoiceID);
?>

<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed" dir="ltr" data-theme="theme-default" data-assets-path="../assets/" data-template="vertical-menu-template-free">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>اضافة - تعديل فاتور المشتريات</title>
    <meta name="description" content="" />
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />
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
                        <li class="menu-item">
                        <a href="Category.php" class="menu-link">
                            <i class='bx bx-category-alt'></i>
                            <div data-i18n="Analytics">الاصناف</div>
                        </a>
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
                    <div class="content-wrapper">
                        <div class="container-xxl flex-grow-1 container-p-y">
                            <div class="row">
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
                                <div class="row">
                                    <div class="col-xl">
                                        <div class="card mb-4">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">اضافة فاتورة مشتريات</h5>
                                                <small class="text-muted float-end">اضافة للمخزون</small>
                                            </div>
                                            <div class="card-body">
                                            <form id="invoiceForm" method="post" action="add_new_Inventory_invoice.php?InventoryInvoiceID=<?php echo $inventoryInvoiceID; ?>">
                                                <div class="mb-3">
                                                    <label for="productSelect" class="form-label">اسم المنتج</label>
                                                    <select class="form-select" id="productSelect" name="ProductID" aria-label="Default select example" required>
                                                        <option value="" selected="">اختر اسم المنتج</option>
                                                        <?php foreach ($products as $product): ?>
                                                            <option value="<?php echo htmlspecialchars($product['productId']); ?>">
                                                                <?php echo htmlspecialchars($product['ProductName']); ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                                <div>
                                                    <label for="quantityInput" class="form-label">الكمية</label>
                                                    <input type="number" class="form-control" id="quantityInput" name="quantity" placeholder="اضف الكمية" aria-describedby="defaultFormControlHelp" required>
                                                </div>
                                                <button type="submit" name="add_row" class="btn btn-secondary">إضافة</button>
                                             
                                            </form>
                                            </div>
                                        </div>
                                        <div class="card mb-4">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <h5 class="mb-0">خصم من الفاتورة</h5>
                                            </div>
                                            <div class="card-body">
                                            <form id="invoiceForm" method="post" action="add_new_Inventory_invoice.php?InventoryInvoiceID=<?php echo $inventoryInvoiceID; ?>">
                                                <div>
                                                    <label for="DiscountInput" class="form-label">المبلغ</label>
                                                    <input type="number" class="form-control" id="DiscountInput" value="<?php echo $InventoryInvoice[0]['Discount']?>" name="DiscountValue" placeholder="اضف الكمية" aria-describedby="defaultFormControlHelp" required>
                                                </div>
                                                <div>
                                                    <button type="submit" name="Discount" class="btn btn-secondary">حفظ</button>
                                                </div>
                                            </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card">
                        <h5 class="card-header">فواتير المشتريات</h5>
                        <div class="table-responsive text-nowrap">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>اسم المنتج</th>
                                        <th>سعر الشراء</th>
                                        <th>سعر البيع</th>
                                        <th>الكمية</th>
                                        <th>اجمالي السعر</th>
                                        <th>تعديل</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($productsInInvoice as $index => $productInInvoice): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($index + 1); ?></td>
                                            <td><?= htmlspecialchars($productInInvoice['ProductName']); ?></td>
                                            <td><?= htmlspecialchars($productInInvoice['BuyPrice']); ?> EG</td>
                                            <td><?= htmlspecialchars($productInInvoice['SalePrice']); ?> EG</td>
                                            <td><?= htmlspecialchars($productInInvoice['Quantity']); ?></td>
                                            <td><?= htmlspecialchars($productInInvoice['TotalPrice']); ?> EG</td>
                                            <td>
                                                <div class="dropdown">
                                                    <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                        <i class="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div class="dropdown-menu">
                                                        <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editModal-<?= $productInInvoice['id']; ?>">
                                                            <i class="bx bx-edit-alt me-1"></i> تعديل
                                                        </button>
                                                        <form method="post" action="add_new_Inventory_invoice.php?InventoryInvoiceID=<?= htmlspecialchars($inventoryInvoiceID); ?>">
                                                            <input type="hidden" name="id" value="<?= htmlspecialchars($productInInvoice['id']); ?>">
                                                            <button type="submit" name="delete_row" class="dropdown-item">
                                                                <i class="bx bx-trash me-1"></i> حذف
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                                <!-- Edit Modal -->
                                                <div class="modal fade" id="editModal-<?= $productInInvoice['id']; ?>" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <form class="modal-content" method="post" action="add_new_Inventory_invoice.php?InventoryInvoiceID=<?= htmlspecialchars($inventoryInvoiceID); ?>">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">تعديل المنتج</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <input type="hidden" name="id" value="<?= htmlspecialchars($productInInvoice['id']); ?>">
                                                                <div class="mb-3">
                                                                    <label for="productSelect-<?= $productInInvoice['id']; ?>" class="form-label">اسم المنتح</label>
                                                                    <select class="form-select" id="productSelect-<?= $productInInvoice['id']; ?>" name="ProductID" required>
                                                                        <option value="" selected>اختر اسم المنتج</option>
                                                                        <?php foreach ($products as $product): ?>
                                                                            <option value="<?= htmlspecialchars($product['productId']); ?>" <?= $productInInvoice['productID'] == $product['productId'] ? 'selected' : ''; ?>>
                                                                                <?= htmlspecialchars($product['ProductName']); ?>
                                                                            </option>
                                                                        <?php endforeach; ?>
                                                                    </select>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <label for="quantityInput-<?= $productInInvoice['id']; ?>" class="form-label">الكمية</label>
                                                                    <input type="number" class="form-control" id="quantityInput-<?= $productInInvoice['id']; ?>" name="quantity" value="<?= htmlspecialchars($productInInvoice['Quantity']); ?>" required>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" name="update_row" class="btn btn-primary">تحديث</button>
                                                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">الغاء</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                            </div>
                        </div>
                    </div>
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
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <script>
    document.getElementById('invoiceForm').addEventListener('submit', function(event) {
        const productSelect = document.getElementById('productSelect');
        const quantityInput = document.getElementById('quantityInput');

        if (!productSelect.value) {
            alert('من فضلك اختر اسم المنتج');
            event.preventDefault();
        }

        if (!quantityInput.value || isNaN(quantityInput.value) || quantityInput.value <= 0) {
            alert('من فضلك ادخل كمية صحيحة');
            event.preventDefault();
        }
    });
</script>
</body>
</html>
