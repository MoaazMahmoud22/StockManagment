<?php

require_once '../../Controllers/DBController.php';

class VaultController
{
    public $db;

    public function GetVault()
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM `vault` Where CashID = 1";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function DecreaseCash($Cash,$details,$date_details)
{
    $this->db = new DBController;
    if ($this->db->openConnection()) {
        $result = $this->db->select("SELECT Cash FROM `vault` WHERE CashID = 1");
        if ($result) {
            $OldCash = $result[0]['Cash'];  // Assuming select returns an array of rows, and we're getting the 'Cash' column from the first row.
            $NewCash = $OldCash - $Cash;
            $query = "UPDATE `vault` SET Cash = $NewCash WHERE CashID = 1";
            $this->db->insert("INSERT INTO vault_info (CashID, details, date_details, `Status`,Amount) VALUES (1, '$details', '$date_details', 'decrease',$Cash)");
            return $this->db->update($query);  // Use update function instead of select
        } else {
            echo "Error retrieving old cash value.";
            return false;
        }
    } else {
        echo "Error in Database Connection";
        return false;
    }
}

    public function IncreaseCash($Cash,$details,$date_details)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $result = $this->db->select("SELECT Cash FROM `vault` WHERE CashID = 1");
            if ($result) {
                $OldCash = $result[0]['Cash'];
                $NewCash = $OldCash + $Cash;
                    $query = "UPDATE `vault` SET Cash = $NewCash WHERE CashID = 1";
                    $this->db->insert("INSERT INTO vault_info (CashID, details, date_details,`Status`,Amount) VALUES (1, '$details','$date_details','increase',$Cash)");
                    return $this->db->update($query); 
                } 

            } else {
                echo "Error retrieving old cash value.";
                return false;
            }
    }

    public function GetAllVaultInfo()
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $query = "SELECT CashID, vault_infoID, `details`, DATE_FORMAT(date_details, '%Y-%m-%d %h:%i:%s %p') AS Date, `Status`, `Amount`
                      FROM `vault_info`
                      WHERE CashID = 1";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

}
?>
