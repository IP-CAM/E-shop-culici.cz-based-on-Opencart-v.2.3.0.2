# Postup, jak zprovoznit culici-oc #

## Instalace OpenCart, včetně češtiny, bez demo dat ##

- stáhnout OpenCart, verzi 2.3.0.2, tj. soubor *"opencart-2.3.0.2.zip"* \[[https://github.com/opencart/opencart/releases](https://github.com/opencart/opencart/releases "OpenCart")\]
- zkopírovat obsah adresáře *"upload"* ze staženého souboru *"opencart-2.3.0.2.zip"* na server 
- do tohoto defaultního stavu udělat změny, potřebné pro přidání češtiny:
    - zkopírovat soubory ze zip archivu *"cestina-2.3.0.2.zip"* (v tomto projektu) do odpovídajících adresářů aplikace na serveru
    - pouze čeština: na serveru nahradit soubor *"install/opencart.sql"* souborem *"opencart-cz.sql"* z tohoto projektu
    - připravené pro culici.cz: na serveru nahradit soubor *"install/opencart.sql"* souborem *"opencart-culici.sql"* z tohoto projektu   
- dále pokračovat v instalaci podle návodu (soubor *"install.txt"* v *"opencart-2.3.0.2.zip"*), krok č. 1 už je hotov.
- instalace je hotova 


## Jak je to s češtinou ##

Aplikace potřebuje přidat češtinu pro uživatelské a administrační prostředí. A pak potřebuje přeložit číselníky, nastavit měnu Kč, daně, ...

Pro uživatelské a administrační prostředí je použita čeština z počeštěné verze OpenCart z webu [http://opencart.cz/](http://opencart.cz/), s drobnými opravami. Potřebné soubory jsou v zip archivu *"cestina-2.3.0.2.zip"*. Je potřeba ji rozbalit a soubory nakopírovat do odpovídajících adresářů aplikace.

Další úpravy už se ukládají do DB, a dělá je správce aplikace.

Nyní je potřeba češtinu přidat v administraci aplikace a nastavit ji jako primární pro eshop:

- přihlásit se jako správce aplikace
- otevřít volbu *System > Localisation > Language*
- kliknout na tlačítko pro přidání nového jazyka
- vyplnit formulář:
    - Language Name: *Čeština*
    - Code: *cs-cz*
    - Locale: *cs-cz*
    - Status: *Enabled*
    - Sort Order: *1*
- uložit
- otevřít volbu *System > Settings*
- zvolit akci "edit"
- přepnout se na záložku "Local"
- nastavit hodnotu parametrů *Language* a "Administration Language" na češtinu.
- uložit
- pokud nechceme mít pro uživatele 2 jazyky, tak angličtinu v *System > Lokalizace > Jazyky* zakážeme.

Tím jsou "počeštěné" jak uživatelské, tak administrátorské prostředí. Ale ještě je potřeba přeložit různé číselníky, přidat CZK jako měnu, nastavit daňovou oblast, třídu a sazbu, ... Toto provádí správce přímo v aplikaci, volba *System > Lokalizace*. Některé položky (třeba daně) nepůjde zrušit, dokud na ně budou navázané produkty.

Všechny potřebné úpravy byly provedeny v referenční instalaci aplikace, následně byl obsah DB exportován do souboru *"opencart-cz.sql"*.
 