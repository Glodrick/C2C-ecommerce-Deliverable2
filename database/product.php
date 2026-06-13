<?php

class Product
{
    public $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return;
        $this->db = $db;
    }

    /**
     * Fetch all products from a given table.
     */
    public function getData($table = 'product')
    {
        // Whitelist table names to prevent SQL injection
        $allowed = ['product', 'cart'];
        $table   = in_array($table, $allowed) ? $table : 'product';

        if ($table === 'product') {
            $result = $this->db->con->query(
                "SELECT p.*, u.first_name as seller_first_name, u.last_name as seller_last_name 
                 FROM `product` p 
                 LEFT JOIN `user` u ON p.seller_id = u.user_id 
                 WHERE p.item_status = 'active'"
            );
        } else {
            $result = $this->db->con->query("SELECT * FROM `{$table}`");
        }
        
        $resultArray = [];
        while ($item = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $resultArray[] = $item;
        }
        return $resultArray;
    }

    /**
     * Fetch a single product by its ID.
     */
    public function getProduct($item_id = null)
    {
        if (!isset($item_id)) return [];

        $stmt = $this->db->con->prepare(
            "SELECT p.*, u.first_name as seller_first_name, u.last_name as seller_last_name 
             FROM `product` p 
             LEFT JOIN `user` u ON p.seller_id = u.user_id 
             WHERE p.item_id = ?"
        );
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
        $result      = $stmt->get_result();
        $resultArray = [];
        while ($item = $result->fetch_assoc()) {
            $resultArray[] = $item;
        }
        $stmt->close();
        return $resultArray;
    }

    /**
     * Fetch all products in a given category.
     */
    public function getProductsByCategory($category)
    {
        $stmt = $this->db->con->prepare(
            "SELECT p.*, u.first_name as seller_first_name, u.last_name as seller_last_name 
             FROM `product` p 
             LEFT JOIN `user` u ON p.seller_id = u.user_id 
             WHERE p.item_category = ? AND p.item_status = 'active' 
             ORDER BY p.item_register DESC"
        );
        $stmt->bind_param('s', $category);
        $stmt->execute();
        $result      = $stmt->get_result();
        $resultArray = [];
        while ($item = $result->fetch_assoc()) {
            $resultArray[] = $item;
        }
        $stmt->close();
        return $resultArray;
    }

    /**
     * Fetch all brand-new products.
     */
    public function getBrandNewProducts()
    {
        $result      = $this->db->con->query(
            "SELECT p.*, u.first_name as seller_first_name, u.last_name as seller_last_name 
             FROM `product` p 
             LEFT JOIN `user` u ON p.seller_id = u.user_id 
             WHERE p.item_is_new = 1 AND p.item_status = 'active' 
             ORDER BY p.item_register DESC"
        );
        $resultArray = [];
        while ($item = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $resultArray[] = $item;
        }
        return $resultArray;
    }

    /**
     * Fetch all sale products.
     */
    public function getSaleProducts()
    {
        $result      = $this->db->con->query(
            "SELECT p.*, u.first_name as seller_first_name, u.last_name as seller_last_name 
             FROM `product` p 
             LEFT JOIN `user` u ON p.seller_id = u.user_id 
             WHERE p.item_is_sale = 1 AND p.item_status = 'active' 
             ORDER BY p.item_register DESC"
        );
        $resultArray = [];
        while ($item = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
            $resultArray[] = $item;
        }
        return $resultArray;
    }

    /**
     * Search products by name or brand.
     */
    public function searchProducts($term)
    {
        $like = '%' . $term . '%';
        $stmt = $this->db->con->prepare(
            "SELECT p.*, u.first_name as seller_first_name, u.last_name as seller_last_name 
             FROM `product` p 
             LEFT JOIN `user` u ON p.seller_id = u.user_id 
             WHERE (p.item_name LIKE ? OR p.item_brand LIKE ?)
               AND p.item_status = 'active'
             ORDER BY p.item_name ASC"
        );
        $stmt->bind_param('ss', $like, $like);
        $stmt->execute();
        $result      = $stmt->get_result();
        $resultArray = [];
        while ($item = $result->fetch_assoc()) {
            $resultArray[] = $item;
        }
        $stmt->close();
        return $resultArray;
    }

    /**
     * Fetch all listings by a specific seller
     */
    public function getListingsBySeller($seller_id)
    {
        $seller_id = (int)$seller_id;
        $stmt = $this->db->con->prepare(
            "SELECT * FROM `product` WHERE `seller_id` = ? ORDER BY `item_register` DESC"
        );
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $resultArray = [];
        while ($item = $result->fetch_assoc()) {
            $resultArray[] = $item;
        }
        $stmt->close();
        return $resultArray;
    }
}