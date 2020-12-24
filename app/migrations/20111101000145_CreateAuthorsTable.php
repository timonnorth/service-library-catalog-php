<?php

use Phpmig\Migration\Migration;

class CreateAuthorsTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $container = $this->getContainer();
        $container['db']->query($this->sqlUp());
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $sql = "DROP TABLE `authors`";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    /**
     * @return string
     */
    public function sqlUp()
    {
        return "
            CREATE TABLE IF NOT EXISTS `authors` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `birthdate` date NOT NULL,
                `deathdate` date,
                `biography` TEXT COLLATE utf8_unicode_ci,
                `summary` TEXT COLLATE utf8_unicode_ci,
                PRIMARY KEY (`id`),
                FULLTEXT(`name`),
                UNIQUE KEY `author_name_birthdate` (`name`, `birthdate`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    }

    /**
     * @return string
     */
    public function sqlUpSqlite()
    {
        return "
            CREATE TABLE IF NOT EXISTS `authors` (
                `id` int(11) NOT NULL,
                `name` varchar(255) NOT NULL,
                `birthdate` date NOT NULL,
                `deathdate` date,
                `biography` TEXT,
                `summary` TEXT,
                PRIMARY KEY (`id`)
            );";
    }

    /**
     * @return string
     */
    public function sqlDownSqlite()
    {
        return "DROP TABLE `authors`";
    }
}

