shrtnr
=====

Your super simple and fast private goo·gl/bit·ly system, privately hosted wherever you want, completely frontend agnostic.

## Installation

- `composer require mattmezza/shrtnr`
- define the env variables needed and set up the DB (check below)

## Usage

The `Shrtnr` class offers a very simple API:

- `shrtn(string $from, string $ip = null) : string` -> Given a URI gives back the destination URL of the rule matching with the `from` field (or throws a `NoRuleException` if no rule is associated with the passed URI)

Can be used like this (in this example we forward the query string to the destination URL):

```php
$both = explode("?", $_SERVER["REQUEST_URI"]);
$uri = $both[0];
$shrtnr = new Shrtnr();
try {
    $to = $shrtnr->shrtn($uri);
    if (count($both) > 1) {
        $to .= "?" . $_SERVER["QUERY_STRING"];
    }
    header("Location: $to");
} catch (NoRuleException $e) {
    die($e->getMessage());
}

```

## Admin usage

To add, edit, remove and search for clicks and rules you can check out the DAOs `Clicks` and `Rules` that are exposing a couple of methods for CRUD ops.

## DB

`shrtnr` connects to the DB using three ENV variables `DB` which is the DSN (e.g. `mysql:host=127.0.0.1;dbname=shrtnr`), `DB_USER` and `DB_PASS` respectively for the db user and password.

The DB itself should be built as follows:

```sql
CREATE TABLE IF NOT EXISTS `clicks` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `rule_id` int(11) unsigned NOT NULL,
        `clicked_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `from` varchar(256) NOT NULL DEFAULT '',
        `to` varchar(256) NOT NULL DEFAULT '',
        `ip` varchar(16) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `rule_id` (`rule_id`),
        CONSTRAINT `clicks_ibfk_1` FOREIGN KEY (`rule_id`) REFERENCES `rules` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
CREATE TABLE IF NOT EXISTS `rules` (
        `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `user_id` varchar(256) DEFAULT NULL,
        `from` varchar(256) NOT NULL,
        `to` varchar(256) NOT NULL,
        `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `enabled` tinyint(1) NOT NULL DEFAULT '1',
        `modified_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `from` (`from`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8
```

## Entities

`shrtnr` works with two main entities: `Rule` and `Click`.

### Rule

It is the rule that instructs the system what to do for a specific URI. It has info about the matching URI and the destination URL. It also has a field `user_id` that should report an id of the user who created the rule (can be either a string or a numeric id).

### Click

It represents an applied rule, it is created when somebody goes to the URI and gets redirected to the matching destination. It reports info about the IP address and references the applied rule.

## Development

- `git clone https://github.com/mattmezza/shrtnr.git`
- `cd shrtnr`
- define the `DB`, `DB_USER`, `DB_PASS` env variable
- `composer migrate`
- `composer test`


##### Matteo Merola <mattmezza@gmail.com>
