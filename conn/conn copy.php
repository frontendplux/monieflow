<?php
include __DIR__."/config.php";
$conn=new mysqli(db_host,db_user,db_pass) or die('unable to connect');
$conn->query("create database if not exists ". db_name);
$conn->select_db(db_name);