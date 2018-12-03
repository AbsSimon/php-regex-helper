php-regex-helper
================

Simple functions for practicing and understanding regular expressions.

Requirements
------------
* PHP > 5.3

Usage
-----
#### Example 1. Find double words
```php
$r = regex([],
	Group(
		oneOrMany(
			oneOfTheseCharacters(WordChar())
		)
	) 
	.
	oneOfTheseCharacters(Whitespace())
	.
	oneOrMany(
		getGroupContentNr(1)
	)
);

preg_match($r, 'Paris in the the spring', $m);

/*
$r = {([\w]+)[\s]\1+}

$m = Array
(
    [0] => the the
    [1] => the
)
*/
```

#### Example 2. Extract search
```php
$r = regex([],
	Or_(
		'-' . Group(
			oneOrMany(NonWhitespace()),
			'exclude' // groupname
		)
	,
		inlineOption(['mode::smallest-selection-for*and+'], 
			'"' . Group(
				nullOrMany(Char()), 
				'literal' 
			) . '"'
		)
	,
		Group(
			oneOrMany(NonWhitespace()),
			'normal'
		)
	)
);

if (preg_match_all($r, 'programming language "PHP 7.2" -perl', $m)) {
	print_r($m);
}

/*
$r = {-(?\S+)|(?U:"(?.*)")|(?\S+)}

$m = Array
(
    ...
    [exclude] => Array
        (
            [0] => 
            [1] => 
            [2] => 
            [3] => perl
        )
    [literal] => Array
        (
            [0] => 
            [1] => 
            [2] => PHP 7.2
            [3] => 
        )
    [normal] => Array
        (
            [0] => programming
            [1] => language
            [2] => 
            [3] => 
        )
    ...
)
*/
```

#### Example 3. Add mailto: 
```php
$r = regex(['mode::case-insensitiv'],

	wordBoundry()
	.
	Group(
		WordChar()
		.
		nullOrMany(
			oneOfTheseCharacters('-.' . WordChar())
		)
		.
		escape('@')
		.
		oneOrMany(  // hostnames do not start with a dot...
			oneOfTheseCharacters('-' . StringLower() . Number())
		)
		.
		nullOrMany( 
			Group(
				escape('.')
				.
				oneOrMany(
					oneOfTheseCharacters('-' . StringLower() . Number())
				)
			)
		)
		.
		escape('.')
		.
		Group(
			Or_('com','edu','info','de','gov') // Domain
		)
	)
	.
	wordBoundry()
);

echo preg_replace($r, '<a href="mailto:$1">$1</a>', 'My email adress is test.tester@nasa.gov (http://www.nasa.gov)');

/*
$r = {\b(\w[-.\w]*@[-a-z\d]+(\.[-a-z\d]+)*\.(com|edu|info|de|gov))\b}i

My email adress is <a href="mailto:test.tester@nasa.gov">test.tester@nasa.gov</a> (http://www.nasa.gov)
*/

```
