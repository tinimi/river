<?php
ini_set('memory_limit','1G');

function readFromFile($filename) {
	$f = fopen($filename, 'r');
	$sudoku = [];
	$i = 0;
	while ($s = fgets($f)) {
		$s = preg_replace("/\s/", "", $s);
		if (empty($s)) {
			continue;
		}
		for ($j = 0; $j < 9; $j++) {
			if (!preg_match('/^[\d_]$/', $s[$j])) {
				die("Invalid symbol at $i $j {$s[$j]}");
			}
			if ('_' == $s[$j]) {
				$sudoku[$i][$j] = -1;
			} else {
				$sudoku[$i][$j] = intval($s[$j]);
			}
		}
		$i++;
	}
	fclose($f);
	return $sudoku;
}

function toString($sudoku) {
	$output = '';
	for ($i = 0; $i < 9; $i++) {
		for ($j = 0; $j < 9; $j++) {
			if (-1 == $sudoku[$i][$j]) {
				$output .= '_';
			} else {
				$output .= $sudoku[$i][$j];
			}
			if (2 == $j % 3) {
				$output .= ' ';
			}
		}
		$output .= "\n";
		if (2 == $i % 3) {
			$output .= "\n";
		}

	}
	return $output;
}

function solve1_vertical(&$sudoku) {
	$replaced = false;
	for ($i = 0; $i < 9; $i++) {
		$used = [1,2,3,4,5,6,7,8,9];
		$leftPos = -1;
		$found = 0;
		for ($j = 0; $j < 9; $j++) {
			if (-1 != $sudoku[$i][$j]) {
				$used[$sudoku[$i][$j]-1] = false;
				$found++;
			} else {
				$leftPos = $j;
			}
		}
		if (8 == $found) {
			$used = array_filter($used);
			$sudoku[$i][$leftPos] = array_pop($used);
			$replaced = true;
		}
	}
	return $replaced;
}

function solve1_horizontal(&$sudoku) {
	$replaced = false;
	for ($j = 0; $j < 9; $j++) {
		$used = [1,2,3,4,5,6,7,8,9];
		$leftPos = -1;
		$found = 0;
		for ($i = 0; $i < 9; $i++) {
			if (-1 != $sudoku[$i][$j]) {
				$used[$sudoku[$i][$j]-1] = false;
				$found++;
			} else {
				$leftPos = $i;
			}
		}
		if (8 == $found) {
			$used = array_filter($used);
			$sudoku[$leftPos][$j] = array_pop($used);
			$replaced = true;
		}
	}
	return $replaced;
}

function solve1_quad(&$sudoku) {
	$replaced = false;
	for ($qi = 0; $qi < 3; $qi++) {
		for ($qj = 0; $qj < 3; $qj++) {
			$used = [1,2,3,4,5,6,7,8,9];
			$li = $lj = -1;
			$found = 0;

			for ($i = 0; $i < 3; $i++) {
				for ($j = 0; $j < 3; $j++) {
					$v = $sudoku[$qi * 3 + $i][$qj * 3 + $j];
					if (-1 != $v) {
						$used[$v-1] = false;
						$found++;
					} else {
						$li = $i;
						$lj = $j;
					}
				}
			}

			if (8 == $found) {
				$used = array_filter($used);
				$sudoku[$qi * 3 + $li][$qj * 3 + $lj] = array_pop($used);
				$replaced = true;
			}

		}
	}

	return $replaced;
}

function solve1(&$sudoku) {
	while (true) {
		if (solve1_vertical($sudoku)) {
			continue;
		}
		if (solve1_horizontal($sudoku)) {
			continue;
		}
		if (solve1_quad($sudoku)) {
			continue;
		}
		break;
	}
}

function validate_vertical(&$sudoku) {
	for ($i = 0; $i < 9; $i++) {
		$used = [0,0,0,0,0,0,0,0,0];
		for ($j = 0; $j < 9; $j++) {
			if (-1 == $sudoku[$i][$j]) {
				continue;
			}
			$used[$sudoku[$i][$j]-1]++;
		}
		foreach ($used as $u) {
			if ($u > 1) {
				return false;
			}
		}
	}
	return true;
}

function validate_horizontal(&$sudoku) {
	for ($j = 0; $j < 9; $j++) {
		$used = [0,0,0,0,0,0,0,0,0];
		for ($i = 0; $i < 9; $i++) {
			if (-1 == $sudoku[$i][$j]) {
				continue;
			}
			$used[$sudoku[$i][$j]-1]++;
		}
		foreach ($used as $u) {
			if ($u > 1) {
				return false;
			}
		}
	}
	return true;
}

