<?php


namespace App\Region;


use App\Base\Model;

class RegionModel extends Model
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
     * @var bool
     */
    public $city;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $update;

    /**
     * @return bool
     */
    public function insert() {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("INSERT INTO region (name, description, city) VALUES (:name, :description, :city)");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description,
                ":city" => $this->city
            ]);
            $this->db->commit();
            $this->id = intval($this->db->lastInsertId());
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
            $st = $this->db->prepare("UPDATE region SET name = :name, description = :description, city = :city, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description,
                ":city" => $this->city,
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
            $st = $this->db->prepare("DELETE FROM region WHERE id = :id");
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
     * @return RegionModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, name, description, city, created, updated FROM region WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @return RegionModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, name, description, city, created, updated FROM region ORDER BY name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }
}