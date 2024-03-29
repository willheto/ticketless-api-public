<?php

namespace App\Providers;

use Faker\Provider\Base;

class KummeliProvider extends Base
{
    protected static array $kummeliSentences = [
        'Terve',
        'Teillä oli täällä säätieteilijän paikka auki',
        'Koko kevään syyssää',
        'Vettä tulee kun esterin perseestä',
        'Ensin talven sää:',
        'Lunta sataa ja kaikkia vituttaa',
        'Huomisen illan ja yön sää',
        'Aurinko laskee ja karmea pimeys peittää maan',
        'Osa menee nukkumaan ja osa menee...',
        'Moi',
        'Pieni kahvi',
        'Tommonen vohveli tosta',
        'Kahdeksan markkaa',
        'Jaa niin',
        'Mulla ei ookkaan kun tämmönen iso seteli',
        'Ja tuhanteen',
        'Mähän annoin sulle tonnin setelin',
        'Enks mä antanu tonnin',
        'Tonni',
        'Kukas toi on',
        'Toi on nyt nimenomaan se panomies',
        'Ai toi on se panomies',
        'Ai moi',
        'Mihis se menee',
        'Panee.',
        'Ai panee?',
        'Sillä jäi valot päälle',
        'Sulla jäi valot päälle',
        'Hei, thänks',
        'Sehän on aivan mielettömän mukava jätkä',
        'Sillä on tossa kakkosessa sellanen panoluukku',
        'Mitä mieltä, onko se tossa autossa...',
        'Ai pannu vai?',
        'On on on',
        'Siisti civicci',
        'Voi veljet',
        'Hei, haluutsä lähtee mun mukaan',
        'Tyhmä jätkä',
        'Lähe nyt',
        'En mä voi lähtee',
        'Asia kunnossa',
        'Jankon betoni, Kalervo Jankko',
        'Rautajoki morjesta',
        'Siellä on kaverit laittanu liikaa kovetetta',
        'Kalle, haista paska',
        'Seppo Numminen kummolahdelta taas terve',
        'Sä oot luvannu ne kaivonrenkaan viikko sitten',
        'Tiiätsä mitä se maksaa',
        'Ja sitten vielä Kalle, yks asia',
        'Katos tässä on tää meidän sopimus kaupungin kanssa',
        'Joo maksan pankkikortilla sitte',
        'Haluuks sä välttämättä tän Venäläisen meetvurstin',
        'Tässä on nyt semmonen tilanne, että tää viivakoodin lukija ja kassakone ei tykkää yhtään tästä meetvurstista',
        'Tää jää auki koko tilanne',
        'Eiks oo mukavaa että on tullu lunta',
        'No sitten yks on tietysti',
        'Laitetaan tää siihen',
        'Tää on aika saman paksunen',
        'Ota ihan rauhassa, tää ei satu yhtään',
        'Ei se ota sitä',
        'Niin no',
        'No niin',
        'Onnee matkaan nyt',
        'Kobra kotiin',
        'No täältä radiokaupasta Ahonen hei',
        'No ku siinä tuli semmonen tilanne ku se on niin painava ja välilevy pullistu ylos',
        'Ei se varmaan meille mahdu',
        'Tapan sen.',
        'Eiköhän lähdetä parisuhteen pyörteisiin',
        'Katsotaan neiti Ruotsalainen o valitsee mukaansa',
        'Mistä tulet',
        'Asemalta',
        'Mä tulin autosta',
        'Mä oon jo täällä valmiiks',
        'Eiköhän lähdetä pelaamaan',
        'Mikä sinä olet',
        'Kai mäkin oon rapu',
        'Rapu mä oon',
        'Mäkin oon sitten rapu',
        'Richard Kere',
        'Jane Fonda',
        'No vaikka tarzan',
        'Mäkin tapan sen',
        'No vaikka tapan sen',
        'Tällä onkin miehekkäitä miehiä',
        'Mikä on seksissä lempiasentosi',
        'Ensin vastaa Raimo',
        'Kuule, mikä se on kun mies on päällä',
        'Sama',
        'Mikäs se nimi oli',
        'Virtanen Arto',
        'No ne ovat tässä näin',
        'Se on maksettu',
        'Hienoo että pystyt lainaa tätä',
        'Älä viivy kauaa',
        'Onks sulla kasko tässä?',
        'Ei tämmöseen saa kaskoo, hirvi ja palo vaan',
        'Mä tässä kattelen kun sä lähdet',
        'Se on muute vähä huono se virtalukko',
        'Muista sitten että se on takaveto',
        'Hei, tai no ei mitään, kyl sä tiiät',
        'Teillä oli tämän aamun lehdessä Corolla',
        'Voitasko me pojan kanssa kattoo',
        'Ovi roikkuu',
        'Ikkuna on jumissa',
        'Siitä pistetään kulmasohva',
        'Mä otan semmosen',
        'ET SINÄ NYT TÄTÄ SAA',
        'Mitäs tää maksaa',
        'Kuusestahan sä halusit',
        'Näistä kahdesta, noista tulee kulmasohva',
        'Mä otan tän',
        'Täällä on se normaalimies joka osti sen kulmasohvan teiltä',
        'Mulla rupee olee nää puuosat valmiina',
        'Mikä siellä nyt taas on',
        'Nypi tosta',
        'Jaa',
        'Päivää, onko täällä puhelinta',
        'Mulla jäi mummo katujyrän alle',
        'Onko teillä hinausköyttä?',
        'Onko teillä sokeria, voisko saada vähä lainaks',
        'Voinko mä muuttaa sun luo asumaan',
        'Totta kai',
        'Ota jääkaapista kaljaa ja mennään olohuoneeseen kattoo pornoo',
        'Ette saa',
        'Nyt ei pysty',
        'No puhu nyt',
        'Voit sä tulla käymään, juteltais vähä',
        'Millanen päivä sulla on?',
        'Kaveri oli aika haka verkolla',
        'Eiks me oteta yhet oluet',
        'Mitä sulla on huomenna',
        'Kaks a-olutta ja talon lonkero',
        'Pitäskö mennä oikein pöytään asti',
        'Normipäivä',
        'Eiks me oteta sit kunnon tumut',
        'Ginigreippiä kolme',
        'HEI ÄLÄ RUNTTA MUN TENNISKASSIA',
        'Se on järkevää.',
        'Ei tää saatana päivänvaloo kestä',
        'Missäs Jermu on',
        'Eiks sun pitäny hoitaa se',
        'Mikä on hallituksen virallinen kanta pakoilaispolitiikkaan',
        'Mistä maista suomi ottaa pakolaisia',
        'Annetaan vaan kahen päivän paperit ja hämätään vihreitä',
        'Pena sen ehdotti',
        'Meille jää kuulemma jonkin verran rahaa',
        'No mutta tämähän on täysin rasistinen lausunto',
        'Meillä on hallituksen iltakoulu, ja me tarkennamme tätä lausuntoa',
        'Saatiinhan se sopu',
        'No ei hemmetissä',
        'Metallilakkoa on nyt kestänyt kaksi viikkoa, onko neuvottelut vaikeita',
        'Sehän ei oo tärkeetä mitä tienaa, vaan mitä maksaa veroja',
        'Päätettiin että perustetaan firma man-saarille, pannaan 15 miljardia sinne, sitten Ville ottaa avioeron',
        'Tämä ei ollut valtioneuvoston virallinen lausunto',
        'Kysymyshän on sinänsä vaikea, mutta valoa on tunnelin päässä',
        'Pena sen keksi ihan loppumetreillä, että hämätäänn vihreitä',
        'Tämähän on täysin pöyristyttävää',
        'Ja miten voin palvella?',
        'Kanarian saarille',
        'Ei ku tää playa del ingles',
        'Juhannuksena, kolme viikkoa',
        'No neljä!',
        'Mites toi majotus?',
    ];

    public function kummeliSentence(): string
    {
        return static::randomElement(static::$kummeliSentences);
    }

    // Add more custom methods as needed
}
