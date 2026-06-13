<?php

class User
{
    public $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return;
        $this->db = $db;
    }

    /**
     * Get all users (for admin dashboard).
     */
    public function getAllUsers()
    {
        $result      = $this->db->con->query(
            "SELECT `user_id`, `first_name`, `last_name`, `email`, `username`,
                    `role`, `status`, `register_date`, `last_active`
             FROM `user`
             ORDER BY `register_date` DESC"
        );
        $resultArray = [];
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = $row;
        }
        return $resultArray;
    }

    /**
     * Get active users (active within the last X minutes).
     */
    public function getActiveUsers($minutes = 15)
    {
        $minutes = (int)$minutes;
        $stmt    = $this->db->con->prepare(
            "SELECT `user_id`, `first_name`, `last_name`, `email`, `username`,
                    `role`, `status`, `register_date`, `last_active`
             FROM `user`
             WHERE `last_active` >= DATE_SUB(NOW(), INTERVAL ? MINUTE)
             ORDER BY `last_active` DESC"
        );
        $stmt->bind_param('i', $minutes);
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
     * Get a single user by ID.
     */
    public function getUserById($user_id)
    {
        $user_id = (int)$user_id;
        $stmt    = $this->db->con->prepare(
            "SELECT `user_id`, `first_name`, `last_name`, `email`, `username`,
                    `role`, `status`, `register_date`, `last_active`
             FROM `user` WHERE `user_id` = ?"
        );
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        $stmt->close();
        return $row;
    }

    /**
     * Register a new user.
     * Uses PHP's password_hash() with PASSWORD_DEFAULT (bcrypt).
     */
    public function register($first_name, $last_name, $email, $username, $password, $role = 'buyer')
    {
        // Hash the password securely using bcrypt
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $now             = date('Y-m-d H:i:s');

        $stmt = $this->db->con->prepare(
            "INSERT INTO `user`
             (`first_name`, `last_name`, `email`, `username`, `password_hash`, `role`, `status`, `register_date`, `last_active`)
             VALUES (?, ?, ?, ?, ?, ?, 'active', ?, ?)"
        );
        $stmt->bind_param(
            'ssssssss',
            $first_name, $last_name, $email, $username,
            $hashed_password, $role, $now, $now
        );
        $result = $stmt->execute();
        $new_id = $this->db->con->insert_id;
        $stmt->close();

        return $result ? $new_id : false;
    }

    /**
     * Verify login credentials.
     * Uses password_verify() — compatible with password_hash(PASSWORD_DEFAULT).
     */
    public function login($email, $password)
    {
        $stmt = $this->db->con->prepare(
            "SELECT `user_id`, `first_name`, `last_name`, `email`, `username`,
                    `password_hash`, `role`, `status`
             FROM `user` WHERE `email` = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if (!$user) return false;
        if ($user['status'] === 'banned' || $user['status'] === 'suspended') return false;

        // Verify using PHP's native password_verify() — never MD5/SHA1
        if (password_verify($password, $user['password_hash'])) {
            // Update last_active timestamp
            $this->updateLastActive($user['user_id']);
            unset($user['password_hash']); // Never return the hash
            return $user;
        }

        return false;
    }

    /**
     * Update the last_active timestamp for a user.
     */
    public function updateLastActive($user_id)
    {
        $user_id = (int)$user_id;
        $now     = date('Y-m-d H:i:s');
        $stmt    = $this->db->con->prepare(
            "UPDATE `user` SET `last_active` = ? WHERE `user_id` = ?"
        );
        $stmt->bind_param('si', $now, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Check if an email already exists in the database.
     */
    public function emailExists($email)
    {
        $stmt = $this->db->con->prepare(
            "SELECT `user_id` FROM `user` WHERE `email` = ? LIMIT 1"
        );
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
        return $exists;
    }

    /**
     * Count total registered users.
     */
    public function getTotalCount()
    {
        $result = $this->db->con->query("SELECT COUNT(*) as total FROM `user`");
        $row    = $result->fetch_assoc();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Get the wallet balance for a user.
     */
    public function getWalletBalance($user_id)
    {
        $user_id = (int)$user_id;
        $stmt    = $this->db->con->prepare(
            "SELECT `wallet_balance` FROM `user` WHERE `user_id` = ?"
        );
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        $stmt->close();
        return (float)($row['wallet_balance'] ?? 0.00);
    }

    /**
     * Add funds to a user's wallet.
     */
    public function addFunds($user_id, $amount)
    {
        $user_id = (int)$user_id;
        $amount  = (float)$amount;
        if ($amount <= 0) return false;

        $stmt = $this->db->con->prepare(
            "UPDATE `user` SET `wallet_balance` = `wallet_balance` + ? WHERE `user_id` = ?"
        );
        $stmt->bind_param('di', $amount, $user_id);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    }

    /**
     * Get transaction history for a buyer.
     */
    public function getTransactions($buyer_id)
    {
        $buyer_id = (int)$buyer_id;
        $stmt = $this->db->con->prepare(
            "SELECT t.transaction_id, t.amount, t.transaction_date, t.order_status,
                    p.item_name, p.item_image
             FROM `transaction` t
             INNER JOIN `product` p ON t.item_id = p.item_id
             WHERE t.buyer_id = ?
             ORDER BY t.transaction_date DESC"
        );
        $stmt->bind_param('i', $buyer_id);
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
     * Get metrics for a specific user.
     */
    public function getUserMetrics($user_id)
    {
        $user_id = (int)$user_id;
        $metrics = [
            'total_bought' => 0,
            'total_sold' => 0,
            'preferred_category' => 'None'
        ];

        // Total Bought
        $stmt = $this->db->con->prepare("SELECT COUNT(*) as total FROM `transaction` WHERE `buyer_id` = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $metrics['total_bought'] = (int)($row['total'] ?? 0);
        $stmt->close();

        // Total Sold
        $stmt = $this->db->con->prepare("SELECT COUNT(*) as total FROM `transaction` WHERE `seller_id` = ?");
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        $metrics['total_sold'] = (int)($row['total'] ?? 0);
        $stmt->close();

        // Preferred Category
        $stmt = $this->db->con->prepare(
            "SELECT p.item_category, COUNT(p.item_category) as cat_count
             FROM `transaction` t
             JOIN `product` p ON t.item_id = p.item_id
             WHERE t.buyer_id = ?
             GROUP BY p.item_category
             ORDER BY cat_count DESC
             LIMIT 1"
        );
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();
        if ($row) {
            $metrics['preferred_category'] = ucfirst($row['item_category']);
        }
        $stmt->close();

        return $metrics;
    }
}
