# Postup, jak zprovoznit culici-oc #

## Instalace OpenCart ##

- stáhnout OpenCart, verzi 2.3.0.1 \[[https://github.com/opencart/opencart/releases](https://github.com/opencart/opencart/releases "OpenCart")\]
- nainstalovat podle návodu

## Přidání češtiny ##

Pro tento eshop je použita čeština z počeštěné verze OpenCart z webu [http://opencart.cz/](http://opencart.cz/).

Čeština se nachází v zip archivu *cestina-2.3.0.2.zip*. Je potřeba ji rozbalit a soubory nakopírovat do odpovídajících adresářů.

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
- a protože nechceme mít pro uživatele 2 jazyky, tak angličtinu v *System > Lokalizace > Jazyky* zakážeme.


  


    

 