<?php

$passengers = [
	'volk' => [
	],
	'koza' => [
		'check' => function($pass) {
			if (in_array('volk', $pass) && !in_array('ded', $pass)) {
				return false;
			}
			return true;
		}
	],
	'kapusta' => [
		'check' => function($pass) {
			if (in_array('koza', $pass) && !in_array('ded', $pass)) {
				return false;
			}
			return true;
		}
	],
	'ded' => [
		'canDrive' => true
	]
];

function array_remove_value($array, $remove) {
	if (!is_array($remove)) {
		$remove = [$remove];
	}
	return array_filter($array, function($value) use ($remove) {
		return !in_array($value, $remove);
	});
}
function validate($pass, $except=[]) {
	global $passengers;
	$pass = array_remove_value($pass, $except);
	foreach ($pass as $p) {
		if (in_array($p, $except)) {
			continue;
		}
		if (isset($passengers[$p]['check'])) {
			$ret = $passengers[$p]['check']($pass);
			if (!$ret) {
				return false;
			}
		}
	}
	return true;
}
function canDrive($pass, $except = []) {
	global $passengers;
	$pass = array_remove_value($pass, $except);
	foreach ($pass as $p) {
		if (isset($passengers[$p]['canDrive']) && $passengers[$p]['canDrive']) {
			return true;
		}
	}
	return false;
}

function roll($pass) {
	if (!count($pass)) {
		return [];
	}
	$combinations = [];
	// We need to take 1 element
	foreach ($pass as $p) {
		// validate left
		if (!validate($pass, [$p])) {
			continue;
		}
		//validate boat
		if (!validate([$p])) {
			continue;
		}
		if (!canDrive([$p])) {
			continue;
		}
		$combinations[] = [$p];
	}
	if (count($pass) == 1) {
		return $combinations;
	}
	for ($i1 = 0; $i1 < count($pass) - 1; $i1++) {
		for ($i2 = $i1 + 1; $i2 < count($pass); $i2++) {
			$p1 = $pass[$i1];
			$p2 = $pass[$i2];
			if (!validate($pass, [$p1, $p2])) {
				continue;
			}
			//validate boat
			if (!validate([$p1, $p2])) {
				continue;
			}
			if (!canDrive([$p1, $p2])) {
				continue;
			}
			$combinations[] = [$p1, $p2];
		}
	}
	return $combinations;
}
$states = [];
function dump($left, $right, $boatLeft) {
	echo $boatLeft ? 'B ' : '  ';
	echo str_pad(implode($left, ' '), 50);
	echo $boatLeft ? '  ' : 'B '; 
	echo implode($right, ' ') . "\n";
}
function doit($left, $right, $boatLeft) {
	global $states;
	sort($left);
//	$left = array_unique($left);
	sort($right);
//	$right = array_unique($right);
	$state = implode($left) . implode($right) . $boatLeft;
	if (in_array($state, $states)) {
		return false;
	}
	if (empty($left)) {
		dump($left, $right, $boatLeft);
		return true;
	}
	$states[] = $state;
	if ($boatLeft) {
		$boats = roll($left);
		foreach ($boats as $boat) {
			if (doit(array_remove_value($left, $boat), array_merge($right, $boat), false)) {
				dump($left, $right, $boatLeft);
				return true;
			}
		}
	} else {
		$boats = roll($right);
		foreach ($boats as $boat) {
			if (doit(array_merge($left, $boat), array_remove_value($right, $boat), true)) {
				dump($left, $right, $boatLeft);
				return true;
			};
		}
	}
	array_pop($states);
	return false;
}

//var_dump(roll(['ded', 'kapusta', 'volk']));
doit(['volk', 'koza','kapusta', 'ded'], [], true);

