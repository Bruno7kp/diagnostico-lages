<?php


namespace App\Region;


use App\Base\Model;
use App\Categories\CategoriesModel;
use App\Indicator\IndicatorModel;
use App\Segmentation\SegmentationGroupModel;

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
    public $updated;

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
     * @param $search
     * @param $from
     * @param $offset
     * @param bool $datatables
     * @return RegionModel[]
     */
    public function getAll($search, $from, $offset, $datatables = true) {
        $st = $this->db->prepare("SELECT id, name FROM region WHERE name LIKE :search ORDER BY city DESC, name LIMIT ".$from.", ".$offset);
        $st->execute([":search" => $search]);
        if ($datatables)
            return $st->fetchAll(\PDO::FETCH_NUM);
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @return RegionModel[]
     */
    public function getFullList() {
        $st = $this->db->prepare("SELECT id, name, description, city, created, updated FROM region ORDER BY city DESC, name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param string $search
     * @return int
     */
    public function getTotal($search = "%%") {
        $st = $this->db->prepare("SELECT COUNT(id) as total FROM region WHERE name LIKE :search");
        $st->execute([":search" => $search]);
        $res = $st->fetch(\PDO::FETCH_OBJ);
        if ($res)
            return intval($res->total);
        return 0;
    }

    /**
     * @return array[]
     */
    public function periods() {
        $st = $this->db->prepare("SELECT v.indicator_period FROM indicator_value v WHERE v.region_id = :id GROUP BY v.indicator_period ORDER BY v.indicator_period");
        $st->execute([":id" => $this->id]);
        $fill = $st->fetchAll(\PDO::FETCH_ASSOC);
        if ($fill)
            return $fill;
        $now = new \DateTime();
        return [["indicator_period" => $now->format("Y")]];
    }

    public function getYearlyData($periods) {
        // categories -> group -> indicator -> year -> data
        $seg = new SegmentationGroupModel();
        $category = new CategoriesModel();
        $categories = $category->getFullList(true);
        foreach ($categories as $category) {
            foreach ($category->groups as $group) {
                foreach ($group->indicators as $segid => $array) {
                    $segmentation = $seg->getById($segid);
                    if ($segmentation) {
                        foreach ($array as $indicator) {
                            $indicator->getYearlyRegionValue($periods, $this);
                        }
                        $segmentation->indicators = $array;
                        $group->segmentations[] = $segmentation;
                    }
                }
            }
        }

        return $categories;
    }
}