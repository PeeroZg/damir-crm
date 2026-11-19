<?php

/*
| Croatian validation messages for the rules used in this application.
| Anything missing falls back to English (APP_FALLBACK_LOCALE).
*/
return [
    'accepted' => 'Polje :attribute mora biti prihvaćeno.',
    'after_or_equal' => 'Polje :attribute mora biti datum jednak ili nakon :date.',
    'array' => 'Polje :attribute mora biti popis.',
    'boolean' => 'Polje :attribute mora biti da ili ne.',
    'confirmed' => 'Potvrda polja :attribute se ne podudara.',
    'current_password' => 'Lozinka nije ispravna.',
    'date' => 'Polje :attribute mora biti ispravan datum.',
    'digits' => 'Polje :attribute mora imati :digits znamenki.',
    'email' => 'Polje :attribute mora biti ispravna e-mail adresa.',
    'enum' => 'Odabrana vrijednost polja :attribute nije ispravna.',
    'exists' => 'Odabrana vrijednost polja :attribute nije ispravna.',
    'gt' => [
        'numeric' => 'Polje :attribute mora biti veće od :value.',
    ],
    'in' => 'Odabrana vrijednost polja :attribute nije ispravna.',
    'integer' => 'Polje :attribute mora biti cijeli broj.',
    'lowercase' => 'Polje :attribute mora biti napisano malim slovima.',
    'max' => [
        'array' => 'Polje :attribute može imati najviše :max stavki.',
        'numeric' => 'Polje :attribute ne smije biti veće od :max.',
        'string' => 'Polje :attribute može imati najviše :max znakova.',
    ],
    'min' => [
        'array' => 'Polje :attribute mora imati barem :min stavku.',
        'numeric' => 'Polje :attribute mora biti barem :min.',
        'string' => 'Polje :attribute mora imati barem :min znakova.',
    ],
    'numeric' => 'Polje :attribute mora biti broj.',
    'password' => [
        'letters' => 'Polje :attribute mora sadržavati barem jedno slovo.',
        'mixed' => 'Polje :attribute mora sadržavati velika i mala slova.',
        'numbers' => 'Polje :attribute mora sadržavati barem jedan broj.',
        'symbols' => 'Polje :attribute mora sadržavati barem jedan simbol.',
        'uncompromised' => 'Ova lozinka se pojavila u curenju podataka. Odaberi drugu.',
    ],
    'required' => 'Polje :attribute je obavezno.',
    'required_if' => 'Polje :attribute je obavezno.',
    'string' => 'Polje :attribute mora biti tekst.',
    'unique' => 'Vrijednost polja :attribute već postoji.',
    'url' => 'Polje :attribute mora biti ispravna web adresa.',

    'attributes' => [
        'name' => 'naziv',
        'email' => 'e-mail',
        'password' => 'lozinka',
        'current_password' => 'trenutna lozinka',
        'phone' => 'telefon',
        'website' => 'web stranica',
        'status' => 'status',
        'role' => 'uloga',
        'locale' => 'jezik',
        'position' => 'pozicija',
        'notes' => 'bilješke',
        'description' => 'opis',
        'lost_reason' => 'razlog gubitka',
        'subject_type' => 'vrsta zapisa',
        'type' => 'vrsta',
    ],
];
