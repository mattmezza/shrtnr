<?php

use PHPUnit\Framework\TestCase;
use Shrtnr\DAO\Rules;
use Shrtnr\DAO\Clicks;
use Shrtnr\Rule;
use Shrtnr\Shrtnr;
use Shrtnr\Exception\AddRuleException;
use Shrtnr\Exception\NoRuleException;

class ShrtnrTest extends TestCase
{
    private $db;
    private $shrtnr;
    private $rulesMgr;
    private $clicksMgr;

    public function setUp()
    {
        $this->db = getDB();
        $stmt = $this->db->prepare("delete from rules");
        $stmt->execute([]);
        $this->shrtnr = new Shrtnr();
        $this->rulesMgr = new Rules();
        $this->clicksMgr = new Clicks();
    }

    public function testAddRule()
    {
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $stmt = $this->db->prepare("select * from rules where `from` = ?");
        $stmt->execute(["/corsi"]);
        $rows = $stmt->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals("https://www.corsi.it", $rows[0]["to"]);
    }

    public function testAddRuleException()
    {
        $this->expectException(AddRuleException::class);
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $addedRule2 = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it/privacy",
            "matteo"
        );
    }

    public function testNoRule()
    {
        $this->expectException(NoRuleException::class);
        $to = $this->shrtnr->shrtn("/sburun");
    }

    public function testShorten()
    {
        $ip = "192.168.1.1";
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $to = $this->shrtnr->shrtn("/corsi", $ip);
        $this->assertEquals("https://www.corsi.it", $to);
        $stmt = $this->db->prepare("select * from clicks where `from` = ?");
        $stmt->execute(["/corsi"]);
        $rows = $stmt->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals("https://www.corsi.it", $rows[0]["to"]);
        $this->assertEquals($ip, $rows[0]["ip"]);
    }

    public function testRuleOkFirstClick()
    {
        $ip = "192.168.1.1";
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $rule = $this->rulesMgr->getByFromUrl("/corsi");
        $this->clicksMgr->add(
            $rule,
            $ip
        );
        $stmt = $this->db->prepare("select * from clicks where `from` = ?");
        $stmt->execute(["/corsi"]);
        $rows = $stmt->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals("https://www.corsi.it", $rows[0]["to"]);
        $this->assertEquals($ip, $rows[0]["ip"]);
    }

    public function testRuleOkMoreThanOneClick()
    {
        $ip = "192.168.1.1";
        $ip2 = "192.168.1.170";
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $rule = $this->rulesMgr->getByFromUrl("/corsi");
        $this->clicksMgr->add(
            $rule,
            $ip
        );
        $this->clicksMgr->add(
            $rule,
            $ip2
        );
        $stmt = $this->db->prepare("select * from rules where `from` = ?");
        $stmt->execute(["/corsi"]);
        $rows = $stmt->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals("https://www.corsi.it", $rows[0]["to"]);
        $stmt2 = $this->db->prepare("select * from clicks where `from` = ?");
        $stmt2->execute(["/corsi"]);
        $rows2 = $stmt2->fetchAll();
        $this->assertEquals(2, count($rows2));
        $this->assertEquals("https://www.corsi.it", $rows2[0]["to"]);
        $this->assertEquals($ip2, $rows2[1]["ip"]);
    }

    public function testRelink()
    {
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $this->rulesMgr->edit($addedRule->getId(), "/corsi.it", "https://www.corsi.it/");
        $stmt = $this->db->prepare("select * from rules where `id` = ?");
        $stmt->execute([$addedRule->getId()]);
        $rows = $stmt->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals("https://www.corsi.it/", $rows[0]["to"]);
        $this->assertEquals("/corsi.it", $rows[0]["from"]);
    }

    public function testDelete()
    {
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $this->rulesMgr->delete($addedRule->getId(), "/corsi.it", "https://www.corsi.it/");
        $stmt = $this->db->prepare("select * from rules where `id` = ?");
        $stmt->execute([$addedRule->getId()]);
        $rows = $stmt->fetchAll();
        $this->assertEquals(0, count($rows));
    }

    public function testDisable()
    {
        $addedRule = $this->rulesMgr->add(
            "/corsi",
            "https://www.corsi.it",
            "matteo"
        );
        $this->rulesMgr->setEnabled($addedRule->getId(), false);
        $stmt = $this->db->prepare("select * from rules where `id` = ?");
        $stmt->execute([$addedRule->getId()]);
        $rows = $stmt->fetchAll();
        $this->assertEquals(1, count($rows));
        $this->assertEquals("0", $rows[0]["enabled"]);
    }

    public function tearDown() 
    {
        $stmt = $this->db->prepare("delete from rules");
        $stmt->execute([]);
    }
}
