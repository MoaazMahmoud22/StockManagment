<?php

require_once '../../Controllers/DBController.php';

class CustomerController
{
    public $db;

 // Function to add a new customer
 public function addCustomer($name, $phone_number)
 {
     $this->db = new DBController();
     if ($this->db->openConnection()) {
         // Check if the customer name is unique
         if ($this->isCustomerNameExist($name)) {
             echo "Error: Customer with this name already exists.";
             return false;
         }
         
         $qry = "INSERT INTO customer (CustomerName, phone_number,`role` ,Status) VALUES ('$name', '$phone_number','customer' , 'Active')";
         return $this->db->insert($qry);
     } else {
         echo "Error in Database Connection";
         return false;
     }
 }
    // Function to get all customers
    public function getAllCustomers()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM customer Where CustomerName !='المحل' AND role ='Customer' AND Status = 'Active' ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function getAllDeactiveCustomers()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM customer Where CustomerName !='المحل' AND role ='Customer' AND Status = 'NotActive' ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    // Function to get a specific customer by ID
    public function getCustomer($CustomerId)
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

    // Function to check if a customer name is unique
    public function isCustomerNameExist($CustomerName, $CustomerId = null)
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $qry = "SELECT COUNT(*) as count FROM customer WHERE CustomerName = '$CustomerName' AND `role`='customer'";
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
    
    public function Calculate_Customer_Debt($CustomerID) {
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
    public function IncreasePaymentCustomer($Payment, $CustomerID)
    {
        // Ensure database connection is open
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            
            // Calculate the current debt/payment of the customer
            $result = $this->db->select("SELECT * from customer Where `CustomerId` = $CustomerID");
            $CustomerDebt = $this->db->select("SELECT SUM(TotalPrice) as TotalPrice FROM invoices WHERE `CustomerId` = $CustomerID AND `Status` = 'اجل' ;");
            if ($result) {
                // Retrieve the old payment value
                $OldPayment = $result[0]['Payment'];
                
                // Calculate the new payment value
                $NewPayment = $OldPayment + $Payment;
                if($CustomerDebt>=$NewPayment){
                // Prepare and execute the update query
                $query = "UPDATE `customer` SET Payment = $NewPayment WHERE CustomerId = $CustomerID";
                $updateResult = $this->db->update($query);
                
                // Close the database connection
                $this->db->closeConnection();
                
                return $updateResult;
                } 
            } else {
                echo "Error retrieving customer debt value.";
                return false;
            }
            
        } else {
            echo "Error connecting to the database.";
            return false;
        }
    }
    
    public function DecreasePaymentCustomer($Payment, $CustomerID)
{
    $this->db = new DBController;
    if ($this->db->openConnection()) {
        $result = $this->db->select("SELECT * from customer Where `CustomerId` = $CustomerID");
        if ($result) {
            $OldPayment = $result[0]['Payment'];  // Assuming select returns an array of rows, and we're getting the 'Cash' column from the first row.

            if ($Payment <= $OldPayment) {
                $NewPayment = $OldPayment - $Payment;
                $query = "UPDATE `customer` SET Payment = $NewPayment WHERE CustomerId = $CustomerID";
                
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

    // Function to update a customer
    public function updateCustomer($CustomerId, $name, $phone_number)
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            // Check if the new customer name is unique
            if ($this->isCustomerNameExist($name, $CustomerId)) {
                echo "Error: A Customer with this name already exists.";
                return false;
            }

            $qry = "UPDATE customer SET CustomerName = '$name' ,phone_number='$phone_number' WHERE CustomerId = $CustomerId";
            return $this->db->update($qry);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    // Function to delete a customer
    public function DeActiveCustomer($CustomerId)
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
    public function ActiveCustomer($CustomerId)
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
