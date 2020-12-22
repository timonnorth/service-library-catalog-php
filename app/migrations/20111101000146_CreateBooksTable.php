<?php

use Phpmig\Migration\Migration;

class CreateBooksTable extends Migration
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
        $sql = "DELETE TABLE `books`";
        $container = $this->getContainer();
        $container['db']->query($sql);
    }

    /**
     * @return string
     */
    public function sqlUp()
    {
        return "
            CREATE TABLE IF NOT EXISTS `books` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `title` varchar(4096) COLLATE utf8_unicode_ci NOT NULL,
                `summary` TEXT COLLATE utf8_unicode_ci,
                `authorId` int(11) NOT NULL,
                PRIMARY KEY (`id`),
                FULLTEXT(`title`),
                CONSTRAINT `book_author` FOREIGN KEY (`authorId`) REFERENCES `authors` (`id`) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
    }

    /**
     * @return string
     */
    public function sqlSqlite()
    {
        return "
            CREATE TABLE IF NOT EXISTS `books` (
                `id` int(11) NOT NULL,
                `title` varchar(4096) NOT NULL,
                `summary` TEXT,
                `authorId` int(11) NOT NULL,
                PRIMARY KEY (`id`)
            );";
    }
}

