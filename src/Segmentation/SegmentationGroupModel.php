<?php


namespace App\Segmentation;


use App\Base\Model;

class SegmentationGroupModel extends Model
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
            $st = $this->db->prepare("INSERT INTO segmentation_group (name, description) VALUES (:name, :description)");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description
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
            $st = $this->db->prepare("UPDATE segmentation_group SET name = :name, description = :description, updated = CURRENT_TIMESTAMP WHERE id = :id");
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
            $st = $this->db->prepare("DELETE FROM segmentation_group WHERE id = :id");
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
     * @return SegmentationGroupModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, name, description, created, updated FROM segmentation_group WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @return SegmentationGroupModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, name, description, created, updated FROM segmentation_group ORDER BY name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }
}
