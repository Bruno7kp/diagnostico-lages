<?php


namespace App\Segmentation;


use App\Base\Model;
use App\Segmentation\SegmentationGroupModel;

class SegmentationModel extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $segmentation_group_id;

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
            $st = $this->db->prepare("INSERT INTO segmentation (name, segmentation_group_id, description) VALUES (:name, :segmentation_group_id, :description)");
            $st->execute([
                ":name" => $this->name,
                ":segmentation_group_id" => $this->segmentation_group_id,
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
            $st = $this->db->prepare("UPDATE segmentation SET name = :name, segmentation_group_id = :segmentation_group_id, description = :description, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description,
                ":segmentation_group_id" => $this->segmentation_group_id,
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
            $st = $this->db->prepare("DELETE FROM segmentation WHERE id = :id");
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
     * @return SegmentationModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, name, segmentation_group_id, description, created, updated FROM segmentation WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @return SegmentationModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, name, segmentation_group_id, description, created, updated FROM segmentation ORDER BY name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param $id
     * @return SegmentationModel[]
     */
    public function getAllByGroup($id) {
        $st = $this->db->prepare("SELECT id, name, segmentation_group_id, description, created, updated FROM segmentation WHERE segmentation_group_id = :id ORDER BY name");
        $st->execute([":id" => $id]);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return SegmentationGroupModel|null
     */
    public function getGroup() {
        $model = new SegmentationGroupModel();
        return $model->getById($this->segmentation_group_id);
    }
}