<?php
$day = ["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"]; 
$month = [["janvier","Jan"], ["février","Fév"], ["mars","Mar"], ["avril","Avr"], ["mai","Mai"], ["juin","Juin"], ["juillet","Juil"], ["août","Août"], ["septembre","Sept"], ["octobre","Oct"], ["novembre","Nov"], ["décembre","Déc"]]; 
// Now
$date = explode('|', date("w|d|n|Y"));
// Given time
$timestamp = time () ;

function nav_item (string $lien, string $titre):string 
{
    return <<<HTML
        <li class="nav-item">
            <a class="nav-link" aria-current="page" href="$lien">$titre</a>
        </li>
HTML;
}

function nav_subitem (string $lien, string $titre):string 
{
    return <<<HTML
        <a class="dropdown-item" href="$lien">$titre</a>
HTML;
}
?>