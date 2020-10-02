<?php
/**
 ***********************************************************************************************
 * Gemeinsame Funktionen fuer das Admidio-Plugin sudokuhelper
 *
 * @copyright 2004-2019 rmb
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License v2.0 only
 ***********************************************************************************************
 */

require_once(__DIR__ . '/../../adm_program/system/common.php');

if (!defined('PLUGIN_FOLDER'))
{
	define('PLUGIN_FOLDER', '/'.substr(__DIR__,strrpos(__DIR__,DIRECTORY_SEPARATOR)+1));
}

/**
 * Funktion erzeugt einen Button mit Link
 * @param   string  $row  Zeile des Buttons
 * @param   string  $col  Spalte des Buttons
 * @return  string  html-Code mit Link für einen Button
 */
function generate_button($row, $col)
{
    $ret = '';
    $text = '';
   
    if ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] == 0)
    {
        $dist = '';
        $anz = 0;
        $fontsize = '8px';
        
        foreach ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] as $key => $data)
        {
            if ($data)
            {
                $anz++;
            }
        }
        
        if ($anz < 6)
        {
            $dist = ' ';
        	$fontsize = '10px';
        }
        if ($anz < 3)
        {
        	$fontsize = '12px';
        }

        foreach ($_SESSION['pSudokuHelper']['sudoku'][$row][$col]['possible'] as $key => $data)
        {
            if ($data)
            {
                $text .= $key.$dist;
            }
        }

        $ret .= '<button class="button" data-toggle="modal" data-target="#admidio_modal"  style= "height: 60px;width:60px;font-size: '.$fontsize.' " href="'.safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/assign.php', array('row' => $row, 'col' => $col)) . '">'. $text .'</button>';
    }
    else
    {
        $ret .= '<button class="button" data-toggle="modal" data-target="#admidio_modal"  style= "height: 60px;width:60px;font-size: 40px" href="'.safeUrl(ADMIDIO_URL . FOLDER_PLUGINS . PLUGIN_FOLDER .'/assign.php', array('row' => $row, 'col' => $col)) . '">'. $_SESSION['pSudokuHelper']['sudoku'][$row][$col]['set'] .'</button>';
    }
    
    return $ret;
}

/**
 * Funktion gibt den Startindex eines Neunerblocks zurück,
 * z.B. Zeile oder Spalte = 5, ergibt Startindex = 4,
 * z.B. Zeile oder Spalte = 9, ergibt Startindex = 7
 * (Info: Endindex ist Startindex + 2)
 * @param   int  $field   Feld (=Zeile oder Spalte)
 * @return  int  Startindex für diesen Neunerblock
 */
function novum($field)
{
    $ret = 0;
    
    if ($field < 4)
    {
        $ret = $field -($field-1);
    }
    elseif ($field > 6)
    {
        $ret = $field -($field-7);
    }
    else
    {
        $ret = $field -($field-4);
    }
    return $ret;
}

/**
 * Funktion erzeugt einen Button mit Link eingebettet in <tr><td> einer Tabelle
 * @param   string  $url  URL des Links
 * @param   string  $text  Anzeigetext des Buttons
 * @return  string  html-Code mit Link für einen Button 
 */
function function_button($url, $text)
{
    $ret = '<tr>';
    $ret .= '<td>';
    $ret .= '<button type="button" class="btn btn-default" onclick="window.location.href=\''.$url.'\'">'.$text.'</button>';
    $ret .= '</td>';
    $ret .= '</tr>';
    
    return $ret;
}

/**
 * Funktion sucht Zahlen
 * z.B. wenn $anz=1, dann wird 2 gefunden in 12457 145 458 458
 * z.B. wenn $anz=2, dann wird 35 gefunden in 123458 2687 23458 478 4789
 * @param   int  $anz  Anzahl der zu suchenden Zahlen
 * @param   array  $arbArray  das übergebene Array in dem die Zahlen gesucht werden
 * @return  array  Array mit den gefundenen Zahlen
 */
function search_numbers($anz, $arbArray)
{
    $ret = array();
    $foundArray = array();
    
    // im ersten Schritt die Zahlen löschen, deren (Gesamt)Anzahl nicht stimmt
    // wenn z.B. nach Pärchen gesucht wird ($anz=2), dann darf eine Zahl (1 bis 9) nur 2x vorkommen
    for ($possible = 1; $possible < 10; $possible++)
    {
        $possible_count = 0;
        $foundArray[$possible] = '';
        
        for ($i = 1; $i < 10; $i++)
        {
            if (!$arbArray[$i][$possible])
            {
                continue;
            }
            else
            {
                $possible_count++;
                $foundArray[$possible] .= $i;
            }
        }
        
        if ($possible_count <> $anz)
        {
            for ($i = 1; $i < 10; $i++)
            {
                $arbArray[$i][$possible] = false;
            }
            unset($foundArray[$possible]);
        }
    }
    
    $tempArray = array_count_values($foundArray);
    
    // jetzt die Zahlen löschen, die nicht im selben "Kästchen" sind
    // wenn z.B. nach Pärchen gesucht wird, müssen die Zahlen 35 im selben Kästchen sein 123547 3578 (=OK), 12378 1578 123578 (=NEIN)
    foreach ($tempArray as $colFound => $count)
    {
        if ($count <> $anz)
        {
            foreach (array_keys($foundArray, $colFound) as $key)
            {
                unset($foundArray[$key]);
            }
        }
    }
    
    // jetzt das Rückgabearray zusammensetzen
    if (sizeof($foundArray) > 0)
    {
        foreach ($foundArray as $key => $data)
        {
            $ret[$data][] = $key;
        }
    }
    
    return $ret;
}

/**
 * Funktion erzeugt eine Leerzeile in einer Tabelle eingebettet in <tr><td> einer Tabelle
 * @param   none
 * @return  string  html-Code mit Link für einen Button
 */
function emptyLine()
{
    $ret = '<tr>';
    $ret .= '<td>';
    $ret .= '&nbsp';
    $ret .= '</td>';
    $ret .= '</tr>';
    
    return $ret;
}

/**
 * Funktion initialisiert ein neues Spiel
 * @param   none
 */
function initSudoku()
{
    $_SESSION['pSudokuHelper'] = array();
    $_SESSION['pSudokuHelper']['backup'] = array();
    
    for ($row = 1; $row < 10; $row++)
    {
        for ($col = 1; $col < 10; $col++)
        {
            $_SESSION['pSudokuHelper']['sudoku'][$row][$col] = array('possible' => array_fill(1,9,true), 'set' => 0);
        }
    }
}