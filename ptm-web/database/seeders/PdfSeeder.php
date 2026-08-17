<?php

namespace Database\Seeders;

use App\Models\Pdf;
use Illuminate\Database\Seeder;

class PdfSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
        [
            'slug' => 'greek-abstraction-hebrew-cochin-james-restoration',
            'filename' => 'from-greek-abstraction-to-hebrew-truth-the-cochin-james-restoration.pdf',
            'path' => 'pdfs/from-greek-abstraction-to-hebrew-truth-the-cochin-james-restoration.pdf',
            'title' => 'From Greek Abstraction to Hebrew Truth',
            'description' => 'For seventeen centuries, the letter written by Yaakov (James), brother of
John (the sons of Zebedee), to the twelve tribes scattered among the
nations has been read through an Alexandrian Greek lens—a lens that
transforms covenantal burden into passive patience, Torah observance
into "law," and emunah into mental assent. But in 1803, Claudius
Buchanan discovered something in the synagogue of the black Jews of
Cochin, India, that changes everything: a Hebrew manuscript of the
New Testament writings, preserved outside the reach of empire, bearing
the linguistic markers of Hebrew and Aramaic alongside Greek textual
tradition.',
            'category' => 'Articles',
            'file_size' => 129182,
            'mime_type' => 'application/pdf',
            'source_url' => null,
            'created_at' => '2026-08-12 22:25:28',
            'updated_at' => '2026-08-13 16:55:39',
            'deleted_at' => null,
        ],
        [
            'slug' => 'trapped-translation-greek-goddess-deity-pistis-faith-charis-grace-elpis-hope',
            'filename' => 'trapped-in-translation.pdf',
            'path' => 'pdfs/trapped-in-translation.pdf',
            'title' => 'Trapped in Translation',
            'description' => 'There is an assault on Scripture that few believers recognize—not because they
haven\'t read their bibles, but because they\'ve read them in translation. The enemy
understood that if he couldn\'t burn the Book, he could redefine its words.',
            'category' => 'Articles',
            'file_size' => 126286,
            'mime_type' => 'application/pdf',
            'source_url' => null,
            'created_at' => '2026-08-12 23:08:01',
            'updated_at' => '2026-08-13 16:56:30',
            'deleted_at' => null,
        ],
        [
            'slug' => 'cochin-hebrew-matthew-Cambridge-Interlinear',
            'filename' => 'matthew-chapter-1.pdf',
            'path' => 'pdfs/matthew-chapter-1.pdf',
            'title' => 'Cochin Hebrew Matthew Chapter 1',
            'description' => 'Cambridge Cochin MS Oo 1.32 Hebrew Matthew Chapter 1',
            'category' => 'Cochin Hebrew Matthew',
            'file_size' => 2422796,
            'mime_type' => 'application/pdf',
            'source_url' => null,
            'created_at' => '2026-08-14 17:34:16',
            'updated_at' => '2026-08-14 23:18:51',
            'deleted_at' => null,
        ],
        [
            'slug' => 'cochin-matthew-Interlinear-Cambridge',
            'filename' => 'cochin-matthew-chapter-2-publication-april-6-2026-changes-in-red.pdf',
            'path' => 'pdfs/cochin-matthew-chapter-2-publication-april-6-2026-changes-in-red.pdf',
            'title' => 'Cochin Hebrew Matthew Chapter 2',
            'description' => 'Cambridge Cochin MS Oo 1.32 Hebrew Matthew Chapter 2',
            'category' => 'Cochin Hebrew Matthew',
            'file_size' => 2577551,
            'mime_type' => 'application/pdf',
            'source_url' => null,
            'created_at' => '2026-08-14 17:36:48',
            'updated_at' => '2026-08-14 23:18:11',
            'deleted_at' => null,
        ],
        [
            'slug' => 'Revelation-Hebrew-Cochin-cambridge-Interlinear',
            'filename' => 'the-scroll-of-mysteries-cambridge-ms-001162-july-4-2026-third-edition-final-academia-version-july-30-2026.pdf',
            'path' => 'pdfs/the-scroll-of-mysteries-cambridge-ms-001162-july-4-2026-third-edition-final-academia-version-july-30-2026.pdf',
            'title' => 'Cochin Hebrew Revelation (Scroll of Mysteries)',
            'description' => 'The Scroll of Mysteries: Cochin Hebrew Revelation presents an English translation, Hebrew
transcription, manuscript images, interlinear tables, and commentary for the Cochin Hebrew Revelation
manuscript preserved in the Cambridge University Library as MS Oo.1.16.2.1 The manuscript belongs to
a broader group of Hebrew New Testament materials associated with the Cochin Jewish community in
India and with the manuscript collection history linked to Claudius Buchanan.',
            'category' => 'Cochin Hebrew Revelation',
            'file_size' => 18917915,
            'mime_type' => 'application/pdf',
            'source_url' => null,
            'created_at' => '2026-08-14 17:47:22',
            'updated_at' => '2026-08-14 23:16:39',
            'deleted_at' => null,
        ],
        [
            'slug' => 'james-Cochin-Hebrew-Cambridge-Interlinear',
            'filename' => 'the-return-letter-of-james-feb-17-2026-updated-new-image-final.pdf',
            'path' => 'pdfs/the-return-letter-of-james-feb-17-2026-updated-new-image-final.pdf',
            'title' => 'Cochin Hebrew James',
            'description' => 'When the disciples were instructed to take the gospel to the world, two disciples, Thomas and
possibly later, Andrew, took Yeshua’s words literal and made their way to Cochin, India. However, the
earliest information available about the Hebrew gospel is linked to the church established by the apostle
Thomas in India. According to Eusebius, the Gospel according to Matthew had been taken to India by
the apostle Bartholomew [Thomas]… written ’in Hebrew script’ and preserved in India until the visit of
Pantaenus… Jerome repeats this testimony of Eusebius, adding that Pantaenus brought the Hebrew
Matthew with him on his return from India to Alexandria.',
            'category' => 'Cochin Hebrew James',
            'file_size' => 12307445,
            'mime_type' => 'application/pdf',
            'source_url' => null,
            'created_at' => '2026-08-14 17:51:36',
            'updated_at' => '2026-08-14 23:17:36',
            'deleted_at' => null,
        ]
        ];

        foreach ($rows as $row) {
            Pdf::updateOrCreate(
                ['slug' => $row['slug']],
                $row
            );
        }
    }
}
