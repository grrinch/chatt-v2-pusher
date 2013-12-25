# Consulting Man

## Członkowie zespołu

~~Arkadiusz Kasperski 102633 arekk199@wp.pl~~

Radosław Paluszak 102614 radoslaw.paluszak@student.put.poznan.pl

### Krótki opis projektu

Aplikacja jest ściśle WEBowa. Pozwala na przeprowadzenie konsultacji lekarskich on-line. Prócz przeglądarki w jednej z aktualnych wersji (załóżmy okolice FF 20+, Chrome 23+, Opera 12+, IE 9+) z __włączoną__ obsługą JavaScript, nie ma żadnych dodatkowych wymagań. Niestety wersja aplikacji nad którą pracujemy nie przewiduje możliwości uruchamiania z poziomu telefonu komórkowego (za małe możliwości obsługi JS). Wyjście jakie jednak przygotowujemy będzie miało _potencjalną_ możliwość uruchomienia napisania aplikacji klienckiej w wersji na iPhone lub Androida.

__Przykład użycia__

Zespół kilku (np. 3) onkologów, którzy stacjonują w niezależnych ośrodkach badawczych (np. Poznań, Cambridge, Sydney) wymaga konsultacji bardzo skomplikowanego przypadku potworniaka. Logują się do aplikacji, wybierają pokój chroniony hasłem. Każdy z nich ma możliwość użycia tekstowego czatu, jaki jest wbudowany w naszą aplikację. Dodatkowo, każdy z nich może uploadować zdjęcie w formacie JPEG (max 1600x1600) i oznaczyć na nim elementy, które aktualnie omawia. Historia czatu i zdjęcia zapisywane są w bazie danych, co umożliwia powrót do zapisków z konsultacji, jak i całej konsultacji w przyszłości.

### Założona funkcjonalność po pierwszym etapie
1. Pełna integracja frameworka z ORMem
- 1. Rozwiązanie problemu połączenia Zend Framework 2 z Doctrine 2. 
- 2. Prawdopodobnie wykorzystamy gotowe i zalecane rozwiązania.
2. Stworzenie schematu bazy danych MySQL (MySQL Workbench)
3. Utworzenie szkieletu aplikacji
Odpowiedni podział zgodny z wymuszoną architekturą MVC. Wygenerowane modele dla Doctrine oraz przygotowane kontrolery, akcje i widoki
4. Integracja ZF z Pusherem oraz "żywy" przykład działania tego połączenia
5. Możliwość logowania - autoryzacja do "pokoi" konferencyjnych
6. Zalążek czatu
Formularze, wysyłanie wiadomości, jeszcze prawdopodobnie bez możliwości ich odbierania.

### Zakładana funkcjonalność wersji końcowej
7. Działający czat
Wysyłanie swoich wypowiedzi do silnika osadzonego na serwerze HTTP. Odbieranie wiadomości innych uczestników w czasie rzeczywistym. Możliwość swobodnej rozmowy tekstowej.
8. Implementacja możliwości opisu zdjęcia/grafiki
Integracja gotowego lub dopasowanego do własnych potrzeb modułu jQuery, który będzie umożliwiać oznaczanie odpowiednich "współrzędnych" na obszarze obrazka i przesyłanie ich do wewnątrz aplikacji
9. Możliwość zapisu konferencji do formatu HTML/XML
10. Możliwość odczytu archiwalnych konferencji

## Opis architektury
![Diagram Consulting Man](http://p43.pl/diagram.jpg "Diagram funkcjonalny")

Rozwiązanie oparte w pełni na module Pusher (www.pusher.com), który umożliwia komunikację w czasie rzeczywistym. Prócz tego silnik sklepu działa na wydajnym frameworku połączonym z ORMem Doctrine, który sprawia, że aplikacja jest niezależna od bazy danych, na której pracuje. GUI wzbogacone jest za pomocą HTML5, CSS oraz JavaScript (szczególnie jQuery). Autoryzacja odbywa się za pomocą loginu i hasła, które nadawane są przez administratora aplikacji.

### Podział zadań w zespole
__Radek:__
- ZF (silnik i inne "bebechy" :P)
- Doctrine
- jQuery i opracowanie komunikacji po stronie klienta
- integracja z Pusherem
- __HTML, CSS (rzeźbienie)__
- __GUI po stronie klienta__
- __testy jednostkowe__

~~__Arek:__~~
~~- HTML, CSS (rzeźbienie)~~
~~- GUI po stronie klienta~~
~~- testy jednostkowe~~

### Przewidywane środowisko realizacji projektu
- PHP 5 (Apache2, NetBeans)
- Zend Framework 2
- Doctrine 2 (MySQL)
- AJAX
- JSON
- JS
- jQuery
- Pusher
- Wzorzec projektowy MVC


### Przewidywane trudności i problemy
- Integracja Doctrine z ZF
- Rozwiązanie problemu czasu rzeczywistego
- Rozwiązanie oznaczania fragmentów zdjęcia w jQuery
- AJAX jako alternatywa dla połączenia przez Pusher (wąskie gardło w ilości żądań HTTP)