function validate_quad(&$sudoku) {
	for ($qi = 0; $qi < 3; $qi++) {
		for ($qj = 0; $qj < 3; $qj++) {
			$used = [0,0,0,0,0,0,0,0,0];

			for ($i = 0; $i < 3; $i++) {
				for ($j = 0; $j < 3; $j++) {
					$v = $sudoku[$qi * 3 + $i][$qj * 3 + $j];
					if (-1 == $v) {
						continue;
					}
					$used[$v-1]++;
				}
			}

			foreach ($used as $u) {
				if ($u > 1) {
					return false;
				}
			}
		}
	}

	return true;
}

function validate(&$sudoku) {
	return validate_vertical($sudoku)
		&& validate_horizontal($sudoku)
		&& validate_quad($sudoku);
}

function variants_init() {
	$var = [];
	for ($i = 0; $i < 9; $i++) {
		$var[$i] = [];
		for ($j = 0; $j < 9; $j++) {
			$var[$i][$j] = [1,2,3,4,5,6,7,8,9];
		}
	}
	return $var;
}

function variants_unset_vertical(&$var, $pi, $pj, $v) {
	for ($i = 0; $i < 9; $i++) {
		$var[$i][$pj][$v - 1] = false;
	}
}

function variants_unset_horizontal(&$var, $pi, $pj, $v) {
	for ($j = 0; $j < 9; $j++) {
		$var[$pi][$j][$v - 1] = false;
	}
}
function variants_unset_quad(&$var, $pi, $pj, $v) {
	$qi = $pi - ($pi % 3);
	$qi = $qi ? $qi / 3 : 0;

	$qj = $pj - ($pj % 3);
	$qj = $qj ? $qj / 3 : 0;

	for ($i = 0; $i < 3; $i++) {
		for ($j = 0; $j < 3; $j++) {
			$var[$qi * 3 + $i][$qj * 3 + $j][$v - 1] = false;
		}
	}
}

function variants(&$sudoku) {
	$var = variants_init();
	for ($i = 0; $i < 9; $i++) {
		for ($j = 0; $j < 9; $j++) {
			$v = $sudoku[$i][$j];
			if (-1 == $v) {
				continue;
			}
			$var[$i][$j] = [false, false, false, false, false, false, false, false, false];
			variants_unset_vertical($var, $i, $j, $v);
			variants_unset_horizontal($var, $i, $j, $v);
			variants_unset_quad($var, $i, $j, $v);
		}
	}
	variants_clean($var);
	return $var;
}

function variants_clean(&$var) {
	for ($i = 0; $i < 9; $i++) {
		for ($j = 0; $j < 9; $j++) {
			$var[$i][$j] = array_filter($var[$i][$j]);
		}
	}
}

function done(&$sudoku) {
	for ($i = 0; $i < 9; $i++) {
		for ($j = 0; $j < 9; $j++) {
			if (-1 == $sudoku[$i][$j]) {
				return false;
			}
		}
	}

	if(!validate($sudoku)) {
		return false;
	}

	echo toString($sudoku);
	die();
}

function solve11(&$sudoku) {
	$found = false;
	$var = variants($sudoku);
	for ($i = 0; $i < 9; $i++) {
		for ($j = 0; $j < 9; $j++) {
			if (count($var[$i][$j]) == 1) {
				$sudoku[$i][$j] = array_pop($var[$i][$j]);
				$found = true;
			}
		}
	}
	return $found;
}

function solve_rec($sudoku) {
//	solve($sudoku);
//	done($sudoku);
	$var = variants($sudoku);
	$min = 10;
	$mi = -1;
	$mj = -1;
	for ($i = 0; $i < 9; $i++) {
		for ($j = 0; $j < 9; $j++) {
			$c = count($var[$i][$j]);
			if (0 == $c) continue;
			if ($c < $min) {
				$min = $c;
				$mi = $i;
				$mj = $j;
			}
		}
	}
	if ($min == 10) {
		done($sudoku);
		return;
	}
	foreach ($var[$mi][$mj] as $v) {
		$sudoku[$mi][$mj] = $v;
		solve_rec($sudoku);
		$sudoku[$mi][$mj] = -1;
	}
}

function solve(&$sudoku) {
	while (solve11($sudoku));
}

$sudoku = readFromFile('in0.txt');
if (!validate($sudoku)) {
	die("Invalid sudoku");
}

echo toString($sudoku);
echo "===\n\n";
//var_dump(variants($sudoku));
solve_rec($sudoku);

