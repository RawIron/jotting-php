<?

$nestedData = array(
    "name" => "aName",
    "address" => array("street" => "aStreet"),
    );

print_r(json_encode($nestedData));
