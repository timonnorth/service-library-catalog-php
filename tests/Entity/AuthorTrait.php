<?php

declare(strict_types=1);

namespace Tests\Entity;

use LibraryCatalog\Entity\Author;

trait AuthorTrait
{
    /**
     * @param string $id
     * @return Author
     */
    protected function createAuthor1($id = '1'): Author
    {
        $author = Author::create(
            "Charles Dickens",
            "1812-02-07",
            "1870-07-09",
            "Born in Portsmouth, Dickens left school to work in a factory when his father was incarcerated in a debtors' prison. Despite his lack of formal education, he edited a weekly journal for 20 years, wrote 15 novels, five novellas, hundreds of short stories and non-fiction articles, lectured and performed readings extensively, was an indefatigable letter writer, and campaigned vigorously for children's rights, education, and other social reforms.",
            "Charles John Huffam Dickens FRSA (/ˈdɪkɪnz/; 7 February 1812 – 9 June 1870) was an English writer and social critic. He created some of the world's best-known fictional characters and is regarded by many as the greatest novelist of the Victorian era.[1] His works enjoyed unprecedented popularity during his lifetime, and by the 20th century, critics and scholars had recognised him as a literary genius. His novels and short stories are still widely read today.[2][3]",
        );
        // For SQLITE we should set ID, in Mysql it'll not be necessary.
        $author->id = $id;
        return $author;
    }
}
