<?php
session_start();
unset($_SESSION['cLogin']);
unset($_SESSION['cNome']);
header("Location: ./");
?>