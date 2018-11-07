<?php
    session_start();

    require 'database.php';
    require 'member.class.php';

    $member = new Member($db);