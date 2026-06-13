<?php

class Cart
{
    public $db = null;

    public function __construct(DBController $db)
    {
        if (!isset($db->con)) return;
        $this->db = $db;
    }

    /**
     * Add an item to the cart for a user.
     * If item already exists, increment quantity instead.
     */
    public function addToCart($user_id, $item_id)
    {
        $user_id = (int)$user_id;
        $item_id = (int)$item_id;

        // Check if product exists and quantity > 0
        $stmt0 = $this->db->con->prepare("SELECT `item_quantity` FROM `product` WHERE `item_id` = ?");
        $stmt0->bind_param('i', $item_id);
        $stmt0->execute();
        $res0 = $stmt0->get_result()->fetch_assoc();
        $stmt0->close();
        if (!$res0 || $res0['item_quantity'] <= 0) {
            return false; // Out of stock
        }

        // Check if already in cart
        $stmt = $this->db->con->prepare(
            "SELECT `cart_id`, `quantity` FROM `cart` WHERE `user_id` = ? AND `item_id` = ?"
        );
        $stmt->bind_param('ii', $user_id, $item_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $existing = $result->fetch_assoc();
        $stmt->close();

        if ($existing) {
            // Increment quantity
            $new_qty = $existing['quantity'] + 1;
            $stmt2   = $this->db->con->prepare(
                "UPDATE `cart` SET `quantity` = ? WHERE `cart_id` = ?"
            );
            $stmt2->bind_param('ii', $new_qty, $existing['cart_id']);
            $result2 = $stmt2->execute();
            $stmt2->close();
        } else {
            // Insert new cart row
            $now  = date('Y-m-d H:i:s');
            $stmt2 = $this->db->con->prepare(
                "INSERT INTO `cart` (`user_id`, `item_id`, `quantity`, `added_at`) VALUES (?, ?, 1, ?)"
            );
            $stmt2->bind_param('iis', $user_id, $item_id, $now);
            $result2 = $stmt2->execute();
            $stmt2->close();
        }

        if ($result2) {
            // Deduct stock
            $this->db->con->query("UPDATE `product` SET `item_quantity` = `item_quantity` - 1 WHERE `item_id` = {$item_id}");
            // Update in_stock flag if zero
            $this->db->con->query("UPDATE `product` SET `item_in_stock` = 0 WHERE `item_quantity` <= 0 AND `item_id` = {$item_id}");
        }

        return $result2;
    }

    /**
     * Update quantity of a cart item.
     */
    public function updateQuantity($user_id, $item_id, $quantity)
    {
        $user_id  = (int)$user_id;
        $item_id  = (int)$item_id;
        $quantity = (int)$quantity;

        if ($quantity <= 0) {
            return $this->removeFromCart($user_id, $item_id);
        }

        // Get current cart quantity
        $stmt0 = $this->db->con->prepare("SELECT `quantity` FROM `cart` WHERE `user_id` = ? AND `item_id` = ?");
        $stmt0->bind_param('ii', $user_id, $item_id);
        $stmt0->execute();
        $res0 = $stmt0->get_result()->fetch_assoc();
        $stmt0->close();

        if (!$res0) return false;
        
        $current_qty = $res0['quantity'];
        $diff = $quantity - $current_qty;

        if ($diff > 0) {
            // Check if product has enough stock
            $stmt1 = $this->db->con->prepare("SELECT `item_quantity` FROM `product` WHERE `item_id` = ?");
            $stmt1->bind_param('i', $item_id);
            $stmt1->execute();
            $res1 = $stmt1->get_result()->fetch_assoc();
            $stmt1->close();

            if (!$res1 || $res1['item_quantity'] < $diff) {
                return false; // Not enough stock to increase quantity
            }
        }

        // Update cart
        $stmt = $this->db->con->prepare(
            "UPDATE `cart` SET `quantity` = ? WHERE `user_id` = ? AND `item_id` = ?"
        );
        $stmt->bind_param('iii', $quantity, $user_id, $item_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result && $diff != 0) {
            // Update product stock (diff can be negative, so this adds stock back)
            $this->db->con->query("UPDATE `product` SET `item_quantity` = `item_quantity` - {$diff} WHERE `item_id` = {$item_id}");
            if ($diff > 0) {
                $this->db->con->query("UPDATE `product` SET `item_in_stock` = 0 WHERE `item_quantity` <= 0 AND `item_id` = {$item_id}");
            } else {
                $this->db->con->query("UPDATE `product` SET `item_in_stock` = 1 WHERE `item_quantity` > 0 AND `item_id` = {$item_id}");
            }
        }

        return $result;
    }

    /**
     * Remove a single item from the cart.
     */
    public function removeFromCart($user_id, $item_id)
    {
        $user_id = (int)$user_id;
        $item_id = (int)$item_id;

        // Get current cart quantity to restore
        $stmt0 = $this->db->con->prepare("SELECT `quantity` FROM `cart` WHERE `user_id` = ? AND `item_id` = ?");
        $stmt0->bind_param('ii', $user_id, $item_id);
        $stmt0->execute();
        $res0 = $stmt0->get_result()->fetch_assoc();
        $stmt0->close();

        $stmt = $this->db->con->prepare(
            "DELETE FROM `cart` WHERE `user_id` = ? AND `item_id` = ?"
        );
        $stmt->bind_param('ii', $user_id, $item_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result && $res0) {
            $qty = $res0['quantity'];
            $this->db->con->query("UPDATE `product` SET `item_quantity` = `item_quantity` + {$qty}, `item_in_stock` = 1 WHERE `item_id` = {$item_id}");
        }

        return $result;
    }

    /**
     * Clear all items from a user's cart.
     */
    public function clearCart($user_id, $restore_stock = true)
    {
        $user_id = (int)$user_id;

        // Get all items to restore stock
        $stmt0 = $this->db->con->prepare("SELECT `item_id`, `quantity` FROM `cart` WHERE `user_id` = ?");
        $stmt0->bind_param('i', $user_id);
        $stmt0->execute();
        $items = $stmt0->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt0->close();

        $stmt = $this->db->con->prepare(
            "DELETE FROM `cart` WHERE `user_id` = ?"
        );
        $stmt->bind_param('i', $user_id);
        $result = $stmt->execute();
        $stmt->close();

        if ($result && $restore_stock) {
            foreach ($items as $item) {
                $iid = (int)$item['item_id'];
                $qty = (int)$item['quantity'];
                $this->db->con->query("UPDATE `product` SET `item_quantity` = `item_quantity` + {$qty}, `item_in_stock` = 1 WHERE `item_id` = {$iid}");
            }
        }

        return $result;
    }

    /**
     * Get all cart items for a user, joined with product data.
     */
    public function getCartItems($user_id)
    {
        $user_id = (int)$user_id;

        $stmt = $this->db->con->prepare(
            "SELECT c.cart_id, c.quantity, c.item_id,
                    p.item_name, p.item_brand, p.item_price,
                    p.item_original_price, p.item_image, p.item_details,
                    p.item_category, p.item_in_stock, p.item_status, p.seller_id,
                    u.first_name as seller_first_name, u.last_name as seller_last_name
             FROM `cart` c
             INNER JOIN `product` p ON c.item_id = p.item_id
             LEFT JOIN `user` u ON p.seller_id = u.user_id
             WHERE c.user_id = ?
             ORDER BY c.added_at DESC"
        );
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result      = $stmt->get_result();
        $resultArray = [];
        while ($row = $result->fetch_assoc()) {
            $resultArray[] = $row;
        }
        $stmt->close();
        return $resultArray;
    }

    /**
     * Get total number of unique items in a user's cart.
     */
    public function getCartCount($user_id)
    {
        $user_id = (int)$user_id;

        $stmt = $this->db->con->prepare(
            "SELECT SUM(`quantity`) as total FROM `cart` WHERE `user_id` = ?"
        );
        $stmt->bind_param('i', $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        $stmt->close();
        return (int)($row['total'] ?? 0);
    }

    /**
     * Calculate total price of all items in a user's cart.
     */
    public function getCartTotal($user_id)
    {
        $items = $this->getCartItems($user_id);
        $total = 0;
        foreach ($items as $item) {
            $total += $item['item_price'] * $item['quantity'];
        }
        return $total;
    }

    /**
     * Process checkout for a user using their digital wallet.
     */
    public function checkoutCart($buyer_id)
    {
        $buyer_id = (int)$buyer_id;
        $items = $this->getCartItems($buyer_id);
        if (empty($items)) {
            return ['success' => false, 'message' => 'Cart is empty.'];
        }

        // Check if all items are still active
        foreach ($items as $item) {
            if ($item['item_status'] !== 'active') {
                return ['success' => false, 'message' => "Item '{$item['item_name']}' is no longer available."];
            }
        }

        // Check wallet balance
        $total = $this->getCartTotal($buyer_id);
        $user_obj = new User($this->db);
        $balance = $user_obj->getWalletBalance($buyer_id);

        if ($balance < $total) {
            return ['success' => false, 'message' => 'Insufficient funds in digital wallet.'];
        }

        // Begin transaction
        $this->db->con->begin_transaction();

        try {
            // Deduct from buyer
            $stmt = $this->db->con->prepare("UPDATE `user` SET `wallet_balance` = `wallet_balance` - ? WHERE `user_id` = ?");
            $stmt->bind_param('di', $total, $buyer_id);
            $stmt->execute();
            $stmt->close();

            // Process each item
            foreach ($items as $item) {
                $seller_id = $item['seller_id'];
                $item_id = $item['item_id'];
                $price = $item['item_price'] * $item['quantity'];

                // Add funds to seller
                if ($seller_id) {
                    $stmt = $this->db->con->prepare("UPDATE `user` SET `wallet_balance` = `wallet_balance` + ? WHERE `user_id` = ?");
                    $stmt->bind_param('di', $price, $seller_id);
                    $stmt->execute();
                    $stmt->close();
                }

                // Create transaction record
                $now = date('Y-m-d H:i:s');
                $seller_for_trans = $seller_id ?: 0;
                $stmt = $this->db->con->prepare("INSERT INTO `transaction` (`buyer_id`, `seller_id`, `item_id`, `amount`, `transaction_date`) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param('iiids', $buyer_id, $seller_for_trans, $item_id, $price, $now);
                $stmt->execute();
                $stmt->close();

                // Mark product as sold ONLY if quantity is now 0 (remember stock was already deducted on Add to Cart)
                $stmt = $this->db->con->prepare("UPDATE `product` SET `item_status` = 'sold' WHERE `item_quantity` <= 0 AND `item_id` = ?");
                $stmt->bind_param('i', $item_id);
                $stmt->execute();
                $stmt->close();
            }

            // Clear cart without restoring stock
            $this->clearCart($buyer_id, false);

            // Commit
            $this->db->con->commit();
            return ['success' => true, 'message' => 'Checkout successful!'];

        } catch (Exception $e) {
            $this->db->con->rollback();
            return ['success' => false, 'message' => 'Checkout failed: ' . $e->getMessage()];
        }
    }
}