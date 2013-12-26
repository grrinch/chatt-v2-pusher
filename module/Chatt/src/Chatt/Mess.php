<?php

namespace Chatt;

class Mess {
    const OUTSIDER = 'Błąd #0x0!! Nie jestem wewnątrz aplikacji!';
    const I500 = 'Błąd #0x500!! Nastąpił wewnętrzny błąd aplikacji!';
    const I501 = 'Błąd #0x501!! Nie udało się utworzyć użytkownika!';
    const I502 = 'Błąd #0x502!! Nie udało się utworzyć sesji użytkownika!';
    const I410 = 'Błąd #0x410!! Błąd połączenia bądź bazy danych!';
    const I401 = 'Błąd #0x401!! Nie jesteś autoryzowany!';
    const USER_ALREADY_EXISTS = 'Użytkownik o tej nazwie już istnieje!';
    const POKOJ_JEST = "Pokój istnieje. Podaj do niego prawidłowe hasło.";
    const POKOJ_NIE_ISTNIEJE = "Pokój o takiej nazwie nie istnieje. Utworzysz go.";
    const POKOJ_ZLE_HASLO = 'Błąd #0x401!! Podano nieprawidłowe hasło do pokoju!';
    const ZALOGUJ = 'Zaloguj się';
    const AUTO_LOGOUT = 'Nastąpiło automatyczne wylogowanie.';
    const NAZWA_POKOJU = 'Nie udało się pobrać nazwy pokoju.';

}