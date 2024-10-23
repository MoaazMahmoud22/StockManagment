<?php

require_once '../../Controllers/DBController.php';

class ReportCotnroller
{
    public $db;

    public function GetCount_of_Customers()
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $query = "SELECT COUNT(*) AS num FROM `customer` WHERE role = 'customer'";
            // Execute the query
            $result = $this->db->select($query); // Assuming runQuery is a method in DBController that executes the query
            // Check if the query executed successfully
            if ($result && $result[0]['num'] > 0) {
                return $result[0]['num'];
            } else {
                // Handle query failure
                return false;
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function GetCount_of_Suppliers()
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $query = "SELECT COUNT(*) AS num FROM `customer` WHERE role = 'Supplier'";
            // Execute the query
            $result = $this->db->select($query); // Assuming runQuery is a method in DBController that executes the query
            // Check if the query executed successfully
            if ($result && $result[0]['num'] > 0) {
                return $result[0]['num'];
            } else {
                // Handle query failure
                return false;
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function GetCount_of_Invoices()
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $query = "SELECT COUNT(*) AS num FROM `invoices` WHERE Status IN ('بيع','اجل')";
            // Execute the query
            $result = $this->db->select($query); // Assuming runQuery is a method in DBController that executes the query
            // Check if the query executed successfully
            if ($result && $result[0]['num'] > 0) {
                return $result[0]['num'];
            } else {
                // Handle query failure
                return false;
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
public function SalesByCategories()
{
    $this->db = new DBController;
    if ($this->db->openConnection()) {
        $query = 'SELECT 
            pc.CategoryName,
            ROUND((SUM(i.Quantity * i.SalePrice) / total.TotalSales) * 100, 2) AS SalesPercentage
        FROM 
            invoices i 
        JOIN 
            product p ON i.ProductId = p.productId 
        JOIN 
            productcategory pc ON p.CategoryID = pc.CategoryId
        JOIN
            (SELECT SUM(i.Quantity * i.SalePrice) AS TotalSales FROM invoices i) AS total
        GROUP BY 
            pc.CategoryName, total.TotalSales
        ';
        // Execute the query
        $result = $this->db->select($query);

        // Check if the query executed successfully
        if ($result !== false) {
            // Format the data for the pie chart
            $labels = [];
            $series = [];
            
            foreach ($result as $row) {
                $labels[] = $row['CategoryName'];
                $series[] = $row['SalesPercentage'];
            }
            
            // Return formatted data
            return [
                'labels' => $labels,
                'series' => $series
            ];
        } else {
            // Handle query failure
            return [
                'labels' => [],
                'series' => []
            ];
        }
    } else {
        echo "Error in Database Connection";
        return [
            'labels' => [],
            'series' => []
        ];
    }
}

public function GetTotalDebtCustomer()
{
    $this->db = new DBController;
    if ($this->db->openConnection()) {
        $query = " SELECT ROUND((SUM(i.TotalPrice -c.Payment)), 2) AS TotalPrice
            FROM customer c 
            JOIN invoices i
            ON c.CustomerId = i.CustomerId
            WHERE i.Status='اجل';";
        // Execute the query
        $result = $this->db->select($query); // Assuming runQuery is a method in DBController that executes the query
        // Check if the query executed successfully
        if ($result && $result[0]['TotalPrice'] > 0) {
            return $result[0]['TotalPrice'];
        } else {
            // Handle query failure
            return false;
        }
    } else {
        echo "Error in Database Connection";
        return false;
    }
} 
public function GetTotalDebtToSuppliers()
{
    $this->db = new DBController;
    if ($this->db->openConnection()) {
        $query = "SELECT 
    SUM(ip.TotalPrice) - COALESCE(SUM(ii.Payment), 0) AS TotalPrice
FROM 
    invoice_product ip
JOIN 
    inventoryinvoice ii ON ip.InventoryInvoiceID = ii.InventoryInvoiceID
WHERE 
    ii.inventory_status = 'اجل';";
        // Execute the query
        $result = $this->db->select($query); // Assuming runQuery is a method in DBController that executes the query
        // Check if the query executed successfully
        if ($result && $result[0]['TotalPrice'] > 0) {
            return $result[0]['TotalPrice'];
        } else {
            // Handle query failure
            return false;
        }
    } else {
        echo "Error in Database Connection";
        return false;
    }
} 
    

}
?>
