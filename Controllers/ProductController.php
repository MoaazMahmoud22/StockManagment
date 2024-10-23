<?php

require_once '../../Controllers/DBController.php';

class ProductController
{
    public $db;

    public function addProduct($categoryID, $name, $buyPrice, $salePrice)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            if ($this->isProductUnique($categoryID, $name)) {
                $qry = "INSERT INTO Product (CategoryID, Name, BuyPrice, SalePrice,Status) VALUES ($categoryID, '$name', $buyPrice, $salePrice,'Active')";
                if ($this->db->insert($qry)) {
                    // Fetch the last inserted ID
                    $lastInsertId = $this->db->getLastInsertId();
                    return $lastInsertId;
                } else {
                    echo "Error: Could not insert product.";
                    return false;
                }
            } else {
                echo "Error: A product with this category and name already exists.";
                return false;
            }
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }


    public function getAllProducts()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT p.productId, p.Name as ProductName, p.BuyPrice, p.SalePrice, p.CategoryID, c.CategoryName 
                      FROM Product p
                      JOIN productcategory c ON p.CategoryID = c.CategoryId Where p.Status = 'Active' ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    public function getAllProductsByCategory($categoryID)
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT p.productId, p.Name as ProductName, p.BuyPrice, p.SalePrice, p.CategoryID, c.CategoryName 
                      FROM Product p
                      JOIN productcategory c ON p.CategoryID = c.CategoryId Where p.Status = 'Active' AND c.CategoryId= $categoryID ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function getAllArchiveProducts()
    {
        $this->db = new DBController();
        if ($this->db->openConnection()) {
            $query = "SELECT p.productId, p.Name as ProductName, p.BuyPrice, p.SalePrice, p.CategoryID, c.CategoryName 
                      FROM Product p
                      JOIN productcategory c ON p.CategoryID = c.CategoryId Where p.Status = 'NotActive' ";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function getProduct($productId)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $query = "SELECT * FROM Product WHERE productId = $productId";
            return $this->db->select($query);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }

    public function isProductUnique($categoryID, $name, $productId = null)
{
    if ($this->db->openConnection()) {
        $qry = "SELECT COUNT(*) as count FROM Product WHERE CategoryID = $categoryID AND Name = '$name'";
        if ($productId) {
            $qry .= " AND productId != $productId";
        }
        $result = $this->db->select($qry);
        if ($result && $result[0]['count'] > 0) {
            return false; // Duplicate found
        }
        return true; // Unique
    }
    return false; // Database connection error
}


    public function isProductNameExist($ProductName, $ProductId = null)
    {
        if ($this->db->openConnection()) {
            $qry = "SELECT COUNT(*) as count FROM `Product` WHERE `Name` = '$ProductName'";
            if ($ProductId) {
                $qry .= " AND `ProductId` != $ProductId";
            }
            $result = $this->db->select($qry);
            if ($result && $result[0]['count'] > 0) {
                return true;
            }
        }
        return false;
    }

    public function updateProduct($productId, $categoryID, $name, $buyPrice, $salePrice)
{
    $this->db = new DBController;
    if ($this->db->openConnection()) {
        if ($this->isProductUnique($categoryID, $name, $productId)) {
            $qry = "UPDATE Product SET CategoryID = $categoryID, Name = '$name', BuyPrice = $buyPrice, SalePrice = $salePrice WHERE productId = $productId";
            return $this->db->update($qry);
        } else {
            echo "Error: A product with this category and name already exists.";
            return false;
        }
    } else {
        echo "Error in Database Connection";
        return false;
    }
}


    public function DeActiveProduct($productId)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $qry = "UPDATE Product SET `Status`  = 'NotActive' WHERE productId = $productId";
            return $this->db->update($qry);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
    public function ActiveProduct($productId)
    {
        $this->db = new DBController;
        if ($this->db->openConnection()) {
            $qry = "UPDATE Product SET `Status`  = 'Active' WHERE productId = $productId";
            return $this->db->update($qry);
        } else {
            echo "Error in Database Connection";
            return false;
        }
    }
}
?>
