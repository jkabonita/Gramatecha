<?php

namespace Database\Seeders;

use App\Models\Quiz;
use App\Models\QuizQuestion;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ComprehensiveQuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createKomunikasyonQuiz();
        $this->createPasulitPasalitaQuiz();
        $this->createWikaQuiz();
        $this->createWikangFilipinoQuiz();
        $this->createWikangPambansaQuiz();
    }

    /**
     * QUIZ 1: KOMUNIKASYON (Uri ng Komunikasyon)
     */
    private function createKomunikasyonQuiz(): void
    {
        $quiz = Quiz::create([
            'title' => 'Komunikasyon - Uri ng Komunikasyon',
            'slug' => Str::slug('Komunikasyon Uri ng Komunikasyon'),
            'description' => 'YUNIT II: Pagsusulit tungkol sa iba\'t ibang uri ng komunikasyon at non-verbal communication.',
            'time_limit' => 45,
            'passing_score' => 70,
            'is_active' => true,
            'attempts_allowed' => 0,
            'show_correct_answers' => true,
        ]);

        // Section A: Uri ng Komunikasyon (Questions 1-20)
        $sectionA = [
            ['question' => 'Pakikipag-usap sa sarili', 'correct' => 'a'],
            ['question' => 'Dayologo sa pagitan ng guro at mag-aaral', 'correct' => 'b'],
            ['question' => 'Pagbabalita', 'correct' => 'd'],
            ['question' => 'Miting', 'correct' => 'b'],
            ['question' => 'Marketing ng isang produkto', 'correct' => 'c'],
            ['question' => 'Warning tungkol sa masamang efekto ng polusyon', 'correct' => 'd'],
            ['question' => 'Pagbobrodkast sa radyo ng mahalagang anunsyo hinggil sa papalapit na bagyo', 'correct' => 'd'],
            ['question' => 'Pagmumuni-muni', 'correct' => 'a'],
            ['question' => 'Usapan sa pagitan ng anak at ama', 'correct' => 'b'],
            ['question' => 'Usapang pangkapayapaan sa pagitan ng rebelde at gobyerno', 'correct' => 'b'],
            ['question' => 'Propaganda laban sa terorismo', 'correct' => 'd'],
            ['question' => 'Anunsyo tungkol sa pangmundong "Woman Trafficking"', 'correct' => 'c'],
            ['question' => 'Pag-aanalisa ng sariling pagkakamali', 'correct' => 'a'],
            ['question' => 'Usapang parlamentaryo', 'correct' => 'b'],
            ['question' => 'Usapan sa husgado', 'correct' => 'b'],
            ['question' => 'Anunsyo para sa eksamen sa Bar', 'correct' => 'd'],
            ['question' => 'Usapang magkasintahan', 'correct' => 'b'],
            ['question' => 'Pagdarasal ng inang inulila', 'correct' => 'a'],
            ['question' => '"United Nation\'s Summit"', 'correct' => 'c'],
            ['question' => 'Komprensiya sa Pangmundong Global Warning', 'correct' => 'c'],
        ];

        $options = [
            'a' => 'Intrapersonal na Komunikasyon',
            'b' => 'Interpersonal na Komunikasyon',
            'c' => 'Multikultural na Komunikasyon',
            'd' => 'Pangmadlang Komunikasyon',
        ];

        foreach ($sectionA as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => ($index + 1) . '. ' . $item['question'],
                'options' => $options,
                'correct_answer' => $item['correct'],
                'order' => $index + 1,
            ]);
        }

        // Section B: Non-verbal Communication (Questions 21-30)
        $sectionB = [
            ['question' => 'Distansyang 12-25 feet sa pag-uusap', 'correct' => 'b'],
            ['question' => 'Pagkibit-balikat', 'correct' => 'f'],
            ['question' => 'Pagtapik sa balikat', 'correct' => 'a'],
            ['question' => 'Paggamit ng mikropono', 'correct' => 'g'],
            ['question' => 'Pagdampi ng kamay sa noo ng batang maysakit', 'correct' => 'a'],
            ['question' => 'Nakabibinging sigaw', 'correct' => 'd'],
            ['question' => 'Pagpikit ng mata', 'correct' => 'c'],
            ['question' => 'Stethoscope ng doctor', 'correct' => 'g'],
            ['question' => 'Boses na mataas/galit', 'correct' => 'd'],
            ['question' => 'Pagdating ng late sa usapan', 'correct' => 'e'],
        ];

        $optionsB = [
            'a' => 'Haptics',
            'b' => 'Proxemics',
            'c' => 'Oculesics',
            'd' => 'Vocalics',
            'e' => 'Chronemics',
            'f' => 'Kinesics',
            'g' => 'Objectives',
        ];

        foreach ($sectionB as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (21 + $index) . '. ' . $item['question'],
                'options' => $optionsB,
                'correct_answer' => $item['correct'],
                'order' => 21 + $index,
            ]);
        }

        // Section C: Body Language (Questions 31-50)
        $sectionC = [
            ['question' => 'Pagbibigay ng high five', 'correct' => 'r'],
            ['question' => 'Paglakad nang padabog', 'correct' => 's'],
            ['question' => 'Pagkindat', 'correct' => 't'],
            ['question' => 'Pagtatakip ng ilong', 'correct' => 'm'],
            ['question' => 'Pagtulog habang may nagsasalita', 'correct' => 'k'],
            ['question' => 'Paghalukipkip', 'correct' => 'c'],
            ['question' => 'Pagtatakip ng mukha', 'correct' => 'q'],
            ['question' => 'Pag-akbay sa tao', 'correct' => 'p'],
            ['question' => 'Pagpapalaki ng mata', 'correct' => 'h'],
            ['question' => 'Paghaplos sa likod', 'correct' => 'i'],
            ['question' => 'Pamumula ng mukha', 'correct' => 'j'],
            ['question' => 'Kausap mo subali\'t nakatingin sa malayo', 'correct' => 'o'],
            ['question' => 'Nanlilisik na mata', 'correct' => 'a'],
            ['question' => 'Pag-amba sa kausap', 'correct' => 'b'],
            ['question' => 'Pag-iwan sa taong nagsasalita', 'correct' => 'u'],
            ['question' => 'Nakalagay sa bulsa ng pantalon ang mga kamay habang nagsasalita', 'correct' => 'd'],
            ['question' => 'Pagtaas ng dalawang kamay', 'correct' => 'e'],
            ['question' => 'Pagkusot ng mata', 'correct' => 'g'],
            ['question' => 'Pagkibit-balikat', 'correct' => 'l'],
            ['question' => 'Mabilis na paglakad', 'correct' => 'f'],
        ];

        $optionsC = [
            'a' => 'Galit',
            'b' => 'Maghahanap ng away/pagiging agresibo',
            'c' => 'Pag-di sang-ayon',
            'd' => 'Mayabang, nagyayabang',
            'e' => 'Pagsuko, surrender',
            'f' => 'Pagmamadali',
            'g' => 'Napuwing, bagong gising',
            'h' => 'Pagkagulat',
            'i' => 'Pag-aaruga/pagmamahal',
            'j' => 'Pagkapahiya',
            'k' => 'Di interesado/walang interes',
            'l' => 'Pagwawalang-bahala',
            'm' => 'Mabaho ang naamoy',
            'n' => 'Kapaguran',
            'o' => 'Walang pakialam',
            'p' => 'Pagka-kaibigan; pagkakamabutihan',
            'q' => 'Pagkahiya o nahihiya',
            'r' => 'Pagkatuwa',
            's' => 'Galit o nagmamaktol',
            't' => 'Pag-akit/pang-akit',
            'u' => 'Pambabastos',
        ];

        foreach ($sectionC as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (31 + $index) . '. ' . $item['question'],
                'options' => $optionsC,
                'correct_answer' => $item['correct'],
                'order' => 31 + $index,
            ]);
        }
    }

    /**
     * QUIZ 2: PASULAT AT PASALITA
     */
    private function createPasulitPasalitaQuiz(): void
    {
        $quiz = Quiz::create([
            'title' => 'Pasulat at Pasalita',
            'slug' => Str::slug('Pasulat at Pasalita'),
            'description' => 'YUNIT III: Pagsusulit tungkol sa pasalita at pasulat na komunikasyon.',
            'time_limit' => 30,
            'passing_score' => 70,
            'is_active' => true,
            'attempts_allowed' => 0,
            'show_correct_answers' => true,
        ]);

        // Pasalita Section (Questions 1-15)
        $pasalitaQuestions = [
            ['question' => 'tila may sipon ang ispiker', 'correct' => 'c'],
            ['question' => 'tila kinakapos ng paghinga ang ispiker', 'correct' => 'e'],
            ['question' => 'swayer', 'correct' => 'h'],
            ['question' => '"vibration"', 'correct' => 'l'],
            ['question' => 'pagbigkas ng tamang salita, titik', 'correct' => 'a'],
            ['question' => 'Taas-baba ng tinig', 'correct' => 'p'],
            ['question' => 'tila nababasag, nabibiyak ang boses', 'correct' => 'b'],
            ['question' => 'amplikasyon o pagpapahusay ng tinig', 'correct' => 'i'],
            ['question' => 'mukhang tuod o bangkay sa entablado', 'correct' => 'f'],
            ['question' => 'tikas ng tagapagsalita', 'correct' => 'a'],
            ['question' => 'lumalabas sa ilong ang sinasabi', 'correct' => 'd'],
            ['question' => 'laringhe', 'correct' => 'n'],
            ['question' => 'paringhe', 'correct' => 'o'],
            ['question' => 'kaaya-ayang tinig', 'correct' => 'j'],
            ['question' => 'pagpunta sa magkabilang panig ng entablado, pagtapos ay sa gitna', 'correct' => 'k'],
        ];

        $pasalitaOptions = [
            'a' => 'Artikulasyon',
            'b' => 'Haski',
            'c' => 'Denasal',
            'd' => 'Pailong (nasal)',
            'e' => 'Breathy',
            'f' => 'Statue/istatwa',
            'g' => 'Tila nakulong (trapped) ang tinig sa lalamunan',
            'h' => 'Pagkilos sa dalawang dulo ng entablado, likuran sa unahan at',
            'i' => 'Produksyon o pagpapalabas ng tinig',
            'j' => 'Pleasant voice',
            'k' => 'Pacer',
            'l' => 'Resonasyon',
            'm' => 'Shrill',
            'n' => 'Naglalaman ng voice box',
            'o' => 'Nagdadala ng tunog pataas patungo sa bibig',
            'p' => 'Intonasyon',
            'q' => 'Postura',
        ];

        foreach ($pasalitaQuestions as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => ($index + 1) . '. ' . $item['question'],
                'options' => $pasalitaOptions,
                'correct_answer' => $item['correct'],
                'order' => $index + 1,
            ]);
        }

        // Pasulat Section - Pangatnig (Questions 16-25)
        $pasulatQuestions = [
            ['question' => 'Nagagamot na ngayon ng siyensya ang malulubhang sakit ________ ang salot ng kahirapan ay hindi pa.', 'correct' => 'm'],
            ['question' => 'Kung kailan pa walang kaparis ang itinaas na produksyon ng pagkain,________ milyung-milyon ang namamatay sa gutom.', 'correct' => 'f'],
            ['question' => 'Sa inaabot na pag-unlad ng teknolohiya, hindi pa nadidiskubre ang paraan ________ paano mabuhay ang lahat nang sagana, panatag at may dignidad.', 'correct' => 'k'],
            ['question' => 'Dapat alisin ang utak talangka,________ na magtulungan ay naghihilaban pababa.', 'correct' => 'd'],
            ['question' => '________ sa pagsasapribado, deregulasyon at liberalisasyon, nagkaroon ng maraming suliranin ang bayan.', 'correct' => 'b'],
            ['question' => '________ silang dagang nabubuhay sa estero.', 'correct' => 'e'],
            ['question' => '________ makakamit din ang hinihintay na pabahay dahil ipagkakaloob na rin ang mga titulo.', 'correct' => 'j'],
            ['question' => 'Si Juan dela Cruz ________ Uncle Sam ay pawang sumisimbolo sa kanilang mga bansa.', 'correct' => 'g'],
            ['question' => '________ na mag-isip kailangang kumilos.', 'correct' => 'n'],
            ['question' => '________, halos kalahati o mas higit pa rito ang naghihirap na mamamayan.', 'correct' => 'o'],
        ];

        $pasulatOptions = [
            'a' => 'para',
            'b' => 'dahil',
            'c' => 'subalit',
            'd' => 'sapagkat',
            'e' => 'parang',
            'f' => 'saka',
            'g' => 'at',
            'h' => 'samakatwid',
            'i' => 'datapwa\'t',
            'j' => 'upang',
            'k' => 'kung',
            'l' => 'maging',
            'm' => 'ngunit',
            'n' => 'kaya',
            'o' => 'sa katunayan',
        ];

        foreach ($pasulatQuestions as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (16 + $index) . '. ' . $item['question'],
                'options' => $pasulatOptions,
                'correct_answer' => $item['correct'],
                'order' => 16 + $index,
            ]);
        }
    }

    /**
     * QUIZ 3: WIKA (General Concepts)
     */
    private function createWikaQuiz(): void
    {
        $quiz = Quiz::create([
            'title' => 'Wika - Kalikasan at Katangian',
            'slug' => Str::slug('Wika Kalikasan at Katangian'),
            'description' => 'Pagsusulit tungkol sa kalikasan, katangian, at mga bahagi ng wika.',
            'time_limit' => 50,
            'passing_score' => 70,
            'is_active' => true,
            'attempts_allowed' => 0,
            'show_correct_answers' => true,
        ]);

        // Section I: Matching Type (Questions 1-10)
        $sectionI = [
            ['question' => 'Wika', 'correct' => 'e'],
            ['question' => 'Ponolohiya', 'correct' => 'k'],
            ['question' => 'Punto ng Artikulasyo', 'correct' => 'a'],
            ['question' => 'Sintaksis', 'correct' => 'i'],
            ['question' => 'Nagmo-modify ng Tunog', 'correct' => 'c'],
            ['question' => 'Morpema', 'correct' => 'j'],
            ['question' => 'Pumapalag na Bagay', 'correct' => 'l'],
            ['question' => 'Paraan ng Artikulasyon', 'correct' => 'b'],
            ['question' => 'Makahulugang Tunog', 'correct' => 'f'],
            ['question' => 'Makahulugang Palitan ng Pangungusap', 'correct' => 'h'],
        ];

        $optionsI = [
            'a' => 'Pinaggaganapan ng Ponemang Katinig',
            'b' => 'Paraan ng Pagpapalabas ng Hangin',
            'c' => 'Resonador',
            'd' => 'Baga',
            'e' => 'Instrumento ng Komunikasyon',
            'f' => 'Ponema',
            'g' => 'Morpema',
            'h' => 'Diskurso',
            'i' => 'Pag-aaral ng mga Pangungusap',
            'j' => 'Maliliit na Yunit ng Salita',
            'k' => 'Pag-aaral ng mga Tunog',
            'l' => 'Artikulador',
        ];

        foreach ($sectionI as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => ($index + 1) . '. ' . $item['question'],
                'options' => $optionsI,
                'correct_answer' => $item['correct'],
                'order' => $index + 1,
            ]);
        }

        // Section II: Multiple Choice (Questions 11-20)
        $sectionII = [
            ['question' => 'Ang pag-aaral ng mga makahulugang tunog ng isang wika ay tinatawag na __________', 'options' => ['a' => 'Ponolohiya', 'b' => 'Morpolohiya', 'c' => 'Sintaks', 'd' => 'Balarila'], 'correct' => 'a'],
            ['question' => 'Ayon kay Gleason, ang wika ay __________ upang maging mabisa ang paggamit nito.', 'options' => ['a' => 'pinipili', 'b' => 'isinasaayos', 'c' => 'pinipili o isinasaayos', 'd' => 'pinipili at isinasaayos'], 'correct' => 'd'],
            ['question' => 'Bawat nilalang ay may kanya-kanya at iba-ibang katangian, kakayahan at kaalaman sa paggamit ng wika dahil ang wika ay __________', 'options' => ['a' => 'masistema', 'b' => 'balangkas', 'c' => 'arbitraryo', 'd' => 'nagbabago'], 'correct' => 'c'],
            ['question' => 'Upang manatiling buhay ang isang wika, kinakailangang ito\'y patuloy na __________', 'options' => ['a' => 'sinasaliksik', 'b' => 'isinasaaklat', 'c' => 'ginagamit', 'd' => 'itinuturo'], 'correct' => 'c'],
            ['question' => 'Ang pagkakaiba-iba ng katangian ng mga wika ay bunga ng pagkakaiba-iba ng ___________ ng mga bansa at pangkat.', 'options' => ['a' => 'panitikan', 'b' => 'kultura', 'c' => 'lahi', 'd' => 'edukasyon'], 'correct' => 'b'],
            ['question' => 'Ang pagkawala ng ilang salita o bokabularyo at ang pagdaragdag ng mga bagong salita sa isang wika ay mga patunay na ang wika ay ___________', 'options' => ['a' => 'nakabatay sa kultura', 'b' => 'sinasalitang tunog', 'c' => 'masistemang balangkas', 'd' => 'nagbabago o dinamiko'], 'correct' => 'd'],
            ['question' => 'Ginagamit ng tao ang wika sa pagpapahayag ng damdamin o kaisipan; kung gayon, ang wika ay instrumento ng __________', 'options' => ['a' => 'pagtuturo', 'b' => 'kabuhayan', 'c' => 'komunikasyon', 'd' => 'pag-aaral'], 'correct' => 'c'],
            ['question' => 'Maraming kaalaman ang naisasalin sa ibang lahi at saling-lahi dahil ang wika ay __________ ng kaalaman.', 'options' => ['a' => 'tumutuklas at nagpapaunlad', 'b' => 'nababawasan at nadaragdagan', 'c' => 'nag-iingat at nagpapalaganap', 'd' => 'lumilinang ng malikhaing pag-iisip'], 'correct' => 'c'],
            ['question' => 'Ang paggana ng imahinasyon sa pagbabasa ng mga akdang pampanitikan ay patunay na ang wika ay __________', 'options' => ['a' => 'lumilinang ng malikhaing pag-iisip', 'b' => 'nagbubuklod ng bansa', 'c' => 'instrumento ng komunikasyon', 'd' => 'nakabatay sa kultura'], 'correct' => 'a'],
            ['question' => 'Nang makihamok ang mga Indones laban sa mga Olandes at isigaw nilang Satu Bangsa! Satu Bahasa! Satu Tuna-ir! (Isang Bansa! Isang Wika! Isang Inangbayan!), pinatunayan nila na ang wika ay __________', 'options' => ['a' => 'nagbabago o dinamiko', 'b' => 'lumilinang ng malikhaing pag-iisip', 'c' => 'nagbubuklod ng bansa', 'd' => 'sinasalitang tunog'], 'correct' => 'c'],
        ];

        foreach ($sectionII as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (11 + $index) . '. ' . $item['question'],
                'options' => $item['options'],
                'correct_answer' => $item['correct'],
                'order' => 11 + $index,
            ]);
        }

        // Section III: Teorya ng Wika (Questions 21-25)
        $sectionIII = [
            ['question' => 'bow-bow', 'correct' => 'd'],
            ['question' => 'pooh-pooh', 'correct' => 'e'],
            ['question' => 'yo-he-ho', 'correct' => 'g'],
            ['question' => 'ta-ta', 'correct' => 'a'],
            ['question' => 'tarara-boom-de-ay', 'correct' => 'c'],
        ];

        $optionsIII = [
            'a' => 'Kumpas ng kamay',
            'b' => 'Diyos',
            'c' => 'Sinaunang ritwal',
            'd' => 'Kalikasan',
            'e' => 'Bulalas ng masidhing damdamin',
            'f' => 'Mga bagay-bagay sa paligid',
            'g' => 'Pwersang pisikal',
        ];

        foreach ($sectionIII as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (21 + $index) . '. ' . $item['question'],
                'options' => $optionsIII,
                'correct_answer' => $item['correct'],
                'order' => 21 + $index,
            ]);
        }

        // Section IV: Tungkulin ng Wika (Questions 26-30)
        $sectionIV = [
            ['question' => 'May lalaking lumapit sa iyo at tinanong kung saan matatagpuan ang istasyon ng pulis.', 'correct' => 'f'],
            ['question' => 'Sumagot ka at sinabi mo kung ano ang jeep na sasakyan n\'ya, kung saan s\'ya bababa at kung anong pinakamadaling daan patungo sa istasyon ng pulis.', 'correct' => 'g'],
            ['question' => 'Lumiham si Bernie sa kanyang kaibigang nasa Japan.', 'correct' => 'a'],
            ['question' => 'Inutusan ni Olive ang kanilang katulong na ipaghanda siya at ang mga bisita niya ng maiinom.', 'correct' => 'b'],
            ['question' => 'Binalaan ng PAGASA ang mga mamamayan hinggil sa parating na malakas na bagyo.', 'correct' => 'c'],
        ];

        $optionsIV = [
            'a' => 'Interaksyunal',
            'b' => 'Instrumental',
            'c' => 'Regulatori',
            'd' => 'Personal',
            'e' => 'Imahinatibo',
            'f' => 'Heuristik',
            'g' => 'Impormatib',
        ];

        foreach ($sectionIV as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (26 + $index) . '. ' . $item['question'],
                'options' => $optionsIV,
                'correct_answer' => $item['correct'],
                'order' => 26 + $index,
            ]);
        }

        // Section V: Antas ng Wika (Questions 31-35)
        $sectionV = [
            ['question' => 'Ewan ko. Hindi ko alam ang nangyari.', 'correct' => 'd'],
            ['question' => 'Ang dami n\'yang datung ngayon, pre.', 'correct' => 'e'],
            ['question' => 'Mag-ingat ka, hane.', 'correct' => 'c'],
            ['question' => 'Igalang natin ang karapatan ng bawat isa.', 'correct' => 'a'],
            ['question' => 'Nakakakiliti ang bulong ng hangin.', 'correct' => 'b'],
        ];

        $optionsV = [
            'a' => 'Pambansa',
            'b' => 'Pampanitikan/Panretorika',
            'c' => 'Lalawiganin',
            'e' => 'Kolokyal',
            'f' => 'Balbal',
        ];

        foreach ($sectionV as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (31 + $index) . '. ' . $item['question'],
                'options' => $optionsV,
                'correct_answer' => $item['correct'],
                'order' => 31 + $index,
            ]);
        }

        // Section VI: Proseso ng Balbal (Questions 36-40)
        $sectionVI = [
            ['question' => 'Pare, gigimik ba tayo bukas?', 'correct' => 'd'],
            ['question' => 'Bagong sweldo ngayon si Rikki kaya marami siyang bread.', 'correct' => 'c'],
            ['question' => 'Sinita si Simon ng sikyo dahil wala siyang I.D.', 'correct' => 'b'],
            ['question' => 'Nadyahi si Jun kay Janet dahil sa di niya pagsipot noong Biyernes.', 'correct' => 'a'],
            ['question' => 'Alaws nang pera si Jiofree dahil sa pagsusugal.', 'correct' => 'e'],
        ];

        $optionsVI = [
            'a' => 'Paghango sa mga Salitang Katutubo/Lalawiganin',
            'b' => 'Panghihiram sa Wikang Banyaga',
            'c' => 'Pagbibigay ng Bagong Kahulugan sa Salitang Tagalog',
            'd' => 'Pagpapaikli/Reduksyon',
            'e' => 'Pagbabaligtad/Metatesis',
            'f' => 'Paggamit ng Akronim',
            'g' => 'Pagpapalit ng Pantig',
            'h' => 'Paghahalo ng Wika',
            'i' => 'Pagdaragdag',
            'j' => 'Paggamit ng Bilang',
        ];

        foreach ($sectionVI as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (36 + $index) . '. ' . $item['question'],
                'options' => $optionsVI,
                'correct_answer' => $item['correct'],
                'order' => 36 + $index,
            ]);
        }
    }

    /**
     * QUIZ 4: WIKANG FILIPINO (Wika and Grammar)
     */
    private function createWikangFilipinoQuiz(): void
    {
        $quiz = Quiz::create([
            'title' => 'Wikang Filipino - Wika at Linggwistika',
            'slug' => Str::slug('Wikang Filipino Wika at Linggwistika'),
            'description' => 'YUNIT I: Pagsusulit tungkol sa kasaysayan ng wikang Filipino at linggwistika.',
            'time_limit' => 40,
            'passing_score' => 70,
            'is_active' => true,
            'attempts_allowed' => 0,
            'show_correct_answers' => true,
        ]);

        // Section I: Historical Figures (Questions 1-20)
        $sectionI = [
            ['question' => 'Antonio de Pigafetta', 'correct' => 'e'],
            ['question' => 'wikain/dayalekto', 'correct' => 'c'],
            ['question' => 'Pedro Serrano Laktaw', 'correct' => 'j'],
            ['question' => 'kultura', 'correct' => 'k'],
            ['question' => 'Lope K. Santos', 'correct' => 'g'],
            ['question' => 'Arbitraryo', 'correct' => 'a'],
            ['question' => 'Tagalog', 'correct' => 'm'],
            ['question' => 'Padre Pedro Chirino', 'correct' => 'l'],
            ['question' => 'dinamiko', 'correct' => 'b'],
            ['question' => 'Wikang Filipino', 'correct' => 'n'],
            ['question' => 'Andy Clark', 'correct' => 'd'],
            ['question' => 'Chomsky', 'correct' => 'q'],
            ['question' => 'Derek Bickerton', 'correct' => 'p'],
            ['question' => 'Werner Girt', 'correct' => 'r'],
            ['question' => 'Archibald Hill', 'correct' => 'i'],
            ['question' => 'Henry Gleason', 'correct' => 's'],
            ['question' => 'Charlegmane', 'correct' => 'h'],
            ['question' => 'Sampson et al', 'correct' => 'f'],
            ['question' => 'Zeus Salazar', 'correct' => 'u'],
            ['question' => 'Ludwig Wittgenstein', 'correct' => 't'],
        ];

        $optionsI = [
            'a' => 'may pagkakaiba sa iba, maaaring tinatanggap o kaya\'y taboo sa iba; napagkasunduang gamit ng wika sa isang lipunan',
            'b' => 'kondisyon ng pagiging buhay, lumalago',
            'c' => 'varayti ng wika na ang pagkakabuo ay nakasalig sa heyograpikal',
            'd' => 'ang wika ay kultural artifact',
            'e' => 'historyador, nagtipon ng ilang salitang Bisaya',
            'f' => 'maaaring umusal ng wika nang hndi nagsasaalang-alang ng gramar.',
            'g' => 'Balarila ng Wikang Tagalog',
            'h' => 'Ang pagkatuto ng ibang wika ay pagkakaroon ng pangalawang kaluluwa.',
            'i' => 'Ang wika ay masistemang balangkas ng sinasalitang tunog',
            'j' => 'Diccionario Tagalog-Hispano',
            'k' => 'mga minanang yaman-gawi, tradisyon, paniniwala',
            'l' => 'nagpalitaw ng apat na tinataglay na kalidad/katangian ng Wikang Tagalog mula sa pinakadakilang wika sa daigdig.',
            'm' => 'Batayan ng Wikang Pambansa',
            'n' => 'Pambansang Wika',
            'o' => 'naiimpluwensyahan ng wika ang pag-iisip ng tao',
            'p' => 'fixed ang signal ng mga hayop sa pagtugon sa istimulus',
            'q' => 'may software at hardware na regalo ang tao',
            'r' => 'wika ang pinakaelaboreyt na anyo ng simbolo',
            's' => 'isang obra maestra ang wika',
            't' => 'ang limitasyon sa wika ay limitasyon sa mundo',
            'u' => 'kuhanan-impukan ng isang kultura',
        ];

        foreach ($sectionI as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => ($index + 1) . '. ' . $item['question'],
                'options' => $optionsI,
                'correct_answer' => $item['correct'],
                'order' => $index + 1,
            ]);
        }

        // Section II: Grammar and Linguistics (Questions 21-40)
        $sectionII = [
            ['question' => 'letrang a at o', 'correct' => 'p'],
            ['question' => 'ponema', 'correct' => 'f'],
            ['question' => 'morpema', 'correct' => 'i'],
            ['question' => 'sintaks', 'correct' => 'h'],
            ['question' => 'semantiks', 'correct' => 'n'],
            ['question' => 'morpolohiya', 'correct' => 'e'],
            ['question' => '2001', 'correct' => 'w'],
            ['question' => 'makangalan', 'correct' => 's'],
            ['question' => 'Konotasyon', 'correct' => 'b'],
            ['question' => 'linggwistiks', 'correct' => 'a'],
            ['question' => 'ortograpiya', 'correct' => 'd'],
            ['question' => 'Saligang Batas 1987', 'correct' => 'v'],
            ['question' => 'denotasyon', 'correct' => 'k'],
            ['question' => 'fonolohiya', 'correct' => 'g'],
            ['question' => 'diptonggo', 'correct' => 't'],
            ['question' => 'digrapo', 'correct' => 'q'],
            ['question' => 'klaster', 'correct' => 'r'],
            ['question' => 'laguhan', 'correct' => 'o'],
            ['question' => 'makauri', 'correct' => 'c'],
            ['question' => 'kabilaan', 'correct' => 'u'],
        ];

        $optionsII = [
            'a' => 'agham sa pag-aaral ng wik',
            'b' => 'figurativ o implayd na kahuluga',
            'c' => 'panlapi at pang-ur',
            'd' => 'pag-aaral sa pagbaybay',
            'e' => 'pag-aaral sa pagbuo ng mga salita',
            'f' => 'yunit ng tunog',
            'g' => 'pag-aaral ng tunog',
            'h' => 'pag-aayos ng salita (word-order)',
            'i' => 'yunit ng salita',
            'j' => 'pag-aaral ng mga simbolo',
            'k' => 'literal na kahulugan',
            'l' => 'pag-aaral sa kung paano ginamit ang wika',
            'm' => 'etimolohiya',
            'n' => 'pag-aaral sa kahulugan (meaning) ng salita',
            'o' => 'panlaping nasa unahan, gitna at hulihan ng salita',
            'p' => 'nagpapakita ng pagkakaiba sa sex o kasarian',
            'q' => 'tunog ch (ts); tunog sh (sy)',
            'r' => 'kambal katinig',
            's' => 'panlapi at pangngalan',
            't' => 'pagsasama ng tunog ng isang patinig at malapatinig o kaya\'y magkasunod na patinig',
            'u' => 'panlapi sa unahan at hulihan ng salitang-ugat',
            'v' => 'Konstitusyunal na batayan ng Wikang Pambansa',
            'w' => 'Revisyon sa Alfabeto.',
        ];

        foreach ($sectionII as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (21 + $index) . '. ' . $item['question'],
                'options' => $optionsII,
                'correct_answer' => $item['correct'],
                'order' => 21 + $index,
            ]);
        }
    }

    /**
     * QUIZ 5: WIKANG PAMBANSA - WIKANG AKADEMIKO
     */
    private function createWikangPambansaQuiz(): void
    {
        $quiz = Quiz::create([
            'title' => 'Wikang Pambansa - Wikang Akademiko',
            'slug' => Str::slug('Wikang Pambansa Wikang Akademiko'),
            'description' => 'Pagsusulit tungkol sa kasaysayan ng Wikang Pambansa at mga taong naging bahagi nito.',
            'time_limit' => 45,
            'passing_score' => 70,
            'is_active' => true,
            'attempts_allowed' => 0,
            'show_correct_answers' => true,
        ]);

        // Section I: Historical Figures (Questions 1-15)
        $sectionI = [
            ['question' => 'Ama ng Wikang Pambansa ng Pilipinas, Pangulo ng Pilipinas, nagtagubilin sa Asamblea Nasyonal ng paglikha ng isang SWP.', 'correct' => 'e'],
            ['question' => 'Ama ng Balarilang Filipino, bumalangkas sa Abakada noong 1940.', 'correct' => 'g'],
            ['question' => 'Hinirang na Tagapangulo at Kinatawan ng Visayang Samar sa SWP noong 1937.', 'correct' => 'i'],
            ['question' => 'Kinatawan ng mga Tagalog, Kalihim at Punong Tagapagtanggap ng SWP noong 1937.', 'correct' => 'j'],
            ['question' => 'Kinatawan ng mga Muslim sa SWP noong 1937.', 'correct' => 'k'],
            ['question' => 'Pangulo ng Pilipinas, lumagda sa Proklama Blg. 12 noong 1954.', 'correct' => 'd'],
            ['question' => 'Pangulo ng Pilipinas, lumagda sa Proklama Blg. 186 noong 1954.', 'correct' => 'd'],
            ['question' => '1959 Kalihim ng Kagawaran ng Edukasyon at Kultura, lumagda sa Kautusang Pangkagawaran Blg. 7 noong 1959.', 'correct' => 'l'],
            ['question' => 'Pangulo ng Pilipinas, lumagda sa Proklama Blg. 304 noong 1971.', 'correct' => 'c'],
            ['question' => 'Kinatawan ng mga Tagalog at Tagapangulo ng SWP noong 1971.', 'correct' => 'f'],
            ['question' => 'Kalihim ng Kagawaran ng Edukasyon at Kultura, lumagda sa Kautusang Pangkagawaran Blg. 25 noong 1974.', 'correct' => 'h'],
            ['question' => 'Kalihim ng Kagawaran ng Edukasyon, Kultura at Palakasan, lumagda sa Kautusan Blg. 52 noong 1987.', 'correct' => 'm'],
            ['question' => 'Pangulo ng Pilipinas, lumagda sa Kautusang Tagapagpaganap Blg. 335 noong 1988.', 'correct' => 'b'],
            ['question' => 'Kalihim ng Kagawaran ng Edukasyon, Kultura at Palakasan, lumagda sa Kautusang Pangkagawaran Blg. 21 noong 1990.', 'correct' => 'n'],
            ['question' => 'Pangulo ng Pilipinas, lumagda sa Proklama Blg. 1041 noong 1997.', 'correct' => 'a'],
        ];

        $optionsI = [
            'a' => 'Fidel V. Ramos',
            'b' => 'Corazon C. Aquino',
            'c' => 'Ferdinand E. Marcos',
            'd' => 'Ramon Magsaysay',
            'e' => 'Manuel L. Quezon',
            'f' => 'Ponciano B.P. Pineda',
            'g' => 'Lope K. Santos',
            'h' => 'Juan L. Manuel',
            'i' => 'Jaime D. de Veyra',
            'j' => 'Cecilio Lopez',
            'k' => 'Hadji Butu',
            'l' => 'Jose E. Romero',
            'm' => 'Lourdes R. Quisumbing',
            'n' => 'Isidro Cariño',
            'o' => 'Rolando A. Bernales',
        ];

        foreach ($sectionI as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => ($index + 1) . '. ' . $item['question'],
                'options' => $optionsI,
                'correct_answer' => $item['correct'],
                'order' => $index + 1,
            ]);
        }

        // Section II: Multiple Choice History (Questions 16-40)
        $sectionII = [
            ['question' => 'Nagkaroon ng unang hakbangin upang ang Pilipinas ay magkaroon ng wikang Pambansa noong ________.', 'options' => ['a' => '1935', 'b' => '1945', 'c' => '1955', 'd' => '1987'], 'correct' => 'a'],
            ['question' => 'Ang Kahulugan ng SWP ay ________', 'options' => ['a' => 'Sambayanan ng Wikang Pambansa', 'b' => 'Sanggunian ng Wikang Pambansa', 'c' => 'Samahan ng Wikang Pambansa', 'd' => 'Surian ng Wikang Pambansa'], 'correct' => 'd'],
            ['question' => 'Noong 1987, iminungkahi ng SWP sa kanilang resolusyon na ang Wikang Pambansa ay ibatay sa ________.', 'options' => ['a' => 'Tagalog', 'b' => 'Iloco', 'c' => 'Cebuano', 'd' => 'Waray'], 'correct' => 'a'],
            ['question' => 'Ang mga opisyal na wika ng Pilipinas noong 1940 ay ________.', 'options' => ['a' => 'Ingles, Kastila at Wikang Pambansa', 'b' => 'Ingles, Kastila at Nihonggo', 'c' => 'Ingles, Kastila at Tagalog', 'd' => 'Ingles, Tagalog at Iloco'], 'correct' => 'a'],
            ['question' => 'Sinimulang ituro ang wikang Pambansa sa mga paaralan noong ________.', 'options' => ['a' => '1935', 'b' => '1937', 'c' => '1940', 'd' => '1941'], 'correct' => 'c'],
            ['question' => 'Noong 1959, nilinaw ng Kagawaran ng Edukasyon na kailan ma\'y tutukuyin ang Wikang Pambansa, ito ay tatawaging ________.', 'options' => ['a' => 'Tagalog', 'b' => 'Pilipino', 'c' => 'Filipino', 'd' => 'Pilifino'], 'correct' => 'b'],
            ['question' => 'Ayon sa Bagong Saligang Batas (1987), ang Wikang Pambansa ng Pilipinas ay tatawaging ________.', 'options' => ['a' => 'Filipino', 'b' => 'Filifino', 'c' => 'Pilipino', 'd' => 'Pilifino'], 'correct' => 'a'],
            ['question' => 'Samantalang nililinang, ang wikang Pambansa ng Pilipinas ay dapat payabungin at pagyamanin pa salig sa ________.', 'options' => ['a' => 'umiiral na wika sa Pilipinas at iba pang mga wika', 'b' => 'mga wikang katutubo sa mga lalawigan', 'c' => 'mga pangunahing wikang dayuhan', 'd' => 'Ingles at Tagalog'], 'correct' => 'a'],
            ['question' => 'Ang patakarang sinimulang ipatupad sa mga paaralan noong 1974 ay tinatawag na ________.', 'options' => ['a' => 'edukasyong multilinggwal', 'b' => 'edukasyong monolinggwal', 'c' => 'edukasyong trilinggwal', 'd' => 'edukasyong bilinggwal'], 'correct' => 'd'],
            ['question' => 'Ang alpabeto ng ating mga ninuno noong panahong pre-kolonyal ay tinawag na ________.', 'options' => ['a' => 'alibata', 'b' => 'alibaba', 'c' => 'talibaba', 'd' => 'alibangbang'], 'correct' => 'a'],
        ];

        foreach ($sectionII as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (16 + $index) . '. ' . $item['question'],
                'options' => $item['options'],
                'correct_answer' => $item['correct'],
                'order' => 16 + $index,
            ]);
        }

        // Continue with more questions (26-40)
        $sectionIII = [
            ['question' => 'Tinuruan ng mga Kastila ang mga Pilipino ng pagsulat sa alpabetong ________.', 'options' => ['a' => 'Espanyol', 'b' => 'Romano', 'c' => 'Griyego', 'd' => 'Arabik'], 'correct' => 'b'],
            ['question' => 'Nakilala ang alpabeto noong panahon ng Kastila sa tawag na ________.', 'options' => ['a' => 'Abecede', 'b' => 'Abecedario', 'c' => 'Ortograpiya', 'd' => 'Letradario'], 'correct' => 'b'],
            ['question' => 'Ang alpabetong binalangkas noong 1940 ay nakilala sa tawag na ________.', 'options' => ['a' => 'Ey Bi Si', 'b' => 'Abakada', 'c' => 'Alpabeto', 'd' => 'Alfabeto'], 'correct' => 'b'],
            ['question' => 'Ang alpabetong binalangkas noong 1940 ay binubuo ng ________.', 'options' => ['a' => '20 titik', 'b' => '25 titik', 'c' => '28 titik', 'd' => '31 titik'], 'correct' => 'a'],
            ['question' => 'Ang Bagon Alpabetong Filipino ay binubuo ng ________.', 'options' => ['a' => '25 titik', 'b' => '26 titik', 'c' => '28 titik', 'd' => '31 titik'], 'correct' => 'c'],
            ['question' => 'Noong 1971, iminungkahing dagdagan ang alpabeto ng ________.', 'options' => ['a' => '5 titik', 'b' => '7 titik', 'c' => '8 titik', 'd' => '11 titik'], 'correct' => 'd'],
            ['question' => 'Ang mga titik sa Bagong Alpabetong Filipino ay tinatawag nang pa-Ingles maliban sa ________.', 'options' => ['a' => 'ñ', 'b' => 'ng', 'c' => 'q', 'd' => 'z'], 'correct' => 'a'],
            ['question' => 'Ang ch, ll, ng at rr ay tinatawag na ________.', 'options' => ['a' => 'digrapo', 'b' => 'diagrapo', 'c' => 'bigrapo', 'd' => 'bigrapi'], 'correct' => 'a'],
            ['question' => 'Sa kasalukuyan, ang wikang opisyal ng Pilipinas ay ________.', 'options' => ['a' => 'Ingles at Pilipino', 'b' => 'Ingles at Filipino', 'c' => 'Filipino at Tagalog', 'd' => 'Ingles, Kastila at Filipino'], 'correct' => 'b'],
            ['question' => 'Tuwing Agosto, ipinagdiriwang ang ________.', 'options' => ['a' => 'Araw ni Balagtas', 'b' => 'Buwan ni Quezon', 'c' => 'Linggo ng Wika', 'd' => 'Buwan ng Wikang Filipino'], 'correct' => 'd'],
        ];

        foreach ($sectionIII as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (26 + $index) . '. ' . $item['question'],
                'options' => $item['options'],
                'correct_answer' => $item['correct'],
                'order' => 26 + $index,
            ]);
        }

        // Final questions (36-40)
        $sectionIV = [
            ['question' => 'Noong 1954, nakapaloob sa pagdiriwang ng Linggo ng Wika ang ________.', 'options' => ['a' => 'Araw ni Balagtas', 'b' => 'Araw ni Quezon', 'c' => 'Araw ni Rizal', 'd' => 'Araw ng SWP'], 'correct' => 'a'],
            ['question' => 'Ang umakda ng Sobre la Nueva Ortografiya de la Lengua Tagala ay si ________.', 'options' => ['a' => 'Graciano Lopex Jaena', 'b' => 'Trinidad H. Pardo de Tavera', 'c' => 'Fr. Pedro Chirino', 'd' => 'Jose P. Rizal'], 'correct' => 'd'],
            ['question' => 'Ayon kay Rizal, ang Pilipinisasyon ng ating ortograpiya ay utang natin kay ________.', 'options' => ['a' => 'Fr. Pedro Chirino', 'b' => 'Ferdinand Blumentritt', 'c' => 'Trinidad H. Pardo de Tavera', 'd' => 'Lope K. Santos'], 'correct' => 'c'],
            ['question' => 'Ang teorya hinggil sa paraan ng pagsulat ng mga sinaunang Pilipino ay binuo ni ________.', 'options' => ['a' => 'Trinidad H. Pardo de Tavera', 'b' => 'Ferdinand Blumentritt', 'c' => 'Fr. Pedro Chirino', 'd' => 'Richard Pittman'], 'correct' => 'a'],
            ['question' => 'Ang pinakahuling rebisyon at patnubay ng wikang Filipino ay ipinalabas ng Komisyon sa Wikang Filipino noong ________.', 'options' => ['a' => '1987', 'b' => '1990', 'c' => '1995', 'd' => '2001'], 'correct' => 'd'],
        ];

        foreach ($sectionIV as $index => $item) {
            QuizQuestion::create([
                'quiz_id' => $quiz->id,
                'question' => (36 + $index) . '. ' . $item['question'],
                'options' => $item['options'],
                'correct_answer' => $item['correct'],
                'order' => 36 + $index,
            ]);
        }
    }
}
