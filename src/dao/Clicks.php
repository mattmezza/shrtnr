<?php
declare(strict_types=1);

namespace Shrtnr\DAO;

use Shrtnr\Click;

class Clicks
{
    private $db;

    public function __construct()
    {
        $this->db = getDB();
    }

    public function getAll(int $page = 0, int $perpage = 20) : array
    {
        $limitTo = $page * $perpage;
        $stmt = $this->db->prepare("
            SELECT * FROM clicks ORDER BY clicked_at DESC LIMIT $page,$limitTo
        ");
        $stmt->execute([]);
        $rows = $stmt->fetchAll();
        $clicks = [];
        foreach ($rows as $row) {
            $click = new Click(
                \DateTime::fromFormat("Y-m-d H:i:s", $row["clicked_at"]),
                intval($row["rule_id"]),
                $row["to"],
                $row["from"],
                $row["ip"]
            );
            $click->setId(intval($row["id"]));
            $clicks[] = $click;
        }
        return $clicks;
    }

    public function add(string $from, string $to, int $ruleId, string $ip = null) : Click
    {
        $now = new \DateTime();
        $stmt = $this->db->prepare("
            INSERT INTO clicks (`from`,`to`,rule_id,clicked_at,`ip`) VALUES (?,?,?,?,?)
        ");
        $stmt->execute([
            $from,
            $to,
            $ruleId,
            $now->format("Y-m-d H:i:s"),
            $ip
            ]);
        $id = $this->db->lastInsertId();
        $click = new Click(
            $now,
            intval($ruleId),
            $to,            
            $from,
            $ip
        );
        $click->setId($id);
        return $click;
    }

    public function delete(int $id) : self 
    {
        $stmt = $this->db->prepare("
            DELETE FROM clicks WHERE id = ?
        ");
        $stmt->execute([$id]);
        return $this;
    }
}
