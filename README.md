# SMSManager.cz API PHP Wrapper
Jednoduchá neoficiální knihovna pro práci se službou [SMSManager.cz](http://www.smsmanager.cz/) v prostředí jazyka PHP >= 5.3

Knihovna pro svůj provoz vyžaduje nainstalované rozšíření cURL.

##Popis
Knihovna obsahuje vlastní namespace `SMSManager` a tvoří ji čtyři třídy:

1. **Config** - konfigurace knihovny podle [popisu API](http://smsmanager.cz/api/) na oficiálních stránkách služby. Standardně je používán protokol **HTTPS** a typ brány **lowcost**. Pro použití je nutné doplnit pouze hodnoty proměnných **username** a **password**.
2. **HTTPRequest** - třída provádějící HTTP GET/POST dotazy do API služby.
3. **SMSManagerException** - třída překládající chybové kódy služby na výjimky. Tyto výjimky jsou v této třídě také automaticky logovány.
4. **SMSManager** - třída implementující následující funkce `SMSManager` API:
	* `prepareMessage(Array $numbers, $text, $type = Config::gateway_type)`
		* `$numbers` - pole telefonních čísel
		* `$text` - text zprávy
		* `$type` - nepovinný parametr typ brány	
	* `send($messsages)`
		* `$messages` - jediná zpráva nebo pole zpráv připravených předchozí metodou `prepareMessage(…)` Zprávy jsou synchronně odeslány pomocí jediného volání XML API služby.
	* `requestList()` - medoda vracející pole zpráv a jejich stav podle [dokumentace API](http://smsmanager.cz/api/http/#requestlist) Formát vracených objektů je patrný z těla metody.
	* `requestStatus($requestId)` - metoda vracející stav konkrétního požadavku podle [dokumentace API](http://smsmanager.cz/api/http/#requeststatus). Formát vraceného objektu je patrný z těla metody.
	* `getUserInfo()` - metoda vracející stav účtu [dokumentace API](http://smsmanager.cz/api/http/#getuserinfo)
	
##Příklad použití
Příklad předpokládá, že je knihovna nainstalována ve vlastním adresáři `SMSManager`, například jako `git submodule` a že je nakonfigurována se správným uživatelským jménem a heslem.

```php
<?php
require("SMSManager/SMSManager.php");
	
$sms = new SMSManager\SMSManager();
	
$message = $sms->prepareMessage("+420776123456", "Testovaci zprava");
$response = $sms->send($message);
	
var_dump($response);
?>
```

##Licence
Tato knihovna je šířena pod BSD licencí:

Copyright © 2012, Adam Konrád. Všechna práva vyhrazena.
Redistribuce a použití zdrojových i binárních forem díla, v původním i upravovaném tvaru, jsou povoleny za následujících podmínek:

Šířený zdrojový kód musí obsahovat výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zřeknutí se odpovědnosti.
Šířený binární tvar musí nést výše uvedenou informaci o copyrightu, tento seznam podmínek a níže uvedené zřeknutí se odpovědnosti ve své dokumentaci a/nebo dalších poskytovaných materiálech.
Ani jméno vlastníka práv, ani jména přispěvatelů nemohou být použita při podpoře nebo právních aktech souvisejících s produkty odvozenými z tohoto software bez výslovného písemného povolení.
TENTO SOFTWARE JE POSKYTOVÁN DRŽITELEM LICENCE A JEHO PŘISPĚVATELI „JAK STOJÍ A LEŽÍ“ A JAKÉKOLIV VÝSLOVNÉ NEBO PŘEDPOKLÁDANÉ ZÁRUKY VČETNĚ, ALE NEJEN, PŘEDPOKLÁDANÝCH OBCHODNÍCH ZÁRUK A ZÁRUKY VHODNOSTI PRO JAKÝKOLIV ÚČEL JSOU POPŘENY. DRŽITEL, ANI PŘISPĚVATELÉ NEBUDOU V ŽÁDNÉM PŘÍPADĚ ODPOVĚDNI ZA JAKÉKOLIV PŘÍMÉ, NEPŘÍMÉ, NÁHODNÉ, ZVLÁŠTNÍ, PŘÍKLADNÉ NEBO VYPLÝVAJÍCÍ ŠKODY (VČETNĚ, ALE NEJEN, ŠKOD VZNIKLÝCH NARUŠENÍM DODÁVEK ZBOŽÍ NEBO SLUŽEB; ZTRÁTOU POUŽITELNOSTI, DAT NEBO ZISKŮ; NEBO PŘERUŠENÍM OBCHODNÍ ČINNOSTI) JAKKOLIV ZPŮSOBENÉ NA ZÁKLADĚ JAKÉKOLIV TEORIE O ZODPOVĚDNOSTI, AŤ UŽ PLYNOUCÍ Z JINÉHO SMLUVNÍHO VZTAHU, URČITÉ ZODPOVĚDNOSTI NEBO PŘEČINU (VČETNĚ NEDBALOSTI) NA JAKÉMKOLIV ZPŮSOBU POUŽITÍ TOHOTO SOFTWARE, I V PŘÍPADĚ, ŽE DRŽITEL PRÁV BYL UPOZORNĚN NA MOŽNOST TAKOVÝCH ŠKOD.
