<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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

        $v1 = JobVacancy::updateOrCreate(
            ['title' => 'Vue.js izstrādātājs', 'user_id' => $employer->id],
            [
                'company'     => 'SIA "Tehnoloģijas"',
                'salary'      => '2500',
                'salary_type' => 'mēnesī',
                'description' => "Meklējam motivētu Vue.js izstrādātāju mūsu jaunā projekta komandai. Prasības: pieredze ar Vue 3, Vite, TailwindCSS. Piedāvājam konkurētspējīgu atalgojumu, elastīgu darba laiku un iespēju strādāt attālināti.",
                'category'    => 'IT & Programmēšana',
                'county'      => 'Rīga',
                'logo'        => null,
            ]
        );

        $v2 = JobVacancy::updateOrCreate(
            ['title' => 'Grāmatvedības speciālists', 'user_id' => $employer->id],
            [
                'company'     => 'SIA "Tehnoloģijas"',
                'salary'      => '1800',
                'salary_type' => 'mēnesī',
                'description' => "Uzņēmums meklē pieredzējušu grāmatvedi. Pienākumi: ikmēneša bilanču sagatavošana, VID atskaites, darba samaksas aprēķini.",
                'category'    => 'Grāmatvedība',
                'county'      => 'Rīga',
                'logo'        => null,
            ]
        );

        $v3 = JobVacancy::updateOrCreate(
            ['title' => 'Mārketinga asistents', 'user_id' => $employer->id],
            [
                'company'     => 'SIA "Tehnoloģijas"',
                'salary'      => '1200',
                'salary_type' => 'mēnesī',
                'description' => "Dinamisks mārketinga asistenta amats. Darbs ar sociālajiem tīkliem, satura veidošana, kampaņu plānošana.",
                'category'    => 'Mārketings',
                'county'      => 'Jūrmala',
                'logo'        => null,
            ]
        );

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
}
