<?php


namespace App\User;


use App\Base\Model;

class UserModel extends Model
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
    public $email;

    /**
     * @var string
     */
    public $password;

    /**
     * @var string
     */
    public $role = Roles::GUEST;

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
            $pepper = getenv("HASH");
            $pwd = $this->password;
            $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
            $this->password = password_hash($pwd_peppered, PASSWORD_ARGON2ID);

            $st = $this->db->prepare("INSERT INTO user (name, email, role, password) VALUES (:name, :email, :role, :password)");
            $st->execute([
                ":name" => $this->name,
                ":email" => $this->email,
                ":role" => $this->role,
                ":password" => $this->password
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
            $st = $this->db->prepare("UPDATE user SET name = :name, email = :email, role = :role, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":name" => $this->name,
                ":email" => $this->email,
                ":role" => $this->role,
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
    public function updatePassword() {
        $this->db->beginTransaction();
        try {
            $pepper = getenv("HASH");
            $pwd = $this->password;
            $pwd_peppered = hash_hmac("sha256", $pwd, $pepper);
            $this->password = password_hash($pwd_peppered, PASSWORD_ARGON2ID);

            $st = $this->db->prepare("UPDATE user SET password = :password, updated = CURRENT_TIMESTAMP WHERE id = :id");
            $st->execute([
                ":password" => $this->password,
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
            $st = $this->db->prepare("DELETE FROM user WHERE id = :id");
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
     * @return UserModel|null
     */
    public function getById($id) {
        $st = $this->db->prepare("SELECT id, name, role, created, updated, email FROM user WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @param $id
     * @return UserModel|null
     */
    public function getWithPasswordById($id) {
        $st = $this->db->prepare("SELECT id, name, password, role, created, updated, email FROM user WHERE id = :id");
        $st->execute([":id" => $id]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @param $email
     * @return UserModel|null
     */
    public function getByEmail($email) {
        $st = $this->db->prepare("SELECT id, name, password, role, created, updated, email FROM user WHERE email = :email");
        $st->execute([":email" => $email]);
        return $st->fetchObject(__CLASS__);
    }

    /**
     * @return UserModel[]
     */
    public function getAll() {
        $st = $this->db->prepare("SELECT id, name, role, created, updated, email FROM user ORDER BY name");
        $st->execute();
        return $st->fetchAll(\PDO::FETCH_CLASS, __CLASS__);
    }

    /**
     * @param $password
     * @return bool
     */
    public function login($password) {
        $pepper = getenv("HASH");
        $pwd_peppered = hash_hmac("sha256", $password, $pepper);
        return password_verify($pwd_peppered, $this->password);
    }
}
