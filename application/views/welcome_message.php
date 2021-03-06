<?php
    defined('BASEPATH') OR exit('No direct script access allowed');
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Course Online - RPL & GDC</title>
        <link rel="icon" href="https://rplgdc.netlify.com/favicon.ico" type="image/x-icon">
        <style>
        body, html {
          height: 100%;
          margin: 0;
        }

        .bgimg {
          background-image: url('<?=base_url()?>/assets/forestbridge.jpg');
          height: 100%;
          background-position: center;
          background-size: cover;
          position: relative;
          color: white;
          font-family: "Courier New", Courier, monospace;
          font-size: 25px;
        }

        .topleft {
          position: absolute;
          top: 0;
          left: 16px;
        }

        .bottomleft {
          position: absolute;
          bottom: 0;
          left: 16px;
        }

        .middle {
          position: absolute;
          top: 50%;
          left: 50%;
          transform: translate(-50%, -50%);
          text-align: center;
        }

        hr {
          margin: auto;
          width: 40%;
        }
        </style>
    </head>
    <body>
        <div class="bgimg">
          <div class="topleft">
            <p><img src="<?=base_url()?>/assets/index.png"> Course Online</p>
          </div>
          <div class="middle">
            <h1>COMING SOON</h1>
            <hr>
            <p>#KAU_MALAS_KAU_MISKIN</p>
          </div>
          <div class="bottomleft">
            <p>&copy; 2020 - Course Online Team</p>
          </div>
        </div>
    </body>
</html>