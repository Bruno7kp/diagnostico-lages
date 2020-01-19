<?php


namespace App\Indicator;


use App\Base\Model;
use App\Segmentation\SegmentationGroupModel;
use App\Segmentation\SegmentationModel;

class IndicatorValueModel extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $indicator_id;

    /**
     * @var int
     */
    public $region_id;

    /**
     * @var int
     */
    public $segmentation_id;

    /**
     * @var string
     */
    public $indicator_period;

    /**
     * @var string
     */
    public $value;

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
            $st = $this->db->prepare("INSERT INTO indicator_value (indicator_id, region_id, segmentation_id, indicator_period, value, description) VALUES (:indicator_id, :region_id, :segmentation_id, :period, :value, :description)");
            $st->execute([
                ":indicator_id" => $this->indicator_id,
                ":region_id" => $this->region_id,
                ":segmentation_id" => $this->segmentation_id,
                ":period" => $this->indicator_period,
                ":value" => $this->value,
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
            $st = $this->db->prepare("UPDATE indicator_value SET indicator_id = :indicator_id, region_id = :region_id, segmentation_id = :segmentation_id, indicator_period = :period, value = :value, description = :description, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":indicator_id" => $this->indicator_id,
                ":region_id" => $this->region_id,
                ":segmentation_id" => $this->segmentation_id,
                ":period" => $this->indicator_period,
                ":value" => $this->value,
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
            $st = $this->db->prepare("DELETE FROM indicator_value WHERE id = :id");
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
     * @return IndicatorValueModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, indicator_id, region_id, segmentation_id, indicator_period, value, description, created, updated FROM indicator_value WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @return IndicatorValueModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, indicator_id, region_id, segmentation_id, indicator_period, value, description, created, updated FROM indicator_value ORDER BY updated DESC");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param string|null $year
     * @param int|null $indicator_id
     * @param int|null $region_id
     * @param int|null $segmentation_id
     * @return IndicatorValueModel[]
     */
    public function getByFilter($year = null, $indicator_id = null, $region_id = null, $segmentation_id = null) {
        $st = $this->db->prepare("SELECT 
                id, indicator_id, region_id, segmentation_id, indicator_period, value, description, created, updated 
            FROM 
                indicator_value 
            WHERE
                (:indicator_id IS NULL OR indicator_id = :indicator_id) AND
                (:region_id IS NULL OR region_id = :region_id) AND
                (:segmentation_id IS NULL OR indicator_id = :segmentation_id) AND
                (:period IS NULL OR indicator_period = :period)
            ORDER BY updated DESC");
        $st->execute([
            ":indicator_id" => $this->indicator_id,
            ":region_id" => $this->region_id,
            ":segmentation_id" => $this->segmentation_id,
            ":period" => $this->indicator_period
        ]);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }
}
