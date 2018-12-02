# php-regex-helper
Some small functions for better understanding

#### Example 1. Find double words

```sh
$r = regex([],
	Group(
		oneOrMany(
			oneOfTheseCharacters(WordChar())
		)
	) 
	.
	oneOfTheseCharacters(
		Whitespace()
	)
	.
	oneOrMany(
		getGroupContentNr(1)
	)
);

preg_match($r, 'Paris in the the spring', $m);
```
```sh
{([\w]+)[\s]\1+}
```
```sh
Array
(
    [0] => the the
    [1] => the
)
```


#### Example 2. Thousands Separators 

```sh
$r = regex([],

	ifLeft(Number())
	.
	ifRight(
		oneOrMany( 
			Group(Number().Number().Number())
		)
		.
		ifNotRight(Number())
	)
);

echo preg_replace($r, '.', 'Germany has 82675000 citizen.');
```
```sh
{(?<=\d)(?=(\d\d\d)+(?!\d))}
```
```sh
Germany has 82.675.000 citizen.
```

### Example 3. Add mailto: 

```sh
$r = regex(['mode::case-insensitiv'],

	wordBoundry()
	.
	Group(
		// Username (do not start with -.)
		WordChar()
		.
		nullOrMany(
			oneOfTheseCharacters('-.' . WordChar())
		)
		.
		escape('@')
		.
		oneOrMany(  // hostname (do not start with a dot)
			oneOfTheseCharacters('-' . StringLower() . Number())// in Hostname ASCI only, so no \w
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
```
```sh
{\b(\w[-.\w]*@[-a-z\d]+(\.[-a-z\d]+)*\.(com|edu|info|de|gov))\b}i
```
```sh
My email adress is <a href="mailto:test.tester@nasa.gov">test.tester@nasa.gov</a> (http://www.nasa.gov)
```

