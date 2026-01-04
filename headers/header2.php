<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__."/../config/function.php"; ?>
  <meta charset="UTF-8">
  <title>Wallet Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Remix Icon -->
  <link href="https://cdn.jsdelivr.net/npm/remixicon/fonts/remixicon.css" rel="stylesheet">

  <!-- Swiper CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

  <style>
    body {
      background-color: #f8f9fa;
      padding-bottom: 70px;
    }

    .wallet-card {
      border-radius: 14px;
      padding: 20px;
      color: #fff;
      box-shadow: 0 5px 15px rgba(0,0,0,.15);
    }

    .notification-item {
      border-bottom: 1px solid #eee;
      padding: 12px 0;
    }

    .footer-nav {
      position: fixed;
      bottom: 0;
      left: 0;
      right: 0;
      background: #fff;
      border-top: 1px solid #ddd;
      display: flex;
      justify-content: space-around;
      padding: 8px 0;
      z-index: 999;
    }

    .footer-nav a {
      text-align: center;
      flex: 1;
      color: #333;
      text-decoration: none;
      font-size: 12px;
    }

    .footer-nav a i {
      display: block;
      font-size: 22px;
    }
  </style>
</head>
<body>
