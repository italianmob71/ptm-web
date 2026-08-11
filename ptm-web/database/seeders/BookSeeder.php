<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Author;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    public function run(): void
    {
        // Get Janice Baca's author ID
        $janice = Author::where('first_name', 'Janice')
            ->where('last_name', 'Baca')
            ->first();

        if (!$janice) {
            $this->command->warn('Janice Baca not found in authors table. Skipping book seeder.');
            return;
        }

        $books = [
            [
                'title' => 'Discover the 144,000: and Their Song',
                'subtitle' => 'A Biblical Exploration of the Sealed Multitude',
                'slug' => 'discover-the-144000-and-their-song',
                'author_id' => $janice->id,
                'body' => '<p>A comprehensive biblical study exploring the identity, mission, and significance of the 144,000 sealed servants of Yehovah described in Revelation 7 and 14. This groundbreaking work examines the Hebrew textual evidence, historical context, and prophetic implications of this mysterious group.</p><p>Drawing from the Cochin Hebrew Revelation manuscript and comparative Semitic textual analysis, Janice F. Baca reveals the linguistic markers that point to the true identity of the 144,000 and their unique role in the end-times narrative.</p>',
                'isbn_13' => '9781234567890',
                'isbn_10' => '1234567890',
                'amazon_link' => 'https://amazon.com/dp/1234567890',
                'lulu_link' => 'https://lulu.com/shop/janice-baca/discover-the-144000',
                'image_front' => 'book-144000-front.jpg',
                'image_back' => 'book-144000-back.jpg',
                'image_inner' => 'book-144000-inner.jpg',
                'edition' => '1st',
                'published_at' => '2022-03-15',
                'published' => true,
                'active' => true,
                'page_count' => 284,
                'language' => 'English',
                'price_usd' => 19.99,
            ],
            [
                'title' => 'Demons, Devils, Deities: And the Four Witnesses',
                'subtitle' => 'Unmasking the Spiritual Powers Behind World Events',
                'slug' => 'demons-devils-deities-and-the-four-witnesses',
                'author_id' => $janice->id,
                'body' => '<p>An exhaustive biblical investigation into the nature, hierarchy, and operations of the spiritual realm as revealed in Scripture. This work systematically identifies the four classes of spiritual beings—demons, devils, deities, and the four witnesses—and their roles in the cosmic conflict.</p><p>Using the Hebrew textual tradition and the Cochin manuscripts, Janice F. Baca provides a taxonomy of spiritual entities that restores the Hebraic understanding of the unseen world, free from Hellenistic philosophical overlays.</p>',
                'isbn_13' => '9781234567891',
                'isbn_10' => '1234567891',
                'amazon_link' => 'https://amazon.com/dp/1234567891',
                'lulu_link' => 'https://lulu.com/shop/janice-baca/demons-devils-deities',
                'image_front' => 'book-demons-front.jpg',
                'image_back' => 'book-demons-back.jpg',
                'image_inner' => 'book-demons-inner.jpg',
                'edition' => '1st',
                'published_at' => '2023-06-20',
                'published' => true,
                'active' => true,
                'page_count' => 356,
                'language' => 'English',
                'price_usd' => 24.99,
            ],
            [
                'title' => 'Power-of-God-Singing: in the Hebrew New Testament',
                'subtitle' => 'Restoring the Lost Song of the Redeemed',
                'slug' => 'power-of-god-singing-in-the-hebrew-new-testament',
                'author_id' => $janice->id,
                'body' => '<p>A revolutionary linguistic and theological study of the "Power of God" (δύναμις θεοῦ / koach Elohim) as it appears in the Hebrew New Testament manuscripts. This work demonstrates how the Cochin Hebrew manuscripts preserve the liturgical and doxological language of the early ekklesia.</p><p>Through comparative analysis of the Cochin Hebrew Revelation, James, and other NT manuscripts, Janice F. Baca recovers the hymnic and worship terminology that was central to first-century Jewish believers in Yeshua.</p>',
                'isbn_13' => '9781234567892',
                'isbn_10' => '1234567892',
                'amazon_link' => 'https://amazon.com/dp/1234567892',
                'lulu_link' => 'https://lulu.com/shop/janice-baca/power-of-god-singing',
                'image_front' => 'book-power-singing-front.jpg',
                'image_back' => 'book-power-singing-back.jpg',
                'image_inner' => 'book-power-singing-inner.jpg',
                'edition' => '1st',
                'published_at' => '2024-01-10',
                'published' => true,
                'active' => true,
                'page_count' => 212,
                'language' => 'English',
                'price_usd' => 18.99,
            ],
            [
                'title' => 'Cleansing the Soul, the House, and the Land for the Final March',
                'subtitle' => 'A Manual for Spiritual and Physical Preparation',
                'slug' => 'cleansing-the-soul-the-house-and-the-land-for-the-final-march',
                'author_id' => $janice->id,
                'body' => '<p>A practical and prophetic manual for the people of Yehovah preparing for the final exodus. This work provides biblical protocols for cleansing—personal (soul), domestic (house), and territorial (land)—based on the Torah patterns of purification, the Levitical priesthood, and the prophetic warnings of the end times.</p><p>Drawing from the Cochin Hebrew manuscripts and the Hebrew roots of the Renewed Covenant, Janice F. Baca presents a step-by-step protocol for spiritual readiness that aligns with the ancient paths of Yehovah\'s people.</p>',
                'isbn_13' => '9781234567893',
                'isbn_10' => '1234567893',
                'amazon_link' => 'https://amazon.com/dp/1234567893',
                'lulu_link' => 'https://lulu.com/shop/janice-baca/cleansing-the-soul',
                'image_front' => 'book-cleansing-front.jpg',
                'image_back' => 'book-cleansing-back.jpg',
                'image_inner' => 'book-cleansing-inner.jpg',
                'edition' => '1st',
                'published_at' => '2025-04-01',
                'published' => true,
                'active' => true,
                'page_count' => 198,
                'language' => 'English',
                'price_usd' => 16.99,
            ],
            [
                'title' => 'The Scroll of Mysteries: Cochin Hebrew Revelation',
                'subtitle' => 'Translation and Commentary on MS Oo.1.16.2',
                'slug' => 'scroll-of-mysteries-cochin-hebrew-revelation',
                'author_id' => $janice->id,
                'body' => '<p>The definitive translation and commentary on the Cochin Hebrew Revelation manuscript (Cambridge MS Oo.1.16.2), also known as "The Scroll of Mysteries." This unique Hebrew Revelation manuscript preserves late Second Temple Hebrew grammar and vocabulary, providing an unprecedented window into the Hebraic Vorlage behind the Greek Apocalypse.</p><p>This volume includes the complete Hebrew text with facing English translation, extensive textual notes comparing the Cochin manuscript to the Greek Textus Receptus, critical apparatus, and a comprehensive introduction to the manuscript\'s history, paleography, and significance for New Testament textual criticism.</p>',
                'isbn_13' => '9781234567894',
                'isbn_10' => '1234567894',
                'amazon_link' => 'https://amazon.com/dp/1234567894',
                'lulu_link' => 'https://lulu.com/shop/janice-baca/scroll-of-mysteries',
                'image_front' => 'book-scroll-mysteries-front.jpg',
                'image_back' => 'book-scroll-mysteries-back.jpg',
                'image_inner' => 'book-scroll-mysteries-inner.jpg',
                'edition' => '1st',
                'published_at' => '2024-11-15',
                'published' => true,
                'active' => true,
                'page_count' => 432,
                'language' => 'English',
                'price_usd' => 34.99,
            ],
            [
                'title' => 'The Return Letter of James: The Cochin Hebrew James',
                'subtitle' => 'Translation and Commentary on the Cochin Hebrew James Manuscript',
                'slug' => 'return-letter-of-james-cochin-hebrew-james',
                'author_id' => $janice->id,
                'body' => '<p>The complete translation and commentary on the Cochin Hebrew James manuscript, presenting the epistle of Ya\'akov (James) in its original Hebrew form. This work demonstrates how the Cochin manuscript preserves a Hebrew Vorlage that predates the Greek Textus Receptus, revealing the Hebraic thought patterns, idioms, and Torah-based theology of the half-brother of Yeshua.</p><p>Includes facing Hebrew-English translation, extensive lexical notes on key terms (emunah, tzedakah, halakha, etc.), comparison with the Peshitta and Greek traditions, and an introduction to the manuscript\'s discovery in the Malabari Synagogue of Cochin, India.</p>',
                'isbn_13' => '9781234567895',
                'isbn_10' => '1234567895',
                'amazon_link' => 'https://amazon.com/dp/1234567895',
                'lulu_link' => 'https://lulu.com/shop/janice-baca/return-letter-of-james',
                'image_front' => 'book-james-front.jpg',
                'image_back' => 'book-james-back.jpg',
                'image_inner' => 'book-james-inner.jpg',
                'edition' => '1st',
                'published_at' => '2023-09-22',
                'published' => true,
                'active' => true,
                'page_count' => 178,
                'language' => 'English',
                'price_usd' => 22.99,
            ],
        ];

        foreach ($books as $book) {
            Book::updateOrCreate(
                ['slug' => $book['slug']],
                $book
            );
        }
    }
}