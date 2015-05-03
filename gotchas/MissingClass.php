<?php

if (class_exists("Member")) {
  print("found Member");
} else {
  print("Member not found");
}

try {
  $member = new Member();
}
catch (Exception $e) {
}
