<?php
require_once '../../Controllers/DBController.php';

class PricesController{

    public $db;

    public function TotalPrice_for_each_product($InventoryInvoiceID) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoice_product WHERE InventoryInvoiceID = $InventoryInvoiceID";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function TotalPriceForEachInventoryInvoice_AfterDiscount($InventoryInvoiceID) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(IP.TotalPrice)-II.Discount as TotalPrice FROM invoice_product IP
            JOIN inventoryinvoice II ON II.InventoryInvoiceID=IP.InventoryInvoiceID 
            WHERE II.InventoryInvoiceID = $InventoryInvoiceID";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function TotalPrice_for_inventoryinvoice() {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM
                invoice_product IP JOIN inventoryinvoice II
                on II.InventoryInvoiceID = IP.InventoryInvoiceID
                WHERE II.inventory_status='بيع'";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    public function calculateFinalTotalInventoryInvoice() {
        $this->db = new DBController();
        
        if ($this->db->openConnection()) {
            // Query to get the total price
            $totalPriceQuery = "SELECT SUM(TotalPrice) as TotalPrice FROM invoice_product IP JOIN inventoryinvoice II on II.InventoryInvoiceID = IP.InventoryInvoiceID WHERE II.inventory_status = 'بيع'";
            $totalPriceResult = $this->db->select($totalPriceQuery);
            
            // Query to get the total discount
            $totalDiscountQuery = "SELECT SUM(Discount) as TotalDiscount FROM inventoryinvoice II WHERE II.inventory_status = 'بيع'";
            $totalDiscountResult = $this->db->select($totalDiscountQuery);
            
            // Ensure results are valid and calculate the final total
            if ($totalPriceResult && count($totalPriceResult) > 0 && $totalDiscountResult && count($totalDiscountResult) > 0) {
                $totalPrice = $totalPriceResult[0]['TotalPrice'];
                $totalDiscount = $totalDiscountResult[0]['TotalDiscount'];
    
                $TotalCash = $totalPrice - $totalDiscount;
                return $TotalCash;
            } else {
                return 0; // Handle cases where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    
    public function TotalQuantity_for_invoice_product($productID) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(Quantity) as TotalQuantity FROM invoice_product WHERE productID = $productID";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalQuantity'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function TotalQuantity_for_invoices($productID) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(Quantity) as TotalQuantity FROM invoices WHERE productID = $productID  AND Status IN ('اجل','بيع')";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalQuantity'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

        public function TotalPriceForinvoices() {
            $this->db = new DBController();
            if ($this->db->openConnection()) {
                $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoices WHERE Status = 'بيع';";
                $result = $this->db->select($query);
                if ($result && count($result) > 0) {
                    return $result[0]['TotalPrice'];
                } else {
                    return 0; // or handle the case where no rows are returned
                }
            } else {
                echo "Error in Database Connection";
                return false;
            }
        }

    public function TotalPriceForinvoicesEachDay($date = null) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            if (!$date) {
                $date = date('Y-m-d');
            }
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoices WHERE Status IN('بيع','اجل') AND Date >= '$date'";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    public function TotalPriceForMonth($year, $month) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $startDate = "$year-$month-01";
            $endDate = date("Y-m-t", strtotime($startDate));
    
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoices WHERE Status IN('بيع','اجل') AND Date >= '$startDate' AND Date <= '$endDate'";
            $result = $this->db->select($query);
            if ($result) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // Handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    
    public function Calculate_the_Profit_for_Each_Invoice() {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoices WHERE Status = 'بيع';";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function Sum_All_Profits_Across_All_Invoices() {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT ROUND(SUM(((i.SalePrice - i.BuyPrice) * i.Quantity)-i.Discount),2) AS TotalProfit FROM invoices i JOIN product p ON i.ProductId = p.productId WHERE i.Status IN ('بيع', 'اجل');";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalProfit'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }


    public function TotalPayPriceForAllCustomers() {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(Payment) as TotalPrice FROM customer WHERE role = 'customer';";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    public function TotalPayPriceForAllSuppliers() {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(II.Payment) as TotalPrice FROM invoice_product IP JOIN inventoryinvoice II on II.InventoryInvoiceID = IP.InventoryInvoiceID WHERE II.inventory_status='اجل'";
            $result = $this->db->select($query);
            if ($result && count($result) > 0) {
                return $result[0]['TotalPrice'];
            } else {
                return 0; // or handle the case where no rows are returned
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    //Filter For Profits
    public function getTotalPriceForInvoicesByDateRange($startDate, $endDate) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoices WHERE Status = 'بيع' AND Date >= '$startDate' AND Date <= '$endDate'";
            return $this->executeSumQuery($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
        
    }
    
    public function getTotalPayPriceForAllCustomersByDateRange($startDate, $endDate) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(c.Payment) as TotalPrice FROM customer c
            JOIN invoices i ON c.CustomerId = i.CustomerId
             WHERE c.role = 'customer' AND i.Date >= '$startDate' AND i.Date <= '$endDate'";
            return $this->executeSumQuery($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
       
    }
    
    public function getCalculateFinalTotalInventoryInvoiceByDateRange($startDate, $endDate) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoice_product IP 
            JOIN inventoryinvoice II on II.InventoryInvoiceID = IP.InventoryInvoiceID 
            WHERE II.inventory_status = 'بيع' AND II.Date >= '$startDate' AND II.Date <= '$endDate'";
            $totalPrice = $this->executeSumQuery($query);

            $discountQuery = "SELECT SUM(Discount) as TotalDiscount FROM inventoryinvoice II 
                    WHERE II.inventory_status = 'بيع' AND II.Date >= '$startDate' AND II.Date <= '$endDate'";
            $totalDiscount = $this->executeSumQuery($discountQuery);

            return $totalPrice - $totalDiscount;
        } else {
            echo "Error in Database Connection";
            return false;
        }
       

    }
    
    public function getTotalPayPriceForAllSuppliersByDateRange($startDate, $endDate) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(II.Payment) as TotalPrice FROM invoice_product IP 
            JOIN inventoryinvoice II on II.InventoryInvoiceID = IP.InventoryInvoiceID 
            WHERE II.inventory_status = 'اجل' AND II.Date >= '$startDate' AND II.Date <= '$endDate'";
            return $this->executeSumQuery($query);
            }
         else {
            echo "Error in Database Connection";
            return false;
        }
    }

    
    public function executeSumQuery($query) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $result = $this->db->select($query);
            return ($result && count($result) > 0 && isset($result[0]['TotalPrice'])) ? $result[0]['TotalPrice'] : 0;
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    
    

    public function calculateProfitForSpecificMonthAndYear($month, $year) {
        $this->db = new DBController();
    
        if ($this->db->openConnection()) {
            $startDate = "$year-$month-01";
            $endDate = date("Y-m-t", strtotime($startDate)); // Get last day of the month
    
            $totalPriceForInvoices = $this->getTotalPriceForInvoicesByDateRange($startDate, $endDate);
            $totalPayPriceForAllCustomers = $this->getTotalPayPriceForAllCustomersByDateRange($startDate, $endDate);
            $calculateFinalTotalInventoryInvoice = $this->getCalculateFinalTotalInventoryInvoiceByDateRange($startDate, $endDate);
            $totalPayPriceForAllSuppliers = $this->getTotalPayPriceForAllSuppliersByDateRange($startDate, $endDate);
    
            $profitForMonth = ($totalPriceForInvoices + $totalPayPriceForAllCustomers) - ($calculateFinalTotalInventoryInvoice + $totalPayPriceForAllSuppliers);
    
            return $profitForMonth;
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }


    public function getMinMaxDates() {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT MIN(Date) as MinDate, MAX(Date) as MaxDate FROM invoices WHERE Status = 'بيع'";
            $result = $this->db->select($query);
    
            if ($result && count($result) > 0) {
                return [
                    'min' => $result[0]['MinDate'],
                    'max' => $result[0]['MaxDate']
                ];
            } else {
                return [
                    'min' => null,
                    'max' => null
                ]; // Handle case where no data is found
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    
    


}




?>