<?php
//Connect to mysql
$host = "cs2.mwsu.edu";             // server name
$user = "************";           // user name

$file = fopen("outfile.txt", 'w');
// Get username and password from slack
$password = "***********";   // password 
$database = "nfl_data";   // database 

// connect to mysql data base
$mysqli = mysqli_connect($host, $user, $password, $database);
if (mysqli_connect_errno($mysqli)) 
{
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
}

 // Helper function to run sql
require "query_function.php";

// Include my example functions
// "include" isn't as strong as "require" it won't error if file is missing
include "get_players1.php";
include "get_players2.php";
include "load_stat_codes.php";

// string variable
$stringvar = "";

// output header
echo"<pre>";
echo "Alex Jenny\nSQL program answers various nfl questions\n\n\n";

//-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------
// Question 1: Count number of teams a player played for.

// need id, name and the number of clubs this player has played for
        $sql = "SELECT id,name, COUNT( DISTINCT(club)) as NumClubs 
                FROM players 
                Group By name 
                ORDER BY COUNT(DISTINCT(club)) DESC
                LIMIT 10;";

        // Send it to our runQuery function with our mysqli resource variable
        $response = runQuery($mysqli, $sql);
        // headre for output
        
        // handle the response
        if ($response['success']) 
        {
            // pull the data out of the result array
            echo "Question 1: Count number of teams an individual player played for(limit 10 players).";
                $data = $response['result'];

                // create table of values
                echo "<table>";
                // column names
                echo "<tr> <td> #</td> 
                <td> Player_ID </td>
                <td>Name </td> 
                <td>#Teams </td> </tr>";

                // formatting
                echo "<tr> <td> =====</td> 
                <td> ================= </td> 
                <td> ================= </td>
                <td> ================ </td></tr>";

                $i = 1;
                foreach ($data as $row) 
                {
                        // column data
                       print("<tr><td>".$i."</td>
                        <td>".$row['id']."</td> 
                        <td>".$row['name']."</td> 
                        <td>".$row['NumClubs']."</td></tr>");
                        $i++;
                        
                }
                echo "</table>";
        }

//----------------------------------------------------------------------------------------------------------------------
// Question 2: Find the players with the highest total rushing yards by year, and limit the result to top 5.
// This means to sum up all players rushing yards per year, and list the top 5 (most yards) in your output
// rushing yards is id 10, rushing yards TD is id 11, Minus rushing yards is id 1
echo "\n\nQuestion 2: Find top 5 players with highest total rushing yards.\n";

// need players and rushing yards per year, i0 is the rushing statid
$sql = "SELECT playerid, season, Sum(yards) as AllYards
FROM players_stats 
WHERE statid = 10
GROUP BY playerid, season
ORDER BY Sum(yards) DESC
LIMIT 5;";

$response = runQuery($mysqli, $sql);

// neat error checking 
if ($response['success']) 
{
        // pull the data out of the result array
        $data = $response['result'];

        // make list of ids, needed for 2nd query
        $idList = [];
        for($j = 0; $j < 5; $j++)
        {
        $idList[] = $data[$j]['playerid'];
        
        }
        $newIds = implode("','", $idList);
        
        // need player name given playerid
       $ssql = "SELECT DISTINCT(id), name
       FROM players
       WHERE id IN ('$newIds')";     
        
        $resp2 = runQuery($mysqli, $ssql);
        $data2 = $resp2['result'];
        
        // create answer table
        echo "<table>";
        // column headers
        echo "<tr> <td> #</td> 
        <td> Player_ID </td>
         <td>Name </td> 
         <td>Year </td> 
         <td> #Yards </td> </tr>";

         // formatting
        echo "<tr> <td> =====</td> 
        <td> ================= </td> 
        <td> ================= </td>
        <td> ================ </td>
        <td> =================</td></tr>";

        $i = 1;
        // need data from 2 arrays
        foreach ($data as $row) 
        {
                foreach($data2 as $row2)
                
                if ($row['playerid'] == $row2['id']) // then we have the right name for the id
              { 
                      // print table data
                print ("<tr><td>".$i."</td>
                <td>".$row['playerid']."</td> 
                <td>".$row2['name']."</td> 
                <td>".$row['season']."</td>
                <td> ".$row['AllYards']." </td> </tr>");
                $i++;
              } 
        }
        echo "</table>";
}

//------------------------------------------------------------------------------------------------------
        echo "\n\nQuestion 3: Find The bottom 5 passing players\n";
        // Find the bottom 5 passing players per year.
        // This is the similar to previous question, just change top to bottom, and most to least.

        // need playerid, yards passed, 15 is the passing statid
        $sql = "SELECT playerid, season, Sum(yards) as AllYards
        FROM players_stats 
        WHERE statid = 15
        GROUP BY playerid, season
        ORDER BY Sum(yards) ASC
        LIMIT 5;";

        $response = runQuery($mysqli, $sql);

        // neat error checking 
        if ($response['success']) 
        {
                // pull the data out of the result array
                $data = $response['result'];        

                // make list of ids, needed for 2nd query
                $idList = [];
                for($j = 0; $j < 5; $j++)
                {
                $idList[] = $data[$j]['playerid'];
                }
                
                $newIds = implode("','", $idList);
                
                // second sql, need 
                $ssql = "SELECT DISTINCT(id), name
                FROM players
                WHERE id IN ('$newIds')";     
                
                $resp2 = runQuery($mysqli, $ssql);
                $data2 = $resp2['result'];
             
                // print data table
                echo "<table>";
                echo "<tr> <td> #</td> 
                <td> Player_ID </td>
                 <td>Name </td> 
                 <td>Year </td> 
                 <td> #Yards </td> </tr>";

                echo "<tr> <td> =====</td> 
                <td> ================= </td> 
                <td> ================= </td>
                <td> ================ </td>
                <td> =================</td></tr>";
                        
                // print column data
                $i = 1;
                foreach ($data as $row) 
                {
                        foreach($data2 as $row2)
                        
                        if ($row['playerid'] == $row2['id'])
                      { print ("<tr><td>".$i."</td>
                        <td>".$row2['id']."</td> 
                        <td>".$row2['name']."</td> 
                        <td>".$row['season']."</td>
                        <td> ".$row['AllYards']." </td> </tr>");
                        $i++;
                      } 
                }
                echo "</table>";
        }        

//--------------------------------------------------------------------------------------------

        echo "\n\n Question 4: Find the top 5 players that had the most rushes for a loss.";
        // This is not grouped by year, this is over a players career. Stat code 10 (I think)

        // need player info, yards rushed if they went backwards (loss)
        $sql = "SELECT playerid, COUNT(playerid) as rushes, SUM(yards) as numYards
        FROM `players_stats` 
        WHERE statid = 10 AND yards < 0 
        GROUP BY playerid
        ORDER BY COUNT(playerid) DESC
        LIMIT 5;";

        $response = runQuery($mysqli, $sql);

        // neat error checking
        if ($response['success']) 
        {
                // pull the data out of the result array
                $data = $response['result'];

                // make list of ids
                $idList = [];
                for($j = 0; $j < 5; $j++)
                {
                        $idList[] = $data[$j]['playerid'];
                }
                $newIds = implode("','", $idList);
                
                // second sql
                $ssql = "SELECT DISTINCT(id), name
                FROM players
                WHERE id IN ('$newIds')";     
                
                $resp2 = runQuery($mysqli, $ssql);
                $data2 = $resp2['result'];
        
        // print data table 
        echo "<table>";
        echo "<tr> <td> #</td> 
        <td> Player_ID </td>
        <td>Name </td> 
        <td>Number of Rushes </td> 
        <td> Total Yards Rushed </td> </tr>";


        echo "<tr> <td> =====</td> 
        <td> ================= </td> 
        <td> ================= </td>
        <td> ======================= </td>
        <td> =================</td></tr>";

        $i = 1;
        foreach ($data as $row) 
        {
                foreach($data2 as $row2)
                
                if ($row['playerid'] == $row2['id'])
              { print ("<tr><td>".$i."</td>
                <td>".$row2['id']."</td> 
                <td>".$row2['name']."</td> 
                <td>".$row['rushes']."</td>
                <td> ".$row['numYards']." </td> </tr>");
                $i++;
              } 
        }
        echo "</table>";

}

//---------------------------------------------------------------------------------------------
// question 5 Find the top 5 teams with the most penalties.
// This is not grouped by year, this is over a players career.
echo "\n\nQuestion 5: Find the top 5 teams with the most penalties.";
// statid: 5, 93 

// need team, number of times a team came up with a stat id of 5 or 93 which are penalties
$sql = "SELECT club, COUNT(club) as numClubs
FROM `players_stats` 
WHERE statid = 5 OR statid = 93 
GROUP BY club
ORDER BY Count(club) DESC
LIMIT 5;";

$response = runQuery($mysqli, $sql);

// neat error checking
if ($response['success']) 
{
        // pull the data out of the result array
        $data = $response['result'];
     
        // print data table
        echo "<table>";
        echo "<tr> <td> #</td> 
        <td> Team Name </td>
         <td> Number of Penalties  </td> </tr>";
        echo "<tr> <td> =====</td> 
        <td> ============= </td>
        <td> =========================</td></tr>";

        $i = 1;
        foreach ($data as $row) 
        {
                print ("<tr><td>".$i."</td>
                <td>".$row['club']."</td> 
                <td>".$row['numClubs']."</td> </tr>");
                $i++;
                
        }
        echo "</table>";
}
        
//-----------------------------------------------------------------------------------------------
// Question 6(or second 5): Find the average number of penalties per year.
// Average Penalties = Sum of all penalties per year / Total games played that year
// Output: List the top 10 seasons by highest average number of penalties.
// stat 5 and 93 are penalties
echo"\n\nQuestion 5 (the second one): Find the average number of penalties per year.";

// turns out that there are always 267 games. 
// get number of penalties per year 
$sql = "SELECT season, COUNT(statid) as penalties  
FROM `players_stats`
WHERE statid = 5 OR statid = 93
GROUP BY season
ORDER BY penalties DESC
LIMIT 10;";

$response = runQuery($mysqli, $sql);

// neat error checking
if ($response['success']) 
{
        // pull the data out of the result array
        $data = $response['result'];

        // this is the average part 
        foreach ($data as $row => $value)
        {
                $data[$row]['penalties'] = $data[$row]['penalties'] / 267;
        }
     
        // print data table
        echo "<table>";
        echo "<tr> <td> #</td> 
        <td> Season </td>
         <td> Total Penalties </td> 
         <td> Average Penalties </td> </tr>";

        echo "<tr> <td> =====</td> 
        <td> ================= </td> 
        <td> ================= </td>
        <td> ======================= </td> </tr>";

        $i = 1;
        foreach ($data as $row) 
        {       $total = $row['penalties'] * 267;
                print ("<tr><td>".$i."</td>
                <td>".$row['season']."</td> 
                <td>".$row['penalties']."</td>
                <td>".$total."</td> </tr>");
                $i++;
        }
        echo "</table>";
}

//------------------------------------------------------------------------------------------
// Question 6: Find the Team with the least amount of average plays every year.
// Average Plays is by game.
// Total Plays is per year.
// ???
// Output: List the top 10 teams by lowest average number of plays.
echo "\n\nQuestion 6: Find the team with the least amount of average plays every year";

// what I have so far
// SELECT plays.clubid , `plays`.gameid, `plays`.Count(gameid) AS numPlays, `games`.season
// FROM `plays`,  `games` 
// GROUP BY(`games'.season`)
// ORDER BY numPlays

// I do not understand how to do this problem.

//-------------------------------------------------------------------------------------------------
// Question 7: Find the top 5 players that had field goals over 40 yards.
echo "\n\nQuestion 7: Find the top 5 players that had field goals over 40 yards.";
// stat 70

// find all players with a field goal (statid 70) greater than 40 yrds
$sql = "SELECT playerid, yards
FROM `players_stats` 
WHERE statid = 70 AND yards >= 40
ORDER BY yards DESC
LIMIT 5;";

$response = runQuery($mysqli, $sql);

// neat error checking 
if ($response['success']) 
{
        // pull the data out of the result array
        $data = $response['result'];

        // make list of ids
        $idList = [];
        for($j = 0; $j < 5; $j++)
        {
        $idList[] = $data[$j]['playerid'];
        }
       //  print_r($idList);
        $newIds = implode("','", $idList);
        
        // second sql
        $ssql = "SELECT DISTINCT(id), name
        FROM players
        WHERE id IN ('$newIds')";     
        
        $resp2 = runQuery($mysqli, $ssql);
        $data2 = $resp2['result'];
     
        // print data found
        echo "<table>";
        echo "<tr> <td> #</td> 
        <td> Player_ID </td>
         <td>Name </td> 
         <td>Longest Field Goal </td> </tr>";
        echo "<tr> <td> =====</td> 
        <td> ================= </td> 
        <td> ================= </td>
        <td> ================ </td> </tr>";

        $i = 1;
        foreach ($data as $row) 
        {
                foreach($data2 as $row2)
                
                if ($row['playerid'] == $row2['id'])
              { print ("<tr> <td>".$i."</td>
                <td>".$row2['id']."</td> 
                <td>".$row2['name']."</td> 
                <td>".$row['yards']."</td> </tr>");
                $i++;
              } 
        }
        echo "</table>";
}


//----------------------------------------------------------------------------------------
// Question 8: Find the top 5 players with the shortest avg field goal length. 
echo "\n\nQuestion 8: Find the top 5 players with the shortest avg field goal length.";

// stat 70

// need player info and average field goal yards
$sql = "SELECT playerid, AVG(yards) as avgYards
FROM `players_stats` 
WHERE statid = 70 
GROUP BY playerid
ORDER BY yards ASC
LIMIT 5;";

$response = runQuery($mysqli, $sql);

// neat error checking 
if ($response['success']) 
{

        // pull the data out of the result array
        $data = $response['result'];

        // make list of ids
        $idList = [];
        for($j = 0; $j < 5; $j++)
        {
        $idList[] = $data[$j]['playerid'];
        }
       //  print_r($idList);
        $newIds = implode("','", $idList);
        
        // second sql
        $ssql = "SELECT DISTINCT(id), name
        FROM players
        WHERE id IN ('$newIds')";     
        
        $resp2 = runQuery($mysqli, $ssql);
        $data2 = $resp2['result'];
     
        // print data found
        echo "<table>";
        echo "<tr> <td> #</td> 
        <td> Player_ID </td>
         <td>Name </td> 
         <td> Average Field Goal Length </td> </tr>";

        echo "<tr> <td> =====</td> 
        <td> ================= </td> 
        <td> ================= </td> </tr>";

        $i = 1;
        foreach ($data as $row) 
        {
                foreach($data2 as $row2)
                
                if ($row['playerid'] == $row2['id'])
              { print ("<tr> <td>".$i."</td>
                <td>".$row2['id']."</td> 
                <td>".$row2['name']."</td> 
                <td>".$row['avgYards']."</td> </tr>");
                $i++;
              } 
        }
        echo "</table>";
}


//--------------------------------------------------------------------------------------
// Question 9: Rank the NFL teams by win loss percentage (worst first).
echo "\n\nQuestion 9: Rank the nfl teams by win/loss percentage.";

// need teams, wins, losses
$sql = "SELECT club, COUNT(IF(wonloss = 'won', 1, NULL)) as wins, COUNT(IF(wonloss = 'loss', 1, NULL)) as losses
FROM `game_totals` 
GROUP BY club
ORDER BY club;";

$response = runQuery($mysqli, $sql);

// neat error checking 
if ($response['success']) 
{

        // pull the data out of the result array
        $data = $response['result'];

        // make list of percents
        $perList = [];
        for($j = 0; $j < 32; $j++)
        {
        $perList[$data[$j]['club']] = $data[$j]['wins'] / $data[$j][losses];
        
        }
        asort($perList);

        // print data table
        echo "<table>";
        echo "<tr> <td> #</td> 
        <td> Club </td>
         <td>Win/Loss Percent </td> </tr>";

        echo "<tr> <td> =====</td> 
        <td> ========== </td> 
        <td> ==================== </td> </tr>";

        $i = 1;
        for($j=0; $j<32; $j++)
        {                
              $value = reset($idList);
              $key = key($idList);
                unset($idList[$key]);
              
              print ("<tr> <td>".$i."</td>
                <td>".$key."</td> 
                <td>".$value."</td> </tr>");
                $i++;
              
        }
        echo "</table>";
}


//------------------------------------------------------------------------------------
// Find the top 5 most common last names in the NFL.
echo "\n\nQuestion 10: Find the top 5 most common last names in the NFL.";

// need all possible names to count
$sql = "SELECT DISTINCT(SUBSTR(name, INSTR(name, '.'))) as lname
 FROM players 
 ORDER BY SUBSTR(name, INSTR(name, '.'))";

$response = runQuery($mysqli, $sql);

// neat error checking 
if ($response['success']) 
{

        // pull the data out of the result array
        $data = $response['result'];

        // number of occurances
        $numOcc = [];

        // count occurances
        foreach($data as $attr => $lname) 
        {

                $numOcc[$lname['lname']] = 0;
        }
        
        // need ids for player names
        $sql = "SELECT DISTINCT(id), SUBSTR(name, INSTR(name, '.')) as lname
        FROM players 
        WHERE LENGTH(SUBSTR(name, INSTR(name, '.'))) != 0
        ORDER BY SUBSTR(name, INSTR(name, '.'))";
        
        $responce = runQuery($mysqli, $sql);
        if ($responce['success'])
        {
                $data = $responce['result'];

                $j = 0;

                // actually count the occurances of each last name
                foreach ($numOcc as $name => $count) 
                {
                        $end = false;
                        while (!$end) 
                        {
                                
                                if ($name == $data[$j]['lname']) 
                                {
                                        $count++;
                                        $j++;
                                }
                                else 
                                {
                                        $numOcc[$name] = $count;

                                        $end = true;
                                }
                        }
                }
                
                
        }
        else {echo 'failue. you, not the program'; }
        arsort($numOcc);
     
        // // print data table
        echo "<table>";
        echo "<tr> <td> #</td> 
        <td> Last Name </td>
         <td> Number of Players With Last Name </td> </tr>";

        echo "<tr> <td> =====</td> 
        <td> ========== </td> 
        <td> ==================================== </td> </tr>";

        $i = 1;
        for($j=0; $j<5; $j++)
        {                
              $value = reset($numOcc);
              $key = key($numOcc);
                unset($numOcc[$key]);
              
              print ("<tr> <td>".$i."</td>
                <td>".$key."</td> 
                <td>".$value."</td> </tr>");
                $i++;
              
        }
        echo "</table>";
      fwrite($file, $stringvar);
}

