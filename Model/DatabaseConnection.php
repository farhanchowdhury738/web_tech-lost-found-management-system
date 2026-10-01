
<?php
class DatabaseConnection
{
    function openConnection()
    {
        $db_host = "localhost";
        $db_user = "root";
        $db_password = "";
        $db_name = "khoja_khuji";

        $connection = new mysqli($db_host, $db_user, $db_password, $db_name);
        if ($connection->connect_error) {
            die("Can not connect to the database, please try again. " . $connection->connect_error);
        }
        $connection->set_charset("utf8mb4");
        return $connection;
    }

    function signup($connection, $name, $email, $password, $phone, $role = "user")
    {
        $sql = "INSERT INTO users (name, email, password_hash, phone, role) VALUES ('" . $name . "', '" . $email . "', '" . $password . "', '" . $phone . "', '" . $role . "')";
        return $connection->query($sql);
    }

    function signin($connection, $email, $password)
    {
        $sql = "SELECT * FROM users WHERE email='" . $email . "' AND password_hash='" . $password . "' AND status='active'";
        return $connection->query($sql);
    }

    function emailExists($connection, $email)
    {
        $sql = "SELECT id FROM users WHERE email='" . $email . "'";
        $result = $connection->query($sql);
        return $result && $result->num_rows > 0;
    }

    //nayem - for category management

    function getCategories($connection)
    {
        return $connection->query("SELECT * FROM categories ORDER BY name ASC");
    }
    //nayem - for adding category
    function addCategory($connection, $name, $description)
    {
        return $connection->query("INSERT INTO categories (name, description) VALUES ('" . $name . "', '" . $description . "')");
    }
    //nayem - for updating
    function updateCategory($connection, $id, $name, $description)
    {
        $id = (int) $id;
        return $connection->query("UPDATE categories SET name='" . $name . "', description='" . $description . "' WHERE id=" . $id);
    }
    // nayem - for deleting
    function deleteCategory($connection, $id)
    {
        $id = (int) $id;
        return $connection->query("DELETE FROM categories WHERE id=" . $id);
    }

    function addItem($connection, $user_id, $category_id, $title, $description, $type, $location, $date_lost_found, $image_path, $contact_info)
    {
        $user_id = (int) $user_id;
        $category_id = (int) $category_id;
        $sql = "INSERT INTO items (user_id, category_id, title, description, type, location, date_lost_found, image_path, contact_info, status) VALUES (" . $user_id . ", " . $category_id . ", '" . $title . "', '" . $description . "', '" . $type . "', '" . $location . "', '" . $date_lost_found . "', '" . $image_path . "', '" . $contact_info . "', 'Open')";
        return $connection->query($sql);
    }

    function getItems($connection, $type = "", $search = "", $category_id = "")
    {
        $conditions = [];
        if ($type !== "") {
            $conditions[] = "i.type='" . $type . "'";
        }
        if ($search !== "") {
            $conditions[] = "(i.title LIKE '%" . $search . "%' OR i.description LIKE '%" . $search . "%' OR i.location LIKE '%" . $search . "%')";
        }
        if ($category_id !== "") {
            $category_id = (int) $category_id;
            $conditions[] = "i.category_id=" . $category_id;
        }

        $where = count($conditions) > 0 ? " WHERE " . implode(" AND ", $conditions) : "";
        $sql = "SELECT i.*, c.name AS category_name, u.name AS reporter_name, u.email AS reporter_email, u.phone AS reporter_phone FROM items i 
        INNER JOIN categories c ON i.category_id=c.id 
        INNER JOIN users u ON i.user_id=u.id" . $where . " ORDER BY i.created_at DESC";
        return $connection->query($sql);
    }

    function getItemById($connection, $id)
    {
        $id = (int) $id;
        $sql = "SELECT i.*, c.name AS category_name, u.name AS reporter_name, u.email AS reporter_email, u.phone AS reporter_phone FROM items i INNER JOIN categories c ON i.category_id=c.id INNER JOIN users u ON i.user_id=u.id WHERE i.id=" . $id;
        $result = $connection->query($sql);
        return $result ? $result->fetch_assoc() : null;
    }

    function getUserItems($connection, $user_id)
    {
        $user_id = (int) $user_id;
        return $connection->query("SELECT i.*, c.name AS category_name FROM items i INNER JOIN categories c ON i.category_id=c.id WHERE i.user_id=" . $user_id . " ORDER BY i.created_at DESC");
    }


    function updateItem($connection, $id, $category_id, $title, $description, $type, $location, $date_lost_found, $image_path, $contact_info, $status)
    {
        $id = (int) $id;
        $category_id = (int) $category_id;
        $sql = "UPDATE items SET category_id=" . $category_id . ", title='" . $title . "', description='" . $description . "', type='" . $type . "', location='" . $location . "', date_lost_found='" . $date_lost_found . "', image_path='" . $image_path . "', contact_info='" . $contact_info . "', status='" . $status . "' WHERE id=" . $id;
        return $connection->query($sql);
    }

