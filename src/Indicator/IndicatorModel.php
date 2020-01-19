<?php


namespace App\Indicator;


use App\Base\Model;
use App\Indicator\IndicatorGroupModel;

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
            $st = $this->db->prepare("UPDATE indicator SET name = :name, indicator_group_id = :indicator_group_id, description = :description, type = :type, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":name" => $this->name,
                ":description" => $this->description,
                ":indicator_group_id" => $this->indicator_group_id,
                ":type" => $this->type,
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
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @return IndicatorModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, name, indicator_group_id, type, description, created, updated FROM indicator ORDER BY name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param $id
     * @return IndicatorModel[]
     */
    public function getAllByGroup($id) {
        $st = $this->db->prepare("SELECT id, name, indicator_group_id, type, description, created, updated FROM indicator WHERE indicator_group_id = :id ORDER BY name");
        $st->execute([":id" => $id]);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return IndicatorGroupModel|null
     */
    public function getGroup() {
        $model = new IndicatorGroupModel();
        return $model->getById($this->indicator_group_id);
    }
}
