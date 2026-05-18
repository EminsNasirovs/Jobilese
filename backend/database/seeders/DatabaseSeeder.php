<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\Comments;
use App\Models\Favorite;
use App\Models\CvDetail;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::updateOrCreate(
            ['username' => 'admin'],
            [
                'firstname'       => 'Admin',
                'lastname'        => 'User',
                'email'           => 'admin@jobilese.lv',
                'password'        => Hash::make('Password123!'),
                'role'            => 'admin',
                'gender'          => 'vīrietis',
                'birth_date'      => '1990-01-01',
                'company_name'    => null,
                'company_number'  => null,
                'company_address' => null,
            ]
        );

        $employer = User::updateOrCreate(
            ['username' => 'uznemejs_demo'],
            [
                'firstname'       => 'Jānis',
                'lastname'        => 'Bērziņš',
                'email'           => 'janis.berzins@sia-tehnologijas.lv',
                'password'        => Hash::make('Password123!'),
                'role'            => 'uzņēmējs',
                'gender'          => 'vīrietis',
                'birth_date'      => '1985-03-12',
                'company_name'    => 'SIA "Tehnoloģijas"',
                'company_number'  => '40001234567',
                'company_address' => 'Rīga, Brīvības iela 45',
            ]
        );

        $seeker = User::updateOrCreate(
            ['username' => 'anna_liepa'],
            [
                'firstname'       => 'Anna',
                'lastname'        => 'Liepa',
                'email'           => 'anna.liepa@gmail.com',
                'password'        => Hash::make('Password123!'),
                'role'            => 'bezdarbnieks',
                'gender'          => 'sieviete',
                'birth_date'      => '2000-05-22',
            ]
        );

        CvDetail::updateOrCreate(
            ['user_id' => $seeker->id],
            [
                'summary'    => 'Motivēta programmētāja ar 3 gadu pieredzi tīmekļa lietotņu izstrādē. Specializējos Vue.js un Laravel tehnoloģijās.',
                'experience' => [
                    ['company' => 'SIA Digital', 'role' => 'Jaunākā izstrādātāja', 'years' => '2022-2024', 'desc' => 'Vue.js lietotņu izstrāde.'],
                    ['company' => 'SIA WebLab',  'role' => 'Prakstikante',         'years' => '2021-2022', 'desc' => 'Laravel backend atbalsts.'],
                ],
                'education'  => [
                    ['school' => 'Rīgas Valsts tehnikums', 'degree' => 'Programmēšanas tehniķis', 'year' => '2022'],
                ],
                'skills'     => ['Vue.js', 'Laravel', 'PHP', 'JavaScript', 'MySQL', 'Git', 'TailwindCSS'],
            ]
        );

        $vacancies = [
            [
                'title'       => 'Vue.js izstrādātājs',
                'salary'      => '2500',
                'salary_type' => 'Brutto',
                'category'    => 'IT & Programmēšana',
                'county'      => 'Rīga',
                'description' => "Meklējam motivētu Vue.js izstrādātāju mūsu jaunā projekta komandai. Prasības: pieredze ar Vue 3, Vite, TailwindCSS. Piedāvājam konkurētspējīgu atalgojumu, elastīgu darba laiku un iespēju strādāt attālināti.",
            ],
            [
                'title'       => 'Grāmatvedības speciālists',
                'salary'      => '1800',
                'salary_type' => 'Brutto',
                'category'    => 'Grāmatvedība',
                'county'      => 'Rīga',
                'description' => "Uzņēmums meklē pieredzējušu grāmatvedi. Pienākumi: ikmēneša bilanču sagatavošana, VID atskaites, darba samaksas aprēķini. Nepieciešama vismaz 3 gadu pieredze grāmatvedības darbā.",
            ],
            [
                'title'       => 'Mārketinga asistents',
                'salary'      => '1200',
                'salary_type' => 'Brutto',
                'category'    => 'Mārketings',
                'county'      => 'Jūrmala',
                'description' => "Dinamisks mārketinga asistenta amats. Darbs ar sociālajiem tīkliem, satura veidošana, kampaņu plānošana. Lieliska iespēja jaunajiem profesionāļiem.",
            ],
            [
                'title'       => 'Laravel backend izstrādātājs',
                'salary'      => '2800',
                'salary_type' => 'Brutto',
                'category'    => 'IT & Programmēšana',
                'county'      => 'Rīga',
                'description' => "Pievienojies mūsu komandai kā Laravel izstrādātājs! Strādāsi ar Laravel 11, MySQL, REST API un microservisiem. Prasības: 2+ gadu pieredze ar Laravel un PHP 8.",
            ],
            [
                'title'       => 'UI/UX dizainers',
                'salary'      => '2200',
                'salary_type' => 'Brutto',
                'category'    => 'Dizains',
                'county'      => 'Rīga',
                'description' => "Meklējam radošu UI/UX dizaineru. Darbs ar Figma, prototipēšana, lietotāju pētījumi. Piedāvājam radošu vidi un mūsdienīgu biroju Rīgas centrā.",
            ],
            [
                'title'       => 'Pārdošanas menedžeris',
                'salary'      => '1500',
                'salary_type' => 'Brutto',
                'category'    => 'Pārdošana',
                'county'      => 'Liepāja',
                'description' => "B2B pārdošanas menedžeris ar iespēju attīstīt klientu portfeli. Bāzes alga + bonusi atkarībā no rezultātiem. Nepieciešama auto vadītāja apliecība.",
            ],
            [
                'title'       => 'Klientu apkalpošanas speciālists',
                'salary'      => '1100',
                'salary_type' => 'Brutto',
                'category'    => 'Klientu serviss',
                'county'      => 'Daugavpils',
                'description' => "Klientu konsultēšana telefoniski un e-pastā. Maiņu darbs. Mācām un palīdzam adaptēties. Lieliska iespēja sākt karjeru pakalpojumu jomā.",
            ],
            [
                'title'       => 'Loģistikas koordinators',
                'salary'      => '1600',
                'salary_type' => 'Brutto',
                'category'    => 'Loģistika',
                'county'      => 'Jelgava',
                'description' => "Kravu plānošana, sadarbība ar pārvadātājiem, dokumentu apstrāde. Pieredze loģistikā - priekšrocība. Stabils darba grafiks 8:00-17:00.",
            ],
            [
                'title'       => 'Projektu vadītājs',
                'salary'      => '2400',
                'salary_type' => 'Brutto',
                'category'    => 'IT & Programmēšana',
                'county'      => 'Rīga',
                'description' => "Vadi IT projektus no idejas līdz piegādei. Agile/Scrum metodes. Sadarbība ar starptautisku komandu. Angļu valodas zināšanas obligātas.",
            ],
            [
                'title'       => 'QA inženieris',
                'salary'      => '2100',
                'salary_type' => 'Brutto',
                'category'    => 'IT & Programmēšana',
                'county'      => 'Ventspils',
                'description' => "Manuālā un automatizētā testēšana. Cypress, Playwright pieredze - priekšrocība. Iespēja strādāt hibrīda režīmā.",
            ],
            [
                'title'       => 'Satura redaktors',
                'salary'      => '1300',
                'salary_type' => 'Brutto',
                'category'    => 'Mārketings',
                'county'      => 'Rīga',
                'description' => "Tekstu rakstīšana, rediģēšana, SEO optimizācija. Lieliska latviešu valodas izjūta obligāta. Strādāsi ar mājaslapām un blogiem.",
            ],
            [
                'title'       => 'Datu analītiķis',
                'salary'      => '2300',
                'salary_type' => 'Brutto',
                'category'    => 'IT & Programmēšana',
                'county'      => 'Rīga',
                'description' => "SQL, Python, Power BI. Biznesa datu analīze un atskaišu sagatavošana vadībai. Vidējās/augstākās matemātikas zināšanas vēlamas.",
            ],
        ];

        $created = [];
        foreach ($vacancies as $v) {
            $logo = $this->generateLogo($v['title'], $v['category']);

            $vacancy = JobVacancy::updateOrCreate(
                ['title' => $v['title'], 'user_id' => $employer->id],
                [
                    'company'     => 'SIA "Tehnoloģijas"',
                    'salary'      => $v['salary'],
                    'salary_type' => $v['salary_type'],
                    'description' => $v['description'],
                    'category'    => $v['category'],
                    'county'      => $v['county'],
                    'logo'        => $logo,
                ]
            );
            $created[] = $vacancy;
        }

        $v1 = $created[0];
        $v2 = $created[1];

        JobApplication::updateOrCreate(
            ['user_id' => $seeker->id, 'vacancy_id' => $v1->id],
            [
                'cover_letter' => 'Labdien! Mani ļoti ieinteresē Jūsu piedāvātā vakance. Esmu strādājusi ar Vue 3 pēdējos divus gadus un vēlētos pievienoties Jūsu komandai.',
                'cv_path'      => null,
                'status'       => 'pending',
            ]
        );

        $parent = Comments::updateOrCreate(
            ['user_id' => $seeker->id, 'vacancy_id' => $v1->id, 'parent_id' => null],
            ['comment_text' => 'Vai ir iespējams strādāt attālināti pilnu slodzi?']
        );

        Comments::updateOrCreate(
            ['user_id' => $employer->id, 'vacancy_id' => $v1->id, 'parent_id' => $parent->id],
            ['comment_text' => 'Jā, hibrīda vai pilnībā attālināta darba iespēja ir pieejama.']
        );

        Favorite::updateOrCreate(
            ['user_id' => $seeker->id, 'job_vacancy_id' => $v2->id],
            []
        );
    }

    /**
     * Generate an SVG logo per vacancy: colored circle with the title initial.
     * Color is derived deterministically from the category so similar jobs look related.
     */
    private function generateLogo(string $title, string $category): string
    {
        $initial = mb_strtoupper(mb_substr(trim($title), 0, 1), 'UTF-8');

        $palette = [
            'IT & Programmēšana' => ['#1E3A8A', '#3B82F6'],
            'Grāmatvedība'       => ['#065F46', '#10B981'],
            'Mārketings'         => ['#9D174D', '#EC4899'],
            'Dizains'            => ['#5B21B6', '#8B5CF6'],
            'Pārdošana'          => ['#9A3412', '#F97316'],
            'Klientu serviss'    => ['#0E7490', '#06B6D4'],
            'Loģistika'          => ['#374151', '#6B7280'],
        ];
        [$bg, $accent] = $palette[$category] ?? ['#111111', '#3B82F6'];

        $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 120 120" width="120" height="120">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="{$bg}"/>
      <stop offset="100%" stop-color="{$accent}"/>
    </linearGradient>
  </defs>
  <rect width="120" height="120" fill="url(#g)"/>
  <circle cx="60" cy="60" r="42" fill="rgba(255,255,255,0.08)"/>
  <text x="60" y="76" font-family="Inter, Helvetica, Arial, sans-serif" font-size="56" font-weight="700" text-anchor="middle" fill="#FFFFFF">{$initial}</text>
</svg>
SVG;

        $filename = 'vacancy_logos/seed_' . Str::slug($title, '_') . '.svg';
        Storage::disk('public')->put($filename, $svg);

        return $filename;
    }
}
