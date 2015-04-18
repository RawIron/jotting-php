<?php

$emp = array();
//print_r($emp);

$rset = array();
$rset[] = 'hallo';
$rset[] = true;
//print_r($rset);

$nset = array();
$nset = array('color' => 'red');
$nset[] = array('thing' => 45, 'result' => 1);
$nset[] = array('thing' => 47, 'result' => 0);
//print_r($nset);
//echo count($nset) . '\n';

$kv = array('step' => 'buy');
//print_r($kv);

$v = 'building';
$n = null;

// Multidimensional array ->
$arvore = array();
$arvore['1'] = array();
$arvore['1']['1.1'] = array('1.1.1', '1.1.2', '1.1.3');
$arvore['1']['1.2'] = array('1.2.1', '1.2.2', '1.2.3');
$arvore['1']['1.3'] = array('1.3.1', '1.3.2', '1.3.3');
$arvore['2'] = array();
$arvore['2']['2.1'] = array('2.1.1', '2.1.2', '2.1.3');
$arvore['2']['2.2'] = array('2.2.1', '2.2.2', '2.2.3');
$arvore['2']['2.3'] = array('2.3.1', '2.3.2', '2.3.3');
$arvore['3'] = array();
$arvore['3']['3.1'] = array('3.1.1', '3.1.2', '3.1.3');
$arvore['3']['3.2'] = array('3.2.1', '3.2.2', '3.2.3');
$arvore['3']['3.3'] = array('3.3.1', '3.3.2'=>array('3.3.2.1', '3.3.2.2'), '3.3.3');
// <- 



function is_assoc($array) {
    return (is_array($array) && (count($array)==0 || count($array) === count(array_diff_key($array, array_keys(array_keys($array))) )));
} 

function nestedArrayKeys($array)
{
	if (is_array($array)) {
		return array_keys(array_keys($array));
		//return array_keys($array);
	}	
}


function array_extract($array, $extract_type = 1)
{
    foreach ( $array as $key => $value )
    {
        if ( $extract_type == 1 && is_string($key) )
        {
            // delete string keys
            unset($array[$key]);
        }
        elseif ( $extract_type == 2 && is_int($key) )
        {
            // delete integer keys
            unset($array[$key]);
        }
    }

    return $array;
}

function test($var)
{
	echo is_assoc($var) ? "I'm an assoc array.\n" : "I'm not an assoc array.\n";
}

function testNestedKeys($array)
{
	print_r(nestedArrayKeys($array));
}



test($emp);
test($rset);
test($nset);
test($kv);
test($v);
test($n);
test($u);

testNestedKeys($emp);
testNestedKeys($rset);
testNestedKeys($nset);
testNestedKeys($kv);
testNestedKeys($v);
testNestedKeys($n);
testNestedKeys($u);
