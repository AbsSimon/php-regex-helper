<?php

function regex($options = [], $c)
{
	$valid = [];
	$valid['mode::case-insensitiv'] = 'i';
	$valid['mode::dot-matches-also-newsline'] = 's';
	$valid['mode::remove-whitespaces-and-comments'] = 'x';
	$valid['mode::begin-and-end-signs-matches-every-newline'] = 'm';
	$valid['mode::eval-php-code'] = 'e';
	$valid['mode::smallest-selection-for*and+'] = 'U'; //ungreedy, * and + will match as little as possible
	$valid['mode::convert-to-utf8'] = 'u';
	$valid['mode::default-begins-with'] = 'A';
	$valid['mode::line-starts-and-ends-with-newline'] = 'D'; //???
	$valid['mode::use-cache'] = 'S';
	//$valid['match-all'] = 'g'; // use preg_match_all()

	$result = '{' . $c . '}';

	foreach($options as $option) {
		if (isset($valid[$option])) {
			$result .= $valid[$option]; // trailing options
		}
	}

	return $result;
}

function inlineOption($options = [], $c)
{
	$valid = [];
	$valid['mode::case-insensitiv'] = 'i';
	$valid['mode::dot-matches-also-newsline'] = 's';
	$valid['mode::remove-whitespaces-and-comments'] = 'x';
	$valid['mode::begin-and-end-signs-matches-every-newline'] = 'm';
	$valid['mode::smallest-selection-for*and+'] = 'U';
	$valid['disabel-mode::case-insensitiv'] = '-i'; // for example with global i
	$valid['disabel-mode::dot-matches-also-newsline'] = '-s';
	$valid['disabel-mode::remove-whitespaces-and-comments'] = '-x';
	$valid['disabel-mode::begin-and-end-signs-matches-every-newline'] = '-m';
	$valid['disabel-mode::smallest-selection-for*and+'] = '-U';

	$tmp = '';
	foreach($options as $option) {
		if (isset($valid[$option])) {
			$tmp .= $valid[$option];
		}
	}

	return '(?' . $tmp . ':' . $c . ')'; // this is not a Group. Extra group needed for capturing
}

// Containers
function oneOfTheseCharacters($c) 	{ return '[' . $c . ']'; }  // Char Class, inside everything is connected with or and no meta chars work
function notOneOfTheseCharacters($c) 	{ return '[^' . $c . ']'; } // means at least one char (!) that is not inside the char class
function Group($c, $name = '') 		{ return '(' . (!empty($name) ? '?<' . $name . '>' : '') . $c . ')'; } // also with capturing
function notCaptureGroup($c) 		{ return '(?:' . $c . ')'; }

// Logical Or
function Or_(...$x) 			{ return implode('|', $x); }

// Ranges (only inside a oneOfTheseCharacters() allowed)
function CharRange($x, $y) 		{ return $x . '-' . $y; }

// Quantifiers (for single chars or groups)
function nullOrMany($c = '') 		{ return $c . '*'; }
function nullOrOne($c = '') 		{ return $c . '?'; }
function oneOrMany($c = '')	 	{ return $c . '+'; }
function xTimes($c, $x) 		{ return $c . '{' . $x . '}'; }
function minMaxTimes($c, $x, $y)	{ return $c . '{' . $x . ',' . $y . '}'; }
function minTimes($c, $x) 		{ return $c . '{' . $x . ',}'; }

// Character-Representations (will need oneOfTheseCharacterss)
function Char()				{ return '.'; }    		// except '\n'
function Number() 			{ return '\d'; }		// '\d'
function NonNumber() 			{ return '\D'; }		// '^0-9'
function Tab() 				{ return '\t'; }
function Newline() 			{ return '\n'; }
function Return_() 			{ return '\r'; }
function StringLower() 			{ return 'a-z'; }
function StringUpper() 			{ return 'A-Z'; }
function WordChar()			{ return '\w'; }        	// '0-9A-Za-z_' maybee also not ASCI Signs?
function NonWordChar()			{ return '\W'; }        	// '^0-9A-Za-z_'
function Whitespace() 			{ return '\s'; }        	// '\r\n \t'
function NonWhitespace() 		{ return '\S'; }        	// '^\r\n \t'
function GermanyWordChar()		{ return '\wÄäÜüÖöß'; }
function escape($c) 			{ return preg_quote($c); }

// Positions (not 'consumptive', so no problems with multiple turns and preg_match_all())
function beginsWith($c = '')		{ return '^'  . $c; }	       // '\A' hole string, use 'mode::begin-and-end-signs-matches-every-newline' for each line
function endsWith($c = '') 		{ return $c . '$'; }	       // '\Z'
function endsOfString($c = '') 		{ return $c . '\z'; }          // always
function wordBoundry()			{ return '\b'; }    	       // \babc\b - single word
function inWordBoundry()        	{ return '\B'; }	       // \Babc\B - whithin a word

// Backreference
function getGroupContentNr($n)  	{ return '\\' . $n; }          // for example to find double words (in one line?)

// Lookarounds (also Positions and Sub-Regex)
function ifRight($r)			{ return '(?='  . $r . ')'; }  // d(?=r)   // Look Ahead
function ifLeft($r)		    	{ return '(?<=' . $r . ')'; }  // (?<=r)d  // Look Behind
function ifNotRight($r)  		{ return '(?!'  . $r . ')'; }  // d(?!r)   // Negativ Look Ahead
function ifNotLeft($r)			{ return '(?<!' . $r . ')'; }  // (?<!r)d  // Negativ Look Behind