    function deleteItem($connection, $id)
    {
        $id = (int) $id;
        return $connection->query("DELETE FROM items WHERE id=" . $id);
    }

    function updateItemStatus($connection, $item_id, $status)
    {
        $item_id = (int) $item_id;
        return $connection->query("UPDATE items SET status='" . $status . "' WHERE id=" . $item_id);
    }

    function addClaim($connection, $item_id, $user_id, $name, $email, $phone, $description, $proof_path, $additional_info)
    {
        $item_id = (int) $item_id;
        $user_id = (int) $user_id;
        $sql = "INSERT INTO claims (item_id, user_id, claimant_name, claimant_email, claimant_phone, description, proof_path, additional_info, status) VALUES (" . $item_id . ", " . $user_id . ", '" . $name . "', '" . $email . "', '" . $phone . "', '" . $description . "', '" . $proof_path . "', '" . $additional_info . "', 'Pending')";
        return $connection->query($sql);
    }

    function getUserClaims($connection, $user_id)
    {
        $user_id = (int) $user_id;
        return $connection->query("SELECT cl.*, i.title AS item_title, i.type AS item_type FROM claims cl INNER JOIN items i ON cl.item_id=i.id WHERE cl.user_id=" . $user_id . " ORDER BY cl.created_at DESC");
    }

    function getClaims($connection)
    {
        return $connection->query("SELECT cl.*, i.title AS item_title, u.name AS user_name, u.email AS user_email FROM claims cl INNER JOIN items i ON cl.item_id=i.id INNER JOIN users u ON cl.user_id=u.id ORDER BY cl.created_at DESC");
    }

    function updateClaimStatus($connection, $claim_id, $status)
    {
        $claim_id = (int) $claim_id;
        return $connection->query("UPDATE claims SET status='" . $status . "', returned_at=" . ($status === "Returned" ? "NOW()" : "NULL") . " WHERE id=" . $claim_id);
    }

    //UPDATE PROFILE
    function updateProfile($connection, $id, $name)
    {
        $id = (int) $id;

        $sql = "UPDATE users SET name='" . $name . "' WHERE id=" . $id;

        return $connection->query($sql);
    }

    function changePassword($connection, $id, $password)
    {
        $id = (int) $id;
        return $connection->query("UPDATE users SET password_hash='" . $password . "' WHERE id=" . $id);
    }

    function getUserById($connection, $id)
    {
        $id = (int) $id;
        $result = $connection->query("SELECT * FROM users WHERE id=" . $id);
        return $result ? $result->fetch_assoc() : null;
    }


    // AdminUser functions (Aryan)
    function getUsers($connection)
    {
        return $connection->query("SELECT id, name, email, phone, role, status, created_at FROM users ORDER BY created_at DESC");
    }


    function addUser($connection, $name, $email, $password, $phone, $role = "user", $status = "active")
    {
        return $connection->query("INSERT INTO users (name, email, password_hash, phone, role, status) VALUES ('" . $name . "', '" . $email . "', '" . $password . "', '" . $phone . "', '" . $role . "', '" . $status . "')");
    }

    function updateUser($connection, $id, $name, $email, $phone, $role, $status)
    {
        $id = (int) $id;
        return $connection->query("UPDATE users SET name='" . $name . "', email='" . $email . "', phone='" . $phone . "', role='" . $role . "', status='" . $status . "' WHERE id=" . $id);
    }

    function deleteUser($connection, $id)
    {
        $id = (int) $id;
        return $connection->query("DELETE FROM users WHERE id=" . $id);
    }


    function updateUserStatus($connection, $id, $status)
    {
        $id = (int) $id;
        return $connection->query("UPDATE users SET status='" . $status . "' WHERE id=" . $id);
    }
    
    
    function updateItemStatusByClaimStatus($connection, $claim_id, $claim_status)
{
    $claim_id = (int) $claim_id;

    // Find the item connected to this claim
    $result = $connection->query(
        "SELECT item_id FROM claims WHERE id=" . $claim_id
    );

    if (!$result || $result->num_rows === 0) {
        return false;
    }

    $claim = $result->fetch_assoc();
    $item_id = (int) $claim["item_id"];

    // Pending or Approved = item is unavailable
    if ($claim_status === "Pending" || $claim_status === "Approved") {
        return $connection->query(
            "UPDATE items SET status='Claimed' WHERE id=" . $item_id
        );
    }

    // Rejected or Returned = item becomes available again
    if ($claim_status === "Rejected" || $claim_status === "Returned") {
        return $connection->query(
            "UPDATE items SET status='Open' WHERE id=" . $item_id
        );
    }

    return true;
}
}
?>