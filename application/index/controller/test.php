<?php
$html = file_get_contents("https://astro-app.net/horoscope.php?lang=EN&d=31&m=12&y=1999&h=18&min=30&sec=99&t=0&mday=2&mmonth=1&myear=1999&mhour=0&mmin=0&brg=11&brm=39&lng=92&lnm=43&country=inIndia&rgn=0100530Andaman and Nicobar Islands&mro=Aberdeen&city=Aberdeen&id=0&affid=000&sex=1&name=noice&mp=1&dp=17&yp=2019");
$p = "/<td align='left'>([\s\S]*)<\/td>/";
preg_match($p, $html, $match);
var_dump($match);die;