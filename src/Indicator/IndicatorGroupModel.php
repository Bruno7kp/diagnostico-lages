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
     * @return IndicatorGroupModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, name, categories_id, description, description, created, updated FROM indicator_group ORDER BY name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param $id
     * @return IndicatorGroupModel[]
     */
    public function getAllByCategory($id) {
        $st = $this->db->prepare("SELECT id, name, categories_id, description, created, updated FROM indicator_group WHERE categories_id = :id ORDER BY name");
        $st->execute([":id" => $id]);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return CategoriesModel|null
     */
    public function getCategory() {
        $model = new CategoriesModel();
        return $model->getById($this->categories_id);
    }
}
