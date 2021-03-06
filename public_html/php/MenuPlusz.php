<?php

/**
 *
 * @author   Szabó Máté, Guti Patrik, Bárczi Dávid 
 */

function getMenuPluszForm() {
    global $MySqliLink, $MenuPlTartalom;	
    $HTMLkod  = '';
    if ($_SESSION['AktFelhasznalo'.'FSzint']>5)  { // FSzint-et növelni, ha működik a felhasználókezelés!!!  
        $SelectStr = "SELECT * FROM MenuPlusz";
        $result    = mysqli_query($MySqliLink, $SelectStr) OR die("Hiba sKTT 01");
        $rowDB     = mysqli_num_rows($result); 
        if ($rowDB > 0) {
            for ($i = 0; $i < 10; $i++){
                $row = mysqli_fetch_array($result);
                $MenuPlTartalom['id']             = $row['id'];
                $MenuPlTartalom['MenuPlNev']      = $row['MenuPlNev'];
                $MenuPlTartalom['MenuPlTartalom'] = $row['MenuPlTartalom'];
                $MenuPlTartalom['MenuPlPrioritas']= $row['MenuPlPrioritas'];
                $MenuPlTomb[]                     = $MenuPlTartalom;
            }
            mysqli_free_result($result);
            $HTMLkod .= "<div id='divModMenuPlForm' >\n";
            $HTMLkod .= "<form action='?f0=MenuPlusz' method='post' id='formModMenuPlForm'>\n";
            $HTMLkod .= "<h2>".U_MENUKIEG_INFO_BLOKK."</h2>\n";

            for ($i = 0; $i < 10; $i++){
                $id             = $MenuPlTomb[$i]['id'];
                $MenuPlNev       = $MenuPlTomb[$i]['MenuPlNev'];
                $MenuPlTartalom  = $MenuPlTomb[$i]['MenuPlTartalom'];
                $MenuPlPrioritas = $MenuPlTomb[$i]['MenuPlPrioritas'];

                $HTMLkod .= "<div class='divMenuPlElem'>\n ";
               // $HTMLkod .= "<p class='pMenuPlid'>".$i.". rekord</p>\n ";    
                $j = $i+1;
                $HTMLkod .= "<fieldset> <legend>".$j.". ".U_KIEGT_BLOKK_ADATOK."</legend>";

                //Kiegészítő tartalom neve
                $HTMLkod .= "<p class='pModMenuPlNev'><label for='ModMenuPlNev_$i' class='label_1'>".U_NEV.":</label><br>\n ";
                $HTMLkod .= "<input type='text' name='ModMenuPlNev_$i' id='ModMenuPlNev_$i' placeholder='".U_NEV."' value='$MenuPlNev' size='40'></p>\n"; 

                //Kiegészítő tartalom tartalma
                $HTMLkod .= "<p class='pModMenuPlTartalom'><label for='ModMenuPlTartalom_$i' class='label_1'>".U_TARTALOM.":</label><br>\n ";
                $HTMLkod .= "<textarea type='text' name='ModMenuPlTartalom_$i' id='ModMenuPlTartalom_$i' placeholder='".U_TARTALOM."' 
                             rows='4' cols='60'>$MenuPlTartalom</textarea></p>\n"; 

                //Kiegészítő tartalom prioritása
                $HTMLkod .= "<p class='pModMenuPlPrioritas'><label for='ModMenuPlPrioritas_$i' class='label_1'>".U_PRIORITAS.":</label>\n ";
                $HTMLkod .= "<input type='number' name='ModMenuPlPrioritas_$i' id='ModMenuPlPrioritas_$i' min='0' max='9' step='1' value='$MenuPlPrioritas'></p>\n";  

                //Törlésre jelölés
                $HTMLkod .= "<p class='pTorolMP'><label for='TorolMenuPl_$i' class='label_1'>".U_TORTLES.":</label>\n ";
                $HTMLkod .= "<input type='checkbox' name='TorolMenuPlTartalom_$i' id='TorolMenuPl_$i'></p>\n";

                //id
                $HTMLkod .= "<input type='hidden' name='ModMPid_$i' id='ModMPid_$i' value='$id'>\n";
                $HTMLkod .= "</fieldset>";
                $HTMLkod .= "</div>\n ";
            }        
            //Submit
            $HTMLkod .= "<br style='clear:both;float:none;'>\n";
            $HTMLkod .= "<input type='submit' name='submitMenuPlTartalom' id='submitMenuPlTartalom' value='".U_BTN_MODOSITAS."'>\n";
            $HTMLkod .= "</form>\n";
            $HTMLkod .= "</div>\n";      
        }
    }
    return $HTMLkod; 
    
}

