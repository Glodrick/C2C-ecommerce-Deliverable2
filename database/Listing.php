<?php

class Listing
{
    public $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return;
        $this->db = $db;
    }

    /**
     * Add a pending listing after a successful KYC verify call
     */
    public function addPendingListing($seller_id, $item_name, $item_price, $item_category, $item_quantity, $item_description, $item_image, $kyc_status, $kyc_match_score, $raw_kyc_data)
    {
        $seller_id = (int)$seller_id;
        $item_price = (float)$item_price;
        $item_quantity = (int)$item_quantity;
        $now = date('Y-m-d H:i:s');
        
        $raw_kyc_json = json_encode($raw_kyc_data);

        $stmt = $this->db->con->prepare(
            "INSERT INTO `pending_listing` (`seller_id`, `item_name`, `item_price`, `item_category`, `item_quantity`, `item_description`, `item_image`, `kyc_status`, `kyc_match_score`, `raw_kyc_data`, `created_at`) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        $stmt->bind_param('isdsissssss', $seller_id, $item_name, $item_price, $item_category, $item_quantity, $item_description, $item_image, $kyc_status, $kyc_match_score, $raw_kyc_json, $now);
        
        $result = $stmt->execute();
        $stmt->close();
        
        return $result;
    }

    /**
     * Get pending listings for a specific seller
     */
    public function getPendingListings($seller_id)
    {
        $seller_id = (int)$seller_id;
        $stmt = $this->db->con->prepare(
            "SELECT * FROM `pending_listing` WHERE `seller_id` = ? ORDER BY `created_at` DESC"
        );
        $stmt->bind_param('i', $seller_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $resultArray = [];
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = $row;
        }
        $stmt->close();
        return $resultArray;
    }

    /**
     * Get all pending listings for admin queue
     */
    public function getAllPendingListings()
    {
        $stmt = $this->db->con->prepare(
            "SELECT pl.*, u.first_name, u.last_name, u.email 
             FROM `pending_listing` pl
             JOIN `user` u ON pl.seller_id = u.user_id
             WHERE pl.kyc_status NOT IN ('approved', 'rejected')
             ORDER BY pl.created_at ASC"
        );
        $stmt->execute();
        $result = $stmt->get_result();
        $resultArray = [];
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = $row;
        }
        $stmt->close();
        return $resultArray;
    }

    /**
     * Approve a listing
     */
    public function approveListing($listing_id)
    {
        $listing_id = (int)$listing_id;
        
        // Get listing
        $stmt = $this->db->con->prepare("SELECT * FROM `pending_listing` WHERE `listing_id` = ? AND `kyc_status` != 'approved'");
        $stmt->bind_param('i', $listing_id);
        $stmt->execute();
        $listing = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$listing) return false;

        $this->db->con->begin_transaction();
        try {
            // Insert into product table
            $now = date('Y-m-d H:i:s');
            $stmt = $this->db->con->prepare(
                "INSERT INTO `product` (`item_brand`, `item_name`, `item_price`, `item_image`, `item_details`, `item_category`, `item_in_stock`, `item_quantity`, `item_is_new`, `item_is_sale`, `item_register`, `seller_id`, `item_status`, `item_description`) 
                 VALUES ('User Listing', ?, ?, ?, '', ?, 1, ?, 0, 0, ?, ?, 'active', ?)"
            );
            $stmt->bind_param('sdssisis', $listing['item_name'], $listing['item_price'], $listing['item_image'], $listing['item_category'], $listing['item_quantity'], $now, $listing['seller_id'], $listing['item_description']);
            $stmt->execute();
            $stmt->close();

            // Update pending status
            $stmt = $this->db->con->prepare("UPDATE `pending_listing` SET `kyc_status` = 'approved' WHERE `listing_id` = ?");
            $stmt->bind_param('i', $listing_id);
            $stmt->execute();
            $stmt->close();

            $this->db->con->commit();
            return true;
        } catch (Exception $e) {
            $this->db->con->rollback();
            return false;
        }
    }

    /**
     * Reject a listing
     */
    public function rejectListing($listing_id)
    {
        $listing_id = (int)$listing_id;
        $stmt = $this->db->con->prepare("UPDATE `pending_listing` SET `kyc_status` = 'rejected' WHERE `listing_id` = ?");
        $stmt->bind_param('i', $listing_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }
}
