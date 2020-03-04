<?php


namespace App\Categories;


use App\Base\Model;

class CategoriesModel extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $updated;

    /**
     * @return bool
     */
    public function insert() {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("INSERT INTO categories (name, description) VALUES (:name, :description)");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description
            ]);
            $this->id = intval($this->db->lastInsertId());
            $this->db->commit();
            return $this->id > 0;
        } catch (\Exception $ex) {
            $this->error = $ex;
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * @return bool
     */
    public function update() {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("UPDATE categories SET name = :name, description = :description, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description,
                ":id" => $this->id
            ]);
            $this->db->commit();
            return $st->rowCount() > 0;
        } catch (\Exception $ex) {
            $this->error = $ex;
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * @return bool
     */
    public function delete() {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("DELETE FROM categories WHERE id = :id");
            $st->execute([
                ":id" => $this->id
            ]);
            $this->db->commit();
            return $st->rowCount() > 0;
        } catch (\Exception $ex) {
            $this->error = $ex;
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * @param $id
     * @return CategoriesModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, name, description, created, updated FROM categories WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @param $search
     * @param $from
     * @param $offset
     * @param bool $datatables
     * @return CategoriesModel[]
     */
    public function getAll($search, $from, $offset, $datatables = true) {
        $st = $this->db->prepare("SELECT id, name FROM categories WHERE name LIKE :search ORDER BY name LIMIT ".$from.", ".$offset);
        $st->execute([":search" => $search]);
        if ($datatables)
            return $st->fetchAll(\PDO::FETCH_NUM);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return CategoriesModel[]
     */
    public function getFullList() {
        $st = $this->db->prepare("SELECT id, name, description, created, updated FROM categories");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param string $search
     * @return int
     */
    public function getTotal($search = "%%") {
        $st = $this->db->prepare("SELECT COUNT(id) as total FROM categories WHERE name LIKE :search");
        $st->execute([":search" => $search]);
        $res = $st->fetch(\PDO::FETCH_OBJ);
        if ($res)
            return intval($res->total);
        return 0;
    }
}