function setMenuPlusz() {    
    global $MySqliLink;
    $ErrorStr        = "";  
    $MenuPlNev       = "";
    $MenuPlTartalom  = "";
    $MenuPlPrioritas = 0;
    if ($_SESSION['AktFelhasznalo'.'FSzint']>5)  { 
        if (isset($_POST['submitMenuPlTartalom'])) { 
            for ($i = 0; $i < 10; $i++){
                $id = INT_post($_POST["ModMPid_$i"]);
                if (!isset($_POST["TorolMenuPlTartalom_$i"])){
                    if (isset($_POST["ModMenuPlNev_$i"])) {
                        $MenuPlNev = test_post($_POST["ModMenuPlNev_$i"]);
                    }
                    if (isset($_POST["ModMenuPlTartalom_$i"]))  {
                        $MenuPlTartalom  = SQL_post($_POST["ModMenuPlTartalom_$i"]);
                    }
                    if (isset($_POST["ModMenuPlPrioritas_$i"])) {
                        $MenuPlPrioritas = INT_post($_POST["ModMenuPlPrioritas_$i"]);
                    }

                    $UpdateStr = "UPDATE MenuPlusz SET
                                    MenuPlNev       = '$MenuPlNev',
                                    MenuPlTartalom  = '$MenuPlTartalom',
                                    MenuPlPrioritas =  $MenuPlPrioritas
                                    WHERE id = $id"; //echo "<h1>$UpdateStr</h1>";
                    mysqli_query($MySqliLink,$UpdateStr) OR die("Hiba uUKT 2a"); 
                } else {
                    $UpdateStr = "UPDATE MenuPlusz SET
                                    MenuPlNev       = '',
                                    MenuPlTartalom  = '',
                                    MenuPlPrioritas =  0
                                    WHERE id = $id"; 
                    mysqli_query($MySqliLink,$UpdateStr) OR die("Hiba uUKT 2b");
                }
            }
        }    
    }    
    return $ErrorStr;
    
}

function getMenuPluszHTML() {
    global $MySqliLink, $AlapAdatok, $TisztaOURL, $Aktoldal;
    $HTMLkod         = '';
    $FacebookURL     = '';
    //https://www.facebook.com/w3suli.blogmotor/?fref=ts   
    //echo "<h1>A:".$Aktoldal['OTipus']."</h1>";
    if (($AlapAdatok['FacebookOK']==2) || (($AlapAdatok['FacebookOK']==1)&& ($Aktoldal['OTipus'])==0)){
        $FacebookURL     = $AlapAdatok['FacebookURL'];
        if ($FacebookURL=='') {$FacebookURL=$TisztaOURL;}
        $HTMLkod    .= " <div id='fb-likeKulso'>
        <div class='fb-like' data-href='$FacebookURL' 
        data-width='290' data-layout='standard' data-action='like' data-show-faces='true' data-share='true'></div>
        </div>
    ";}  
    
    
    
    $SelectStr = "SELECT * FROM MenuPlusz WHERE MenuPlPrioritas>0 ORDER BY MenuPlPrioritas DESC";
    $result    = mysqli_query($MySqliLink, $SelectStr) OR die("Hiba sKTT 01");
    $rowDB     = mysqli_num_rows($result); 
    if ($rowDB > 0) { 
        while ($row = mysqli_fetch_array($result)){
            if ($row['MenuPlTartalom']!=''){
                $HTMLkod .= "<div class ='divMenuPlKulso'>\n";
                if ($row['MenuPlNev']!='') {$HTMLkod .= "<h2>".$row['MenuPlNev']."</h2>\n";}
                $HTMLkod .= "<div class = 'divMenuPlT'>".$row['MenuPlTartalom']."\n";
                $HTMLkod .= "</div></div>\n";
            }
        }
        mysqli_free_result($result);
    }
    $HTMLkod .= " <h2>".U_MENUPL_MOTOR.":</h2> 
                 <ul class='Ul1'><li class='M1'><a href='https://w3suli.hu/'>W3Suli blogmotor</a></li></ul>\n";
    $HTMLkod .= " <h2>".U_MENUPL_TANAGYAG.":</h2> 
                 <ul class='Ul1'><li class='M1'><a href='http://webfejlesztes.gtportal.eu/'>Webáruház készítés</a></li></ul>\n";
    return $HTMLkod;
}

?>

