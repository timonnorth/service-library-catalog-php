<?php

use Phpmig\Migration\Migration;

class CreateAuthorsTable extends Migration
{
    /**
     * Do the migration
     */
    public function up()
    {
        $sql = "
            CREATE TABLE IF NOT EXISTS `authors` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `birthdate` date,
                `deathdate` date,
                `biography` TEXT COLLATE utf8_unicode_ci,
                `summary` TEXT COLLATE utf8_unicode_ci,
                PRIMARY KEY (`id`),
                KEY `author_name` (`name`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    /**
     * Undo the migration
     */
    public function down()
    {
        $sql = "DELETE TABLE `authors`";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }
}

