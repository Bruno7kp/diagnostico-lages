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
     * @var int|null
     */
    public $segmentation_id = null;

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
                i.id, i.indicator_id, i.region_id, i.segmentation_id, i.indicator_period, i.value, i.description, i.created, i.updated,
                r.city, r.name as region_name, r.description as region_description
            FROM 
                indicator_value i
                LEFT JOIN region r on i.region_id = r.id
            WHERE
                (:indicator_id IS NULL OR i.indicator_id = :indicator_id) AND
                (:region_id IS NULL OR i.region_id = :region_id) AND
                (:segmentation_id IS NULL OR i.indicator_id = :segmentation_id) AND
                (:period IS NULL OR i.indicator_period = :period)
            ORDER BY r.city DESC, r.name, i.updated DESC");
        $st->execute([
            ":indicator_id" => $indicator_id,
            ":region_id" => $region_id,
            ":segmentation_id" => $segmentation_id,
            ":period" => $year
        ]);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param IndicatorValueModel[] $values
     * @return bool
     */
    public function batchInsert($values) {
        $this->db->beginTransaction();
        try {
            $rm = $this->db->prepare("DELETE FROM indicator_value WHERE indicator_id = :indicator_id AND region_id = :region_id AND indicator_period = :period");
            $st = $this->db->prepare("INSERT INTO indicator_value (indicator_id, region_id, segmentation_id, indicator_period, value, description) VALUES (:indicator_id, :region_id, :segmentation_id, :period, :value, :description)");
            foreach ($values as $value) {
                $rm->execute([
                    ":indicator_id" => $value->indicator_id,
                    ":region_id" => $value->region_id,
                    ":period" => $value->indicator_period
                ]);
                $st->execute([
                    ":indicator_id" => $value->indicator_id,
                    ":region_id" => $value->region_id,
                    ":segmentation_id" => $value->segmentation_id,
                    ":period" => $value->indicator_period,
                    ":value" => $value->value,
                    ":description" => $value->description
                ]);
            }
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
     * @param IndicatorValueModel[] $values
     * @return bool
     */
    public function batchUpdate($values) {
        $this->db->beginTransaction();
        try {
            $rows = 0;
            $st = $this->db->prepare("UPDATE indicator_value SET indicator_id = :indicator_id, region_id = :region_id, segmentation_id = :segmentation_id, indicator_period = :period, value = :value, description = :description, updated = CURRENT_TIMESTAMP WHERE id = :id");
            foreach ($values as $value) {
                $st->execute([
                    ":indicator_id" => $value->indicator_id,
                    ":region_id" => $value->region_id,
                    ":segmentation_id" => $value->segmentation_id,
                    ":period" => $value->indicator_period,
                    ":value" => $value->value,
                    ":description" => $value->description,
                    ":id" => $value->id
                ]);
                $rows = $st->rowCount();
            }

            $this->db->commit();
            return $rows > 0;
        } catch (\Exception $ex) {
            $this->error = $ex;
            $this->db->rollBack();
            return false;
        }
    }

    /**
     * @param $values
     * @return IndicatorValueModel[]
     */
    public function arrayByRegionId($values) {
        $list = [];
        /**
         * @var IndicatorValueModel $value
         */
        foreach ($values as $value) {
            $list[$value->region_id] = $value;
        }
        return $list;
    }
}
