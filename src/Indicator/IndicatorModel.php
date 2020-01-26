<?php


namespace App\Indicator;


use App\Base\Model;
use App\Indicator\IndicatorGroupModel;
use App\Segmentation\SegmentationGroupModel;
use App\Segmentation\SegmentationModel;

class IndicatorModel extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $indicator_group_id;

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
     * @see IndicatorType
     */
    public $type;

    /**
     * @var string
     */
    public $created;

    /**
     * @var string
     */
    public $updated;

    /**
     * @var array
     */
    public $segmentations = [];

    /**
     * @return bool
     */
    public function insert() {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("INSERT INTO indicator (name, indicator_group_id, description, type) VALUES (:name, :indicator_group_id, :description, :type)");
            $st->execute([
                ":name" => $this->name,
                ":indicator_group_id" => $this->indicator_group_id,
                ":description" => $this->description,
                ":type" => $this->type
            ]);
            $this->id = intval($this->db->lastInsertId());
            $sst = $this->db->prepare("INSERT INTO indicator_segmentation_group (indicator_id, segmentation_group_id) VALUES (:indicator_id, :segmentation_group_id)");
            foreach ($this->segmentations as $id) {
                $sst->execute([
                    ":indicator_id" => $this->id,
                    "segmentation_group_id" => $id
                ]);
            }
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
            $st = $this->db->prepare("UPDATE indicator SET name = :name, indicator_group_id = :indicator_group_id, description = :description, type = :type, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description,
                ":indicator_group_id" => $this->indicator_group_id,
                ":type" => $this->type,
                ":id" => $this->id
            ]);
            $dst = $this->db->prepare("DELETE FROM indicator_segmentation_group WHERE indicator_id = :indicator_id");
            $dst->execute([":indicator_id" => $this->id]);
            $sst = $this->db->prepare("INSERT INTO indicator_segmentation_group (indicator_id, segmentation_group_id) VALUES (:indicator_id, :segmentation_group_id)");
            foreach ($this->segmentations as $id) {
                $sst->execute([
                    ":indicator_id" => $this->id,
                    "segmentation_group_id" => $id
                ]);
            }
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
            $st = $this->db->prepare("DELETE FROM indicator WHERE id = :id");
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
     * @return IndicatorModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, name, indicator_group_id, type, description, created, updated FROM indicator WHERE id = :id");
        $st->execute([":id" => $id]);
        $model = $st->fetchObject(__CLASS__);
        if ($model instanceof IndicatorModel)
            $model->fillSegmentations();
        return $model;
    }

    /**
     * @return IndicatorModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, name, indicator_group_id, type, description, created, updated FROM indicator ORDER BY name");
        $st->execute();
        $list = $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        foreach ($list as $item) {
            if ($item instanceof IndicatorModel)
                $item->fillSegmentations();
        }
        return $list;
    }

    /**
     * @param $id
     * @return IndicatorModel[]
     */
    public function getAllByGroup($id) {
        $st = $this->db->prepare("SELECT id, name, indicator_group_id, type, description, created, updated FROM indicator WHERE indicator_group_id = :id ORDER BY name");
        $st->execute([":id" => $id]);
        $list = $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
        foreach ($list as $item) {
            if ($item instanceof IndicatorModel)
                $item->fillSegmentations();
        }
        return $list;
    }

    /**
     * @return IndicatorGroupModel|null
     */
    public function getGroup() {
        $model = new IndicatorGroupModel();
        return $model->getById($this->indicator_group_id);
    }

    /**
     * @return $this
     */
    public function fillSegmentations() {
        $st = $this->db->prepare("SELECT segmentation_group_id FROM indicator_segmentation_group WHERE indicator_id = :indicator_id");
        $st->execute([":indicator_id" => $this->id]);
        $fill = $st->fetchAll(\PDO::FETCH_ASSOC);
        foreach ($fill as $item) {
            $this->segmentations[] = $item["segmentation_group_id"];
        }
        return $this;
    }

    /**
     * @return SegmentationGroupModel[]
     */
    public function getSegmentations() {
        $model = new SegmentationGroupModel();
        $list = [];
        foreach ($this->segmentations as $id) {
            $item = $model->getById($id);
            if ($item) {
                $list[] = $item;
            }
        }
        return $list;
    }
}
