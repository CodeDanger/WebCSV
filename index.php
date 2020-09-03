<?php
ini_set('display_errors', 1); ini_set('display_startup_errors', 1); error_reporting(E_ALL); 

include __DIR__."/CSVEngine.php";

$csv = new CSV("Manage Orders File");
$col1 = $csv->addColumn("ID");
$col2 = $csv->addColumn("Name");
$col3 = $csv->addColumn("Email");
$col4 = $csv->addColumn("Password");
$col5 = $csv->addColumn("Action");
$col6 = $csv->addColumn("Action2");
$col7 = $csv->addColumn("Action3");
$col8 = $csv->addColumn("Action4");

$csv->addItem("1",$col1);
$csv->addItem("test1Name",$col2);
$csv->addItem("test1@example.com",$col3);
$csv->addItem("test1Password",$col4);
$csv->addItem("<img src='https://via.placeholder.com/350x150' alt='200x200'>",$col5);
$csv->addItem("<button style = 'background:blue;'>Action Button</button>",$col6);
$csv->addItem("<button style = 'background:blue;'>Action Button</button>",$col7);
$csv->addItem("<button style = 'background:blue;'>Action Button</button>",$col6);

$csv->display();