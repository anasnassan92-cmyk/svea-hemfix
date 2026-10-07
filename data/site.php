<?php
// Svea Hemfix – site data. Edit prices/texts here.
// Services: group 'privat' (RUT) or 'foretag'. Trapphusstädning has its own page (/trapphusstadning).
define('SVEA_GROUPS', ['privat' => 'Privat – med RUT-avdrag', 'foretag' => 'Företag & fastighet']);
define('SVEA_HOURLY', 449);
define('SVEA_HOURLY_RUT', '224,50');
define('SVEA_ORGNR', '559427-4010');
define('SVEA_COMPANY', 'Svea Renovering & Design AB');
define('SVEA_ADDRESS', 'Kronborgsgränd 19, 164 46 Kista');
define('SVEA_AREAS', ['Stockholm','Solna','Sundbyberg','Nacka','Lidingö','Danderyd','Täby','Sollentuna','Järfälla','Huddinge','Botkyrka','Haninge','Tyresö','Bromma','Vallentuna','Upplands Väsby']);
define('SVEA_SERVICES', [
  [
    'slug' => 'hemstadning', 'name' => 'Hemstädning', 'img' => '/media/p/p12.webp', 'icon' => 'home', 'dur' => '1,5–2 tim', 'rut' => true, 'popular' => true, 'group' => 'privat',
    'addons' => ['Fönsterputs', 'Ugn, kyl & frys invändigt', 'Strykning & tvätt', 'Balkong & uteplats'],
    'short' => 'Regelbunden städning som gör skillnaden, varje gång. Hela ditt hem – från golv till tak.',
    'intro' => 'Vår hemstädning är för dig som vill komma hem till ett rent och fräscht hem utan att lägga din fritid på städning. Vi städar regelbundet – varje vecka, varannan vecka eller en gång i månaden – med samma noggranna städare som lär känna ditt hem.',
    'included' => ['Dammtorkning av alla fria ytor, lister och lampor', 'Dammsugning och våttorkning av golv', 'Rengöring av kök: bänkar, spis, diskho och skåpsluckor utvändigt', 'Rengöring av badrum: toalett, handfat, dusch/badkar, speglar', 'Tömning av papperskorgar och bäddning', 'Miljövänliga städmedel och egen utrustning'],
    'faq' => [['Måste jag vara hemma när ni städar?', 'Nej. De flesta av våra kunder lämnar en nyckel eller kod, så städar vi medan du är på jobbet. Alla våra städare är bakgrundskontrollerade och försäkrade.'], ['Kan jag få samma städare varje gång?', 'Ja, vid regelbunden städning gör vi allt för att du ska få samma städare varje gång.'], ['Vad händer om jag behöver avboka?', 'Avboka kostnadsfritt senast 24 timmar innan bokad tid.']],
  ],
  [
    'slug' => 'storstadning', 'name' => 'Storstädning', 'img' => '/media/p/p13.webp', 'icon' => 'spark', 'dur' => '3–5 tim', 'rut' => true, 'popular' => true, 'group' => 'privat',
    'short' => 'Djuprengöring av hela hemmet – kök, badrum, möbler, golv och mattor. 1–2 gånger per år.',
    'intro' => 'Storstädning (djuprengöring) för hem som behöver en riktig omstart – efter en lång vinter, inför en fest eller helt enkelt när vardagsstädningen inte räcker. Vi går på djupet där man annars inte kommer åt.',
    'included' => ['Allt som ingår i hemstädning – plus:', 'Rengöring bakom och under möbler och vitvaror', 'Avkalkning av badrum, kakel och fogar', 'Kök: ugn, fläktfilter, mikro och skåpsluckor in- och utvändigt', 'Element, lister, dörrkarmar och lampor', 'Fönsterbänkar, fönsterkarmar och eluttag'],
    'parts' => [
      ['Kök', 'Ugn, fläkt, kyl och alla ytor på djupet.', ['Ugn och plåtar in- och utvändigt', 'Fläkt och fläktfilter', 'Kyl och frys in- och utvändigt (avfrostad)', 'Skåpsluckor, lådor och handtag in- och utvändigt', 'Kakel, stänkskydd, diskho och blandare', 'Golv och lister']],
      ['Badrum', 'Avkalkning, sanering och polering av kakel och fogar.', ['Avkalkning av kakel, klinker och fogar', 'Dusch, badkar, blandare och duschväggar', 'Toalett in- och utvändigt samt bakom', 'Handfat, speglar och badrumsskåp', 'Golvbrunn rensas och rengörs', 'Ventiler och lister']],
      ['Möbler & textil', 'Ångtvätt av soffor, fåtöljer och madrasser.', ['Soffor, fåtöljer och stolar i tyg', 'Madrasser och sängkläder (ångrengöring)', 'Fläckborttagning', 'Skinnmöbler rengörs och vårdas', 'Allergensanering med ånga', 'Snabbtorkande metod – möblerna kan användas samma dag']],
      ['Golv & mattor', 'Maskinskurning, extraktion och golvvård.', ['Mattor: djuprengöring med extraktion', 'Klinker och kakelgolv: maskinskurning av fogar', 'Parkett och laminat: skonsam rengöring och polish vid behov', 'Fläckborttagning', 'Golvvård och oljning efter överenskommelse', 'Trapphus och hallar']],
    ],
    'faq' => [['Kan ni ta bort mögel i fogarna?', 'Ja, i de flesta fall får vi bort mögel och missfärgningar med saneringsmedel. Djupt inträngt mögel kan kräva nya fogar.'], ['Passar ångrengöring för alla tyger?', 'De flesta tyger tål ånga. Vi testar alltid på en dold yta först och anpassar metoden efter materialet.'], ['Kan jag boka bara kök eller bara badrum?', 'Ja. Storstädning kan bokas för hela hemmet eller för enskilda rum – t.ex. bara köket eller badrummet. Timpriset är detsamma.'], ['Hur ofta bör man djuprengöra?', 'De flesta hem mår bra av en djuprengöring 1–2 gånger per år, som komplement till regelbunden hemstädning.'], ['Kan ni flytta möbler?', 'Ja, vi flyttar lättare möbler för att komma åt. Tunga möbler som soffor och skåp flyttas efter överenskommelse.']],
  ],
  [
    'slug' => 'flyttstadning', 'name' => 'Flyttstädning', 'img' => '/media/p/p02.webp', 'icon' => 'truck', 'dur' => '3–6 tim', 'rut' => true, 'popular' => true, 'group' => 'privat',
    'addons' => ['Slutstädning av hyresrätt', 'Visningsstädning inför försäljning', 'Dödsbostädning', 'Bortforsling av grovsopor'],
    'short' => 'Professionell städning vid flytt – utan krångel. Vi fixar allt så du slipper stressen.',
    'intro' => 'Flyttstädning ska klara en besiktning – och det gör vår. Vi följer Mäklarsamfundets riktlinjer och städar hela bostaden in i minsta vrå, så att du kan lämna över nycklarna med gott samvete. Städgaranti ingår alltid.',
    'included' => ['Kök: ugn, spis, kyl/frys, fläkt, skåp in- och utvändigt', 'Badrum: avkalkning, golvbrunn, kakel, fogar, toalett', 'Fönsterputs in- och utvändigt samt mellan bågar', 'Garderober, lister, element, dörrar och karmar', 'Golv: dammsugning och våttorkning i alla rum', 'Balkong/uteplats sopas – städgaranti med ombesiktning'],
    'faq' => [['Gör ni slutstädning av hyresrätt?', 'Ja. Slutstädning vid avflyttning från hyresrätt är samma tjänst som vår flyttstädning och godkänns av hyresvärdens besiktning.'], ['Ingår fönsterputs i flyttstädningen?', 'Ja, fönsterputs in- och utvändigt samt mellan bågar ingår alltid i vår flyttstädning.'], ['Vad händer om besiktningen har anmärkningar?', 'Vi har städgaranti – får du anmärkningar vid besiktningen kommer vi tillbaka och åtgärdar utan extra kostnad.'], ['Hur långt i förväg bör jag boka?', 'Gärna 1–2 veckor innan flytt, men vi kan ofta hjälpa till med kort varsel.']],
  ],
  [
    'slug' => 'fonsterputsning', 'name' => 'Fönsterputsning', 'img' => '/media/p/p04.webp', 'icon' => 'window', 'dur' => '1–3 tim', 'rut' => true, 'popular' => false, 'group' => 'privat',
    'addons' => ['Fönsterputs för BRF och kontor (avtal)', 'Balkonginglasning', 'Solceller och glastak'],
    'short' => 'Invändig och utvändig fönsterputs som ger ditt hem nytt liv.',
    'intro' => 'Rena fönster släpper in mer ljus och gör hela hemmet ljusare. Vi putsar fönster in- och utvändigt, mellan bågar och på balkonger – med skrapa och rent vatten för ett skinande, strimfritt resultat.',
    'included' => ['Putsning in- och utvändigt', 'Mellan bågar på öppningsbara fönster', 'Fönsterbleck och karmar torkas av', 'Balkong- och altandörrar', 'Speglar och glaspartier vid behov', 'Vi tar med all utrustning'],
    'faq' => [['Hur ofta bör man putsa fönster?', '2–4 gånger per år är lagom för de flesta hem – gärna vår och höst.'], ['Putsar ni även på höjd?', 'Ja, vi putsar fönster som går att nå säkert med stege eller inifrån. Fasadjobb på hög höjd lämnar vi offert på.']],
  ],
  [
    'slug' => 'kontorsstadning', 'name' => 'Kontorsstädning', 'img' => '/media/p/p09.webp', 'icon' => 'office', 'dur' => '2–4 tim', 'rut' => false, 'popular' => false, 'group' => 'foretag',
    'addons' => ['Fönsterputs', 'Golvvård och maskinskurning', 'Storstädning av lokal', 'Förbrukningsmaterial'],
    'short' => 'Professionell städning av kontor och arbetsplatser.',
    'intro' => 'En ren arbetsplats ger gladare personal och ett bättre intryck för besökare. Vi städar kontor, butiker och mottagningar i Stockholm – dagtid eller efter stängning, så ofta ni behöver. Fast månadspris med tydlig omfattning.',
    'included' => ['Dammtorkning av skrivbord, hyllor och fria ytor', 'Dammsugning och moppning av golv', 'Rengöring av toaletter och påfyllning av förbrukningsmaterial', 'Kök/pentry: diskbänk, mikro, kaffemaskin utvändigt', 'Tömning av papperskorgar och källsortering', 'Glaspartier, dörrar och handtag'],
    'faq' => [['Kan ni städa utanför kontorstid?', 'Ja, vi städar gärna kvällar eller tidiga morgnar så att verksamheten inte störs.'], ['Gäller RUT-avdrag för företag?', 'Nej, RUT gäller endast privatpersoner. För företag lämnar vi ett fast månadspris exklusive moms.'], ['Kan vi teckna avtal?', 'Ja, vi erbjuder löpande städavtal på 12 månader eller enligt överenskommelse, med en kontaktperson.']],
  ],
  [
    'slug' => 'byggstadning', 'name' => 'Byggstädning', 'img' => '/media/p/p24.webp', 'icon' => 'award', 'dur' => '2–5 tim', 'rut' => true, 'popular' => false, 'group' => 'foretag',
    'short' => 'Städning efter renovering eller nybyggnation – byggdamm, färgstänk och slutstädning inför besiktning.',
    'intro' => 'Byggstädning och slutstädning efter renovering eller nybyggnation – för privatpersoner (med RUT-avdrag) och byggföretag. Vi tar hand om byggdamm, färgstänk och skräp och lämnar lokalen redo för besiktning eller inflyttning.',
    'included' => ['Borttagning av byggdamm från alla ytor', 'Fönster, karmar och glaspartier', 'Kök och badrum komplett', 'Golv: dammsugning, våttorkning och polish vid behov', 'Färgstänk, klisterrester och etiketter', 'Bortforsling av städavfall'],
    'parts' => [
      ['Grovstädning', 'Direkt efter byggarbetet – skräp, grovdamm och rester.', ['Bortforsling av byggskräp och emballage', 'Grovdammsugning av golv, väggar och tak', 'Färgstänk, spackel- och klisterrester', 'Skyddsplast och etiketter tas bort']],
      ['Finstädning', 'Slutstädning inför besiktning eller inflyttning.', ['Alla ytor dammtorkas – lister, karmar, element, lampor', 'Fönster in- och utvändigt', 'Kök och badrum komplett', 'Golv: dammsugning, våttorkning och polish vid behov']],
    ],
    'faq' => [['Gäller RUT-avdrag för byggstädning?', 'Ja, för privatpersoner som städar sin egen bostad efter renovering. För byggföretag och BRF lämnar vi fast pris exklusive moms.'], ['Vad är skillnaden mot flyttstädning?', 'Byggstädning fokuserar på byggdamm, färgstänk och rester efter hantverkare. Flyttstädning följer Mäklarsamfundets checklista inför överlämning.'], ['Kan ni städa direkt efter hantverkarna?', 'Ja, vi samordnar gärna med hantverkare eller byggföretag så att städningen sker direkt när arbetet är klart.']],
  ],
]);
define('SVEA_REVIEWS', [
  ['Anna Lindberg', 'Stockholm', 5, 'Fantastisk service! Vårt hem har aldrig sett så rent ut. Personalen var professionell och noggrann. Kommer definitivt använda Svea Hemfix regelbundet.'],
  ['Erik Johansson', 'Solna', 5, 'RUT-avdraget gör det så enkelt och prisvärt. Städningen var perfekt och teamet var väldigt professionellt. Rekommenderar starkt!'],
  ['Maria Svensson', 'Nacka', 4, 'Jag har använt Svea Hemfix för flyttstädning två gånger nu. Båda gångerna har resultatet varit perfekt. Mycket nöjd med servicen!'],
  ['Karl Bergström', 'Täby', 5, 'Professionellt och pålitligt! De kom i tid, gjorde ett fantastiskt jobb och lämnade allt skinande rent. Rekommenderas varmt!'],
  ['Sofia Ekström', 'Huddinge', 5, 'Bästa städningen jag någonsin haft! Teamet var noggranna, trevliga och väldigt professionella. Kommer boka igen!'],
]);
define('SVEA_FAQ', [
  ['Hur fungerar RUT-avdraget?', 'RUT-avdraget är en skattereduktion för hushållsnära tjänster. Du betalar bara halva arbetskostnaden – resten hanterar vi direkt med Skatteverket. Du behöver bara godkänna att vi ansöker om avdraget åt dig.'],
  ['Vad kostar en städning?', 'Hemstädning kostar ' . SVEA_HOURLY . ' kr/tim före RUT – du betalar ' . SVEA_HOURLY_RUT . ' kr/tim efter RUT-avdrag. Flyttstädning och större jobb får du ett fast pris på i offerten. Städmaterial ingår alltid.'],
  ['Är ni försäkrade?', 'Ja. Svea Hemfix har fullständig ansvarsförsäkring och alla medarbetare är bakgrundskontrollerade. Du kan känna dig helt trygg.'],
  ['Hur bokar jag?', 'Fyll i formuläret, ring 010 300 10 32 eller skicka ett meddelande på WhatsApp. Vi återkommer inom 24 timmar med en offert – ofta samma dag.'],
  ['Vilka städprodukter använder ni?', 'Endast miljömärkta, icke-giftiga och hypoallergena medel. Perfekt för barn, husdjur och allergiker.'],
  ['Kan jag boka en engångsstädning?', 'Absolut. Allt från engångsstädning till regelbunden städning varje vecka. Vi anpassar oss efter dina behov.'],
  ['Vad händer om jag inte är nöjd?', 'Vi har nöjdhetsgaranti. Är du inte 100 % nöjd kommer vi tillbaka och åtgärdar utan extra kostnad.'],
  ['Kan jag betala med faktura?', 'Ja – Swish, faktura, kort och banköverföring. Välj det som passar dig.'],
]);
