<?php


namespace App\Log;


use App\Base\Model;

class LogModel extends Model
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int|null
     */
    public $user_id;

    /**
     * @var string|null
     */
    public $entity;

    /**
     * @var int|null;
     */
    public $entity_id;

    /**
     * @var string
     */
    public $action;

    /**
     * @var string
     */
    public $created;

    /**
     * @return bool
     */
    public function insert() {
        $this->db->beginTransaction();
        try {
            $st = $this->db->prepare("INSERT INTO log (user_id, entity, entity_id, action) VALUES (:user_id, :entity, :entity_id, :action)");
            $st->execute([
                ":user_id" => $this->user_id,
                ":entity" => $this->entity,
                ":entity_id" => $this->entity_id,
                ":action" => $this->action
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
     * @param $search
     * @param int $from
     * @param int $offset
     * @return LogModel[]
     */
    public function getAll($search, $from, $offset) {
        $st = $this->db->prepare("SELECT id, action, created FROM log WHERE action LIKE :search ORDER BY id DESC LIMIT ".$from.", ".$offset);
        $st->execute([
            ":search" => $search
        ]);
        return $st->fetchAll(\PDO::FETCH_NUM);
    }

    /**
     * @param string $search
     * @return int
     */
    public function getTotal($search = "%%") {
        $st = $this->db->prepare("SELECT COUNT(id) as total FROM log WHERE action LIKE :search");
        $st->execute([":search" => $search]);
        $res = $st->fetch(\PDO::FETCH_OBJ);
        if ($res)
            return intval($res->total);
        return 0;
    }

    /**
     * @param $id
     * @return LogModel[]
     */
    public function getAllByUser($id) {
        $st = $this->db->prepare("SELECT id, user_id, entity, entity_id, action, created FROM log WHERE user_id = :id ORDER BY id DESC");
        $st->execute([
            ":id" => $id
        ]);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param $entity
     * @param $id
     * @return LogModel[]
     */
    public function getAllByEntity($entity, $id) {
        $st = $this->db->prepare("SELECT id, user_id, entity, entity_id, action, created FROM log WHERE entity = :entity AND entity_id = :id ORDER BY id DESC");
        $st->execute([
            ":entity" => $entity,
            ":id" => $id
        ]);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }
}
