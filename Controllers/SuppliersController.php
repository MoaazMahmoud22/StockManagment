<?php

require_once '../../Controllers/DBController.php';

class SuppliersController
{
    public $db;

 // Function to add a new Supplier
 public function addSupplier($name, $phone_number)
 {
     $this->db = new DBController();
     if ($this->db->openConnection()) {
         // Check if the Supplier name is unique
         if ($this->isSupplierNameExist($name)) {
             echo "Error: Supplier with this name already exists.";
             return false;
         }
         
         $qry = "INSERT INTO customer (CustomerName, phone_number , role , Status) VALUES ('$name', '$phone_number','Supplier','Active')";
         return $this->db->insert($qry);
     } else {
         echo "Error in Database Connection";
         return false;
     }
 }
    // Function to get all Supplier
    public function getAllSupplier()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM customer Where CustomerName !='المحل' AND role ='Supplier' AND Status = 'Active'";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    public function getAllDeactiveSupplier()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM customer Where CustomerName !='المحل' AND role ='Supplier' AND Status = 'NotActive' ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    // Function to get a specific customer by ID
    public function getSupplier($CustomerId)
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM customer WHERE CustomerId = $CustomerId";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    // Function to check if a Supplier name is unique
    public function isSupplierNameExist($CustomerName, $CustomerId = null)
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $qry = "SELECT COUNT(*) as count FROM customer WHERE CustomerName = '$CustomerName' AND role ='Supplier'";
            if ($CustomerId) {
                $qry .= " AND CustomerId != $CustomerId";
            }
            $result = $this->db->select($qry);
            if ($result && $result[0]['count'] > 0) {
                return true; // Name exists
            }
        }
        return false; // Name does not exist
    }
    
    public function Calculate_Supplier_Debt($CustomerID) {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT SUM(TotalPrice) as TotalPrice FROM invoices WHERE `CustomerId` = $CustomerID AND `Status` = 'اجل' ;";
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
    public function IncreasePaymentSupplier($Payment, $CustomerID,$InventoryInvoiceID)
    {
        // Ensure database connection is open
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            
            // Calculate the current debt/payment of the Supplier
            $result = $this->db->select("SELECT * from inventoryinvoice Where `customerID` = $CustomerID AND InventoryInvoiceID = $InventoryInvoiceID"); //170 Pay
            $CustomerDebt = $this->db->select("SELECT SUM(TotalPrice) as TotalPrice FROM invoice_product WHERE InventoryInvoiceID = $InventoryInvoiceID"); //180
            if ($result) {
                // Retrieve the old payment value
                $OldPayment = $result[0]['Payment'];
                
                // Calculate the new payment value
                $NewPayment = $OldPayment + $Payment;
                if($NewPayment <= $CustomerDebt){
                // Prepare and execute the update query
                $query = "UPDATE `inventoryinvoice` SET Payment = $NewPayment WHERE customerID = $CustomerID AND InventoryInvoiceID = $InventoryInvoiceID";
                $updateResult = $this->db->update($query);
                
                // Close the database connection
                $this->db->closeConnection();
                
                return $updateResult;
                } 
            } else {
                echo "Error retrieving Supplier debt value.";
                return false;
            }
            
        } else {
            echo "Error connecting to the database.";
            return false;
        }
    }
    
    public function DecreasePaymentSupplier($Payment, $CustomerID,$InventoryInvoiceID)
{
    $this->db = new DBController;
    if ($this->db->openConnection()) {
        $result = $this->db->select("SELECT * from inventoryinvoice Where `customerID` = $CustomerID AND InventoryInvoiceID = $InventoryInvoiceID");
        $CustomerDebt = $this->db->select("SELECT SUM(TotalPrice) as TotalPrice FROM invoice_product WHERE InventoryInvoiceID = $InventoryInvoiceID");
        if ($result) {
            $OldPayment = $result[0]['Payment'];  // Assuming select returns an array of rows, and we're getting the 'Cash' column from the first row.

            if ($Payment <= $OldPayment) {
                $NewPayment = $OldPayment - $Payment;
                $query = "UPDATE `inventoryinvoice` SET Payment = $NewPayment WHERE customerID = $CustomerID AND InventoryInvoiceID = $InventoryInvoiceID";
                
                return $this->db->update($query);  // Use update function instead of select
            } else {
                return false;
            }
        } else {
            echo "Error retrieving old cash value.";
            return false;
        }
    } else {
        echo "Error in Database Connection";
        return false;
    }
}

    // Function to update a Supplier
    public function updateSupplier($CustomerId, $name, $phone_number)
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            // Check if the new customer name is unique
            if ($this->isSupplierNameExist($name, $CustomerId)) {
                echo "Error: A Customer with this name already exists.";
                return false;
            }

            $qry = "UPDATE customer SET CustomerName = '$name' ,phone_number='$phone_number' WHERE CustomerId = $CustomerId ";
            return $this->db->update($qry);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
        // Function to delete a customer
        public function DeActiveSupplier($CustomerId)
        {
            $this->db = new DBController();
            if ($this->db->openConnection()) {
                $qry = "UPDATE customer SET `Status` = 'NotActive' WHERE CustomerId = $CustomerId ";
                return $this->db->update($qry);
            } else {
                echo "Error in Database Connection";
                return false;
            }
        }
        public function ActiveSupplier($CustomerId)
        {
            $this->db = new DBController();
            if ($this->db->openConnection()) {
                $qry = "UPDATE customer SET `Status` = 'Active' WHERE CustomerId = $CustomerId ";
                return $this->db->update($qry);
            } else {
                echo "Error in Database Connection";
                return false;
            }
        }
}
?>
