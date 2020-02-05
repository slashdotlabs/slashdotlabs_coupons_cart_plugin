<?php
/**
 * @package           CouponsCartPlugin
 */

namespace Slash\Database;

class PaymentsModel
{

    private $table;
    private $primaryKey = 'id';
    private $db;

    public function __construct()
    {
        global $wpdb, $table_prefix;
        $this->table = $table_prefix . "payments";
        $this->db = $wpdb;
    }

    public function fetchPayments()
    {
        return $this->db->get_results("SELECT * FROM {$this->table}");
    }

    public function getByOrderId($order_id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE order_id = %s";
        $query = $this->db->prepare($sql, [$order_id]);
        return $this->db->get_row($query);
    }

    public function insert($data)
    {
        if (empty($data)) return false;
        return $this->db->insert($this->table, $data);
    }

    public function update(array $data, array $whereClause)
    {
        return $this->db->update($this->table, $data, $whereClause);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, [$this->primaryKey => $id]);
    }

    public function getCouponCount($coupon_name)
    {
        $sql = "SELECT COUNT(*) AS count FROM {$this->table} WHERE customer_coupon LIKE %s";
        $query = $this->db->prepare($sql, [$coupon_name."%"]);
        $row = $this->db->get_row($query);
        return $row->count + 1;
    }
}