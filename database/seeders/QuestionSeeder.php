<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Question;
use Illuminate\Database\Seeder;

class QuestionSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            'culture-generale' => [
                ['q' => 'Quelle est la capitale de l\'Australie ?', 'options' => ['Sydney', 'Melbourne', 'Canberra', 'Perth'], 'correct' => 2],
                ['q' => 'Combien de continents compte-t-on sur Terre ?', 'options' => ['5', '6', '7', '8'], 'correct' => 2],
                ['q' => 'Quel est le plus long fleuve du monde ?', 'options' => ['Amazone', 'Nil', 'Yangtsé', 'Mississippi'], 'correct' => 1],
                ['q' => 'En quelle année a eu lieu la chute du mur de Berlin ?', 'options' => ['1987', '1989', '1991', '1993'], 'correct' => 1],
                ['q' => 'Quelle langue est la plus parlée au monde (locuteurs natifs) ?', 'options' => ['Anglais', 'Espagnol', 'Mandarin', 'Hindi'], 'correct' => 2],
            ],
            'sport' => [
                ['q' => 'Combien de joueurs compte une équipe de football sur le terrain ?', 'options' => ['9', '10', '11', '12'], 'correct' => 2],
                ['q' => 'Quel pays a remporté la Coupe du Monde 2022 ?', 'options' => ['France', 'Brésil', 'Croatie', 'Argentine'], 'correct' => 3],
                ['q' => 'Dans quel sport utilise-t-on le terme "smash" ?', 'options' => ['Tennis', 'Football', 'Rugby', 'Golf'], 'correct' => 0],
                ['q' => 'Combien de titres de champion d\'Afrique des Nations le Cameroun a-t-il remportés ?', 'options' => ['3', '4', '5', '6'], 'correct' => 2],
                ['q' => 'Tous les combien d\'années ont lieu les Jeux Olympiques d\'été ?', 'options' => ['2 ans', '3 ans', '4 ans', '5 ans'], 'correct' => 2],
            ],
            'cinema-series' => [
                ['q' => 'Qui a réalisé la trilogie "Le Seigneur des Anneaux" ?', 'options' => ['Peter Jackson', 'James Cameron', 'Christopher Nolan', 'Steven Spielberg'], 'correct' => 0],
                ['q' => 'Dans quel film trouve-t-on le personnage de Jack Sparrow ?', 'options' => ['Titanic', 'Pirates des Caraïbes', 'Avatar', 'Gladiator'], 'correct' => 1],
                ['q' => 'Quelle série met en scène la famille Targaryen ?', 'options' => ['The Witcher', 'Vikings', 'Game of Thrones', 'The Crown'], 'correct' => 2],
                ['q' => 'Quel studio a produit "Le Roi Lion" (1994) ?', 'options' => ['Pixar', 'DreamWorks', 'Disney', 'Universal'], 'correct' => 2],
                ['q' => 'Qui interprète Tony Stark / Iron Man au cinéma ?', 'options' => ['Chris Evans', 'Robert Downey Jr.', 'Chris Hemsworth', 'Mark Ruffalo'], 'correct' => 1],
            ],
            'afrique-cameroun' => [
                ['q' => 'Quelle est la capitale politique du Cameroun ?', 'options' => ['Douala', 'Yaoundé', 'Bafoussam', 'Garoua'], 'correct' => 1],
                ['q' => 'Quel est le plus haut sommet d\'Afrique de l\'Ouest/Centrale, situé au Cameroun ?', 'options' => ['Mont Cameroun', 'Mont Kenya', 'Kilimandjaro', 'Mont Oku'], 'correct' => 0],
                ['q' => 'En quelle année le Cameroun a-t-il obtenu son indépendance ?', 'options' => ['1958', '1960', '1962', '1965'], 'correct' => 1],
                ['q' => 'Combien de langues officielles compte le Cameroun ?', 'options' => ['1', '2', '3', '4'], 'correct' => 1],
                ['q' => 'Quel est le pays le plus peuplé d\'Afrique ?', 'options' => ['Éthiopie', 'Égypte', 'Nigeria', 'RD Congo'], 'correct' => 2],
            ],
            'sciences' => [
                ['q' => 'Quelle est la formule chimique de l\'eau ?', 'options' => ['CO2', 'H2O', 'O2', 'NaCl'], 'correct' => 1],
                ['q' => 'Quelle planète est surnommée la "planète rouge" ?', 'options' => ['Vénus', 'Jupiter', 'Mars', 'Saturne'], 'correct' => 2],
                ['q' => 'Qui a formulé la théorie de la relativité ?', 'options' => ['Isaac Newton', 'Albert Einstein', 'Niels Bohr', 'Galilée'], 'correct' => 1],
                ['q' => 'Combien d\'os compte le corps humain adulte ?', 'options' => ['186', '206', '226', '246'], 'correct' => 1],
                ['q' => 'Quel gaz les plantes absorbent-elles principalement lors de la photosynthèse ?', 'options' => ['Oxygène', 'Azote', 'Dioxyde de carbone', 'Hydrogène'], 'correct' => 2],
            ],
            'musique' => [
                ['q' => 'Quel artiste camerounais est surnommé le "roi du Makossa" ?', 'options' => ['Petit Pays', 'Manu Dibango', 'X Maleya', 'Locko'], 'correct' => 1],
                ['q' => 'Quel groupe a interprété "Bohemian Rhapsody" ?', 'options' => ['The Beatles', 'Queen', 'Pink Floyd', 'Led Zeppelin'], 'correct' => 1],
                ['q' => 'De quel pays est originaire le genre musical "Afrobeats" ?', 'options' => ['Ghana', 'Nigeria', 'Cameroun', 'Sénégal'], 'correct' => 1],
                ['q' => 'Quel instrument compte généralement 6 cordes ?', 'options' => ['Violon', 'Guitare', 'Piano', 'Batterie'], 'correct' => 1],
                ['q' => 'Qui est surnommée la "Reine de la Pop" ?', 'options' => ['Rihanna', 'Beyoncé', 'Madonna', 'Adele'], 'correct' => 2],
            ],
        ];

        foreach ($data as $slug => $questions) {
            $category = Category::where('slug', $slug)->first();

            if (! $category) {
                continue;
            }

            foreach ($questions as $item) {
                Question::updateOrCreate(
                    [
                        'category_id' => $category->id,
                        'question_text' => $item['q'],
                    ],
                    [
                        'options' => $item['options'],
                        'correct_option' => $item['correct'],
                        'difficulty' => 'moyen',
                    ]
                );
            }
        }
    }
}
