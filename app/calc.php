<?php
require_once dirname(__FILE__).'/../config.php';

// KONTROLER strony kalkulatora

// W kontrolerze niczego nie wysyła się do klienta.
// Wysłaniem odpowiedzi zajmie się odpowiedni widok.
// Parametry do widoku przekazujemy przez zmienne.

//ochrona kontrolera - poniższy skrypt przerwie przetwarzanie w tym punkcie gdy użytkownik jest niezalogowany
include _ROOT_PATH.'/app/security/check.php';

//pobranie parametrów
function getParams(&$kwotaPozyczki,&$iloscMiesiecy,&$oprocentowanie){
	$kwotaPozyczki = isset($_REQUEST['kwotaPozyczki']) ? $_REQUEST['kwotaPozyczki'] : null;
	$iloscMiesiecy = isset($_REQUEST['iloscMiesiecy']) ? $_REQUEST['iloscMiesiecy'] : null;
	$oprocentowanie = isset($_REQUEST['oprocentowanie']) ? $_REQUEST['oprocentowanie'] : null;
}

//walidacja parametrów z przygotowaniem zmiennych dla widoku
function validate(&$kwotaPozyczki,&$iloscMiesiecy,&$oprocentowanie,&$messages){
	// sprawdzenie, czy parametry zostały przekazane
	if ( ! (isset($kwotaPozyczki) && isset($iloscMiesiecy) && isset($oprocentowanie))) {
		// sytuacja wystąpi kiedy np. kontroler zostanie wywołany bezpośrednio - nie z formularza
		// teraz zakładamy, ze nie jest to błąd. Po prostu nie wykonamy obliczeń
		return false;
	}

	// sprawdzenie, czy potrzebne wartości zostały przekazane
	if ( $kwotaPozyczki == "") {
		$messages [] = 'Nie podano liczby 1';
	}
	if ( $iloscMiesiecy == "") {
		$messages [] = 'Nie podano liczby 2';
	}
    if ( $oprocentowanie == "") {
        $messages [] = 'Nie podano liczby 3';
    }

	//nie ma sensu walidować dalej gdy brak parametrów
	if (count ( $messages ) != 0) return false;
	
	// sprawdzenie, czy $x i $y są liczbami całkowitymi
	if (! is_numeric( $kwotaPozyczki )) {
		$messages [] = 'Pierwsza wartość nie jest liczbą całkowitą';
	}
	
	if (! is_numeric( $iloscMiesiecy )) {
		$messages [] = 'Druga wartość nie jest liczbą całkowitą';
	}

    if (! is_numeric( $oprocentowanie )) {
        $messages [] = 'Trzecia wartość nie jest liczbą całkowitą';
    }

    if (count ( $messages ) != 0) return false;
	else return true;
}

function process(&$kwotaPozyczki,&$iloscMiesiecy,&$oprocentowanie,&$messages,&$result){
	global $role;
	
	//konwersja parametrów na int
    $kwotaPozyczki = intval($kwotaPozyczki);
    $iloscMiesiecy = intval($iloscMiesiecy);
    $oprocentowanie = intval($oprocentowanie);


    //wykonanie operacji
    $result=($kwotaPozyczki + ($kwotaPozyczki * ($oprocentowanie/100)))/$iloscMiesiecy;
}

//definicja zmiennych kontrolera
$kwotaPozyczki = null;
$iloscMiesiecy = null;
$oprocentowanie = null;
$result = null;
$messages = array();

//pobierz parametry i wykonaj zadanie jeśli wszystko w porządku
getParams($kwotaPozyczki,$iloscMiesiecy,$oprocentowanie);
if ( validate($kwotaPozyczki,$iloscMiesiecy,$oprocentowanie,$messages) ) { // gdy brak błędów
	process($kwotaPozyczki,$iloscMiesiecy,$oprocentowanie,$messages,$result);
}

// Wywołanie widoku z przekazaniem zmiennych
// - zainicjowane zmienne ($messages,$kwotaPozyczki,$iloscMiesiecy,$oprocentowanie,$result)
//   będą dostępne w dołączonym skrypcie
include 'calc_view.php';