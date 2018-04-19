<?php
declare(strict_types=1);

namespace Shrtnr\DAO;

use Shrtnr\Rule;
use Shrtnr\User;
use Shrtnr\Exception\AddRuleException;
use Shrtnr\Exception\NoRuleException;

class Rules
{
    private $db;

    public function __construct($dsn = null, $dbUser = null, $dbPass = null)
    {
        $this->db = getDB($dsn, $dbUser, $dbPass);
    }

    public function getAll(int $page = 0, int $perpage = 20) : array
    {
        $limitTo = $page * $perpage;
        $stmt = $this->db->prepare("
            SELECT * FROM rules ORDER BY modified_at,created_at DESC LIMIT $page,$limitTo
        ");
        $stmt->execute([]);
        $rows = $stmt->fetchAll();
        $rules = [];
        foreach ($rows as $row) {
            $rule = new Rule(
                $row["user_id"],
                $row["from"],
                $row["to"],
                \DateTime::createFromFormat("Y-m-d H:i:s", $row["created_at"]),
                ($row["enabled"] == 1 ? true : false),
                \DateTime::createFromFormat("Y-m-d H:i:s", $row["created_at"])
            );
            $rule->setId(intval($row["id"]));
            $rules[] = $rule;
        }
        return $rules;
    }

    public function add(string $from, string $to, string $userId) : Rule
    {
        $now = new \DateTime();
        $stmt = $this->db->prepare("
            INSERT INTO rules (`from`,`to`,user_id,created_at,`enabled`,modified_at) VALUES (?,?,?,?,1,?)
        ");
        $res = $stmt->execute([
            $from,
            $to,
            $userId,
            $now->format("Y-m-d H:i:s"),
            $now->format("Y-m-d H:i:s")
            ]);
        if (!$res) {
            throw new AddRuleException();
        }
        $id = intval($this->db->lastInsertId());
        
        $rule = new Rule(
            $userId,
            $from,
            $to,
            $now,
            true,
            $now
        );
        $rule->setId($id);
        return $rule;
    }

    public function getByFromUrl(string $fromUrl) : Rule
    {
        $stmt = $this->db->prepare("
            SELECT * FROM rules WHERE `from` = ?
        ");
        $stmt->execute([$fromUrl]);
        $rows = $stmt->fetchAll();
        if (count($rows) !== 1) {
            throw new NoRuleException($fromUrl);
        }
        $row = $rows[0];
        $rule = new Rule(
            $row["user_id"],
            $row["from"],
            $row["to"],
            \DateTime::createFromFormat("Y-m-d H:i:s", $row["created_at"]),
            ($row["enabled"] == 1 ? true : false),
            \DateTime::createFromFormat("Y-m-d H:i:s", $row["created_at"])
        );
        $rule->setId(intval($row["id"]));
        return $rule;
    }

    public function getById(int $id) : Rule
    {
        $stmt = $this->db->prepare("
            SELECT * FROM rules WHERE id = ?
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        $rule = new Rule(
            $row["user_id"],
            $row["from"],
            $row["to"],
            \DateTime::createFromFormat("Y-m-d H:i:s", $row["created_at"]),
            ($row["enabled"] == 1 ? true : false),
            \DateTime::createFromFormat("Y-m-d H:i:s", $row["created_at"])
        );
        $rule->setId($row["id"]);
        return $rule;
    }

    public function edit(int $id, string $from, string $to) : self
    {
        $now = new \DateTime();
        $stmt = $this->db->prepare("
            UPDATE rules SET `from` = ?, `to` = ?, `modified_at` = ?
        ");
        $stmt->execute([
            $from,
            $to,
            $now->format("Y-m-d H:i:s")
        ]);
        return $this;
    }

    public function setEnabled(int $id, bool $enabled) : self
    {
        $now = new \DateTime();
        $stmt = $this->db->prepare("
            UPDATE rules SET `enabled` = ?
        ");
        $stmt->execute([($enabled ? 1 : 0)]);
        return $this;
    }

    public function delete(int $id) : self 
    {
        $stmt = $this->db->prepare("
            DELETE FROM rules WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $this;
    }
}
