
<?php

require_once '../../Controllers/DBController.php';


class CategoryController
{

    public $db;

    // CRUD operations for Category
    public function addCategory($categoryName)
    {
        $this->db=new DBController;
         if($this->db->openConnection())
         {
            $query="INSERT INTO productcategory (CategoryName,Status) VALUES ('$categoryName','Active')";
            
            return $this->db->insert($query);
         }
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
    }
    public function getAllCategories()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM ProductCategory Where Status = 'Active' ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    public function getAllArchiveCategories()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM ProductCategory Where Status = 'NotActive' ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }


    public function getCategory($categoryId)
    {
        $this->db=new DBController;
        if($this->db->openConnection())
        {
           $query="SELECT * FROM ProductCategory WHERE CategoryId = $categoryId AND Status = 'Active'";
           return $this->db->select($query);
        }
        else
        {
           echo "Error in Database Connection";
           return false; 
        }
       }

       

    public function isCategoryNameExist($categoryName, $categoryId = null) {
        if ($this->db->openConnection()) {
            $qry = "SELECT COUNT(*) as count FROM `productcategory` WHERE `CategoryName` = '$categoryName'";
            if ($categoryId) {
                $qry .= " AND `CategoryId` != $categoryId";
            }
            $result = $this->db->select($qry);
            if ($result && $result[0]['count'] > 0) {
                return true;
            }
        }
        return false;
    }

    public function updateCategory($categoryId, $categoryName)
    {
        $this->db=new DBController;
         if($this->db->openConnection())
         {
            $qry = "UPDATE `productcategory` SET `CategoryName` = '$categoryName' WHERE `productcategory`.`CategoryId` = $categoryId";
            return $this->db->update($qry);
         }
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }

    public function DeActiveCategory($categoryId)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $qry = "UPDATE `productcategory` SET `Status` = 'NotActive' WHERE `productcategory`.`CategoryId` = '$categoryId'";
            return $this->db->update($qry); // Use the appropriate method
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function ActiveCategory($categoryId)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $qry = "UPDATE `productcategory` SET `Status` = 'Active' WHERE `productcategory`.`CategoryId` = '$categoryId'";
            return $this->db->update($qry); // Use the appropriate method
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

}


?>