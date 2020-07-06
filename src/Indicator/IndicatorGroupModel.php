<?php


namespace App\Indicator;


use App\Base\Model;
use App\Categories\CategoriesModel;

class IndicatorGroupModel extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $categories_id;

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
     * @var string
     */
    public $category = "";

    /**
     * @var IndicatorModel[]
     */
    public $indicators = [];

    /**
     * @return bool
     */
    public function insert() {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("INSERT INTO indicator_group (name, categories_id, description) VALUES (:name, :categories_id, :description)");
            $st->execute([
                ":name" => $this->name,
                ":categories_id" => $this->categories_id,
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
            $st = $this->db->prepare("UPDATE indicator_group SET name = :name, categories_id = :categories_id, description = :description, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description,
                ":categories_id" => $this->categories_id,
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
            $st = $this->db->prepare("DELETE FROM indicator_group WHERE id = :id");
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
     * @return IndicatorGroupModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, name, categories_id, description, created, updated FROM indicator_group WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @param $search
     * @param $from
     * @param $offset
     * @param bool $datatables
     * @return IndicatorGroupModel[]
     */
    public function getAll($search, $from, $offset, $datatables = true) {
        $st = $this->db->prepare("SELECT i.id, i.name, c.name as category FROM indicator_group i LEFT JOIN categories c on i.categories_id = c.id WHERE i.name LIKE :search ORDER BY i.name LIMIT ".$from.", ".$offset);
        $st->execute([":search" => $search]);
        if ($datatables)
            return $st->fetchAll(\PDO::FETCH_NUM);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return IndicatorModel[]
     */
    public function getFullList() {
        $st = $this->db->prepare("SELECT i.id, i.name, c.name as category FROM indicator_group i LEFT JOIN categories c on i.categories_id = c.id ORDER BY i.name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param string $search
     * @return int
     */
    public function getTotal($search = "%%") {
        $st = $this->db->prepare("SELECT COUNT(id) as total FROM indicator_group WHERE name LIKE :search");
        $st->execute([":search" => $search]);
        $res = $st->fetch(\PDO::FETCH_OBJ);
        if ($res)
            return intval($res->total);
        return 0;
    }

    /**
     * @param $id
     * @param bool $child
     * @return IndicatorGroupModel[]
     */
    public function getAllByCategory($id, $child = false) {
        $st = $this->db->prepare("SELECT id, name, categories_id, description, created, updated FROM indicator_group WHERE categories_id = :id ORDER BY name");
        $st->execute([":id" => $id]);
        /** @var IndicatorGroupModel[] $list */
        $list = $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        if ($child) {
            $ind = new IndicatorModel();
            foreach ($list as $item) {
                $item->indicators = $ind->getAllByGroup($item->id);
            }
        }
        return $list;
    }

    /**
     * @return CategoriesModel|null
     */
    public function getCategory() {
        $model = new CategoriesModel();
        return $model->getById($this->categories_id);
    }
}
