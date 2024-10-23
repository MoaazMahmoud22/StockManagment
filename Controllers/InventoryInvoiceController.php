<?php

class InventoryInvoiceController {
    public $db;


    public function getAllInventoryInvoice($status = null)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            // Base query
            $query = "SELECT II.InventoryInvoiceID, II.inventory_status, II.Discount, II.Date, II.Payment, C.CustomerName, C.customerID 
                      FROM inventoryinvoice II 
                      JOIN customer C ON C.CustomerId = II.customerID";
            
            // Prepare statement
            if ($status) {
                $query .= " WHERE II.inventory_status = ?";
            }
    
            $query .= " ORDER BY II.InventoryInvoiceID DESC";
    
            // Prepare and bind parameters
            if ($stmt = $this->db->connection->prepare($query)) {
                if ($status) {
                    $stmt->bind_param('s', $status); // 's' indicates the type string
                }
                $stmt->execute();
                $result = $stmt->get_result();
                $data = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                return $data;
            } else {
                echo "Error in SQL Statement Preparation";
                return false;
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    


    // Add new inventory invoice
    public function addInventoryInvoice($date,$inventory_status,$customerID) {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            // Enclose $date and $totalPrice in quotes
            $query = "INSERT INTO inventoryinvoice (Date,inventory_status,customerID) VALUES ('$date','$inventory_status',$customerID)";
            return $this->db->insert($query);
        } else {
            echo "Error in Database Connection";
            return false; 
        }
    }

    // Update existing inventory invoice
    public function DiscountInventoryInvoice($inventoryInvoiceID,$Discount) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "UPDATE inventoryinvoice SET Discount = $Discount WHERE `InventoryInvoiceID` = $inventoryInvoiceID ";
            return $this->db->update($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }
    public function updateStatusInventoryInvoice($inventoryInvoiceID) {
      $this->db=new DBController;
      if($this->db->openConnection())
       {
          $query = "UPDATE inventoryinvoice SET inventory_status = 'بيع' WHERE InventoryInvoiceID = $inventoryInvoiceID";
          return $this->db->update($query);
       }  
       else
       {
          echo "Error in Database Connection";
          return false; 
       }
      
  }
    // Delete inventory invoice
    public function deleteInventoryInvoice($inventoryInvoiceID) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "DELETE FROM inventoryinvoice WHERE InventoryInvoiceID = $inventoryInvoiceID";
            return $this->db->delete($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }

    public function isInventoryInvoiceExist($inventoryInvoiceID)
    {
        if ($this->db->openConnection()) {
            $qry = "SELECT COUNT(*) as count FROM `invoice_product` WHERE `InventoryInvoiceID` ='$inventoryInvoiceID'";
            $result = $this->db->select($qry);
            if ($result && $result[0]['count'] > 0) {
                return true;
            }
        }
        return false;
    }

    // Add product to inventory invoice
    public function addProductToInvoice($inventoryInvoiceID, $productID, $quantity,$totalPriceForProduct,$BuyPrice,$SalePrice) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "INSERT INTO invoice_product (InventoryInvoiceID, productID, Quantity,TotalPrice,BuyPrice,SalePrice) VALUES ($inventoryInvoiceID, $productID, $quantity,$totalPriceForProduct,$BuyPrice,$SalePrice)";
            return $this->db->insert($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
        
    }

    // Update product in inventory invoice
    public function updateProductInInvoice($id, $productID, $quantity,$TotalPrice) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "UPDATE invoice_product SET  productID = $productID, Quantity = $quantity ,TotalPrice= $TotalPrice WHERE id = $id";
            return $this->db->update($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
    }

    // Delete product from inventory invoice
    public function deleteProductFromInvoice($id) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "DELETE FROM invoice_product WHERE id = $id";
            return $this->db->delete($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }

    // Get inventory invoice by ID
    public function getInventoryInvoiceByID($inventoryInvoiceID) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "SELECT * FROM inventoryinvoice WHERE InventoryInvoiceID = $inventoryInvoiceID";
            return $this->db->select($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }

    public function getinvoice_productByID($invoice_productID) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "SELECT * FROM invoice_product WHERE id = $invoice_productID";
            return $this->db->select($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }

    // Get products by inventory invoice ID
    public function getProductsByInvoiceID($inventoryInvoiceID) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "SELECT  IP.Quantity,IP.TotalPrice ,P.Name as ProductName , IP.BuyPrice,IP.SalePrice, P.productID , IP.id
                FROM
                invoice_product IP
                JOIN inventoryinvoice II
                ON II.InventoryInvoiceID = IP.InventoryInvoiceID
                JOIN product P
                ON IP.productID = P.productId
                WHERE II.InventoryInvoiceID =$inventoryInvoiceID";
            return $this->db->select($query);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }
}
?>
