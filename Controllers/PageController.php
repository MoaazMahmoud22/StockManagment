<?php

class PageController {
    public $db;
    private $records_per_page = 5;


    // Get total record count
    public function getTotalRecords($table) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $query = "SELECT COUNT(*) FROM " . $table;
            $result = mysqli_query($this->conn, $query);
            $row = mysqli_fetch_array($result);
            return $row[0];
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }
        
    }

    // Get records for the current page
    public function getRecords($table, $page_number) {
        $this->db=new DBController;
        if($this->db->openConnection())
         {
            $start_from = ($page_number - 1) * $this->records_per_page;
            $query = "SELECT * FROM " . $table . " LIMIT " . $start_from . ", " . $this->records_per_page;
            $result = mysqli_query($this->conn, $query);
            return mysqli_fetch_all($result, MYSQLI_ASSOC);
         }  
         else
         {
            echo "Error in Database Connection";
            return false; 
         }

    }

    // Generate pagination controls
    public function getPaginationControls($table, $current_page) {
        $total_records = $this->getTotalRecords($table);
        $total_pages = ceil($total_records / $this->records_per_page);
        $controls = '<nav aria-label="Page navigation"><ul class="pagination">';

        if ($current_page > 1) {
            $controls .= '<li class="page-item"><a class="page-link" href="?page=' . ($current_page - 1) . '">Previous</a></li>';
        }

        for ($i = 1; $i <= $total_pages; $i++) {
            $controls .= '<li class="page-item' . ($i == $current_page ? ' active' : '') . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
        }

        if ($current_page < $total_pages) {
            $controls .= '<li class="page-item"><a class="page-link" href="?page=' . ($current_page + 1) . '">Next</a></li>';
        }

        $controls .= '</ul></nav>';
        return $controls;
    }
}
?>