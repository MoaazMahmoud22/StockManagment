<?php
require_once '../../Controllers/DBController.php';
require_once '../../Controllers/PricesController.php';

class InvoicesController {
    public $db;

    public function __construct() {
        $this->db = new DBController();
    }

    public function addInvoice($productID, $quantity, $status, $CustomerId , $date,$TotalPrice,$discount,$BuyPrice,$SalePrice) {
        // Get total quantity from invoice_product and invoices
        if ($this->db->openConnection()) {
            $query = "INSERT INTO invoices (productID, Quantity, Status, CustomerId,Date,TotalPrice,Discount,BuyPrice,SalePrice) VALUES ($productID, $quantity, '$status', $CustomerId, '$date',$TotalPrice,$discount,$BuyPrice,$SalePrice)";
            $result = $this->db->insert($query);
            $this->db->closeConnection();
            return $result ? "Invoice added successfully" : "Error adding invoice";
        } else {
            return "Error in Database Connection";
        }
    }

    public function updateInvoice($invoiceID, $productID, $quantity, $status,$CustomerId,$TotalPrice,$discount) {

        if ($this->db->openConnection()) {
            $query = "UPDATE invoices SET productID = $productID, Quantity = $quantity, Status = '$status', CustomerId = '$CustomerId' , TotalPrice =$TotalPrice ,Discount = $discount WHERE invoiceID = $invoiceID";
            $result = $this->db->update($query);
            $this->db->closeConnection();
            return $result ? "Invoice updated successfully" : "Error updating invoice";
        } else {
            return "Error in Database Connection";
        }
    }

    public function deleteInvoice($invoiceID) {
        if ($this->db->openConnection()) {
            $query = "DELETE FROM invoices WHERE invoiceID = $invoiceID";
            $result = $this->db->delete($query);
            $this->db->closeConnection();
            return $result ? "Invoice deleted successfully" : "Error deleting invoice";
        } else {
            return "Error in Database Connection";
        }
    }

    public function getAllInvoices($date = null) {
        if ($this->db->openConnection()) {
            // Set the default date to today if no date is provided
            if (!$date) {
                $date = date('Y-m-d');
            }
    
            $query = "SELECT I.`InvoiceID`, 
                             (I.SalePrice - I.BuyPrice) AS ProfitPerUnit,
                             i.Quantity,
                             ROUND((I.SalePrice - I.BuyPrice) * i.Quantity, 2) AS TotalProfit,
                             P.Name as ProductName,
                             I.`Quantity`, 
                             I.`TotalPrice`, 
                             I.`Discount`,
                             I.Status,
                             DATE_FORMAT(I.`Date`, '%Y-%m-%d %h:%i:%s %p') AS Date, 
                             C.CustomerName,
                             I.SalePrice
                      FROM `invoices` I 
                      JOIN product P ON I.ProductId=P.productId
                      JOIN customer c ON c.CustomerId=I.CustomerId
                      WHERE I.Date >= '$date'";
            
            $result = $this->db->select($query, [$date]);
            $this->db->closeConnection();
            return $result;
        } else {
            return false;
        }
    }
    
    public function getInvoiceById($InvoiceID) {
        if ($this->db->openConnection()) {
            $query = "SELECT I.`InvoiceID`,P.Name as ProductName, I.`Quantity`, I.`TotalPrice`, I.`Discount`,I.Status,I.Date , C.CustomerName,I.SalePrice,P.ProductId,I.CustomerId
                    FROM `invoices` I 
                    JOIN product P
                    ON I.ProductId=P.productId
                    JOIN customer c
                    ON c.CustomerId=I.CustomerId Where InvoiceID = $InvoiceID";
            $result = $this->db->select($query);
            $this->db->closeConnection();
            return $result;
        } else {
            return false;
        }
    }
}
?>
