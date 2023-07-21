<html>
<head>
    <title>Database Project</title>

    <style>
        #box {
            display: block;
            height: 200px;
            width: 100%;
        }

        body, html {
            height: 100%;
            margin: 0;
            -webkit-font-smoothing: antialiased;
            font-weight: normal;
            font-family: helvetica;
        }

        .tabs input[type=radio] {
            position: absolute;
            top: -9999px;
            left: -9999px;
        }

        .tabs {
            width: 100%;
            float: none;
            list-style: none;
            position: relative;
            padding: 0;

        }

        .tabs li {
            float: left;
        }

        .tabs label {
            display: block;
            padding: 20px 5px;
            border-radius: 2px 2px 0 0;
            color: #000d14;
            font-size: 15px;
            font-weight: normal;
            font-family: 'Roboto', helveti;
            background: rgba(204, 204, 204, 0.6);
            cursor: pointer;
            position: relative;
            top: 3px;
            -webkit-transition: all 0.2s ease-in-out;
            -moz-transition: all 0.2s ease-in-out;
            -o-transition: all 0.2s ease-in-out;
            transition: all 0.2s ease-in-out;
        }

        .tabs label:hover {
            background: rgba(128, 128, 128);
            top: 0;
        }

        [id^=tab]:checked + label {
            background: #cbcecf;
            color: black;
            top: 0;
        }

        [id^=tab]:checked ~ [id^=tab-content] {
            display: block;
        }

        .tab-content {
            z-index: 2;
            display: none;
            text-align: left;
            width: 100%;
            font-size: 15px;
            line-height: 140%;
            padding-top: 10px;
            background: #cbcecf;
            padding: 15px;
            color: black;
            position: absolute;
            top: 53px;
            left: 0;
            box-sizing: border-box;
            -webkit-animation-duration: 0.5s;
            -o-animation-duration: 0.5s;
            -moz-animation-duration: 0.5s;
            animation-duration: 0.5s;
        }

        #query_box {

            text-align: center;
            float: left;
            border-radius: 5px;
            margin: 10px;
            padding: 10px;
        }


        #queryResultBox {

            border-radius: 5px;
            width: 700px;
            margin: 10px;

        }
    </style>

</head>




<h3 style="text-align:center; color: #2F4F4F "><u>Database Systems I - COMP 6120 Final Project</u></h3>
<body>
<?php

function connectToDB()
{
	$servername = "mysql.auburn.edu";
	$username = "mzt0078";
	$password = "sql19";
	$dbname = "mzt0078db";

    return $conn = mysqli_connect($servername, $username, $password, $dbname);
}


function getTables()
{
    $con = connectToDB();
    $query = "show tables";
    $result = mysqli_query($con, $query);

    $count = 0;
    $resultsArray = array();
    while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
        array_push($resultsArray, $row[0]);
    }
    mysqli_close($con);
    return $resultsArray;
}


function selectAllFromTable($tableName)
{
    $con = connectToDB();
    $query = "select * from $tableName";
    $result = mysqli_query($con, $query);
    mysqli_close($con);
    return $result;
}

function getHtmlTable($rs)
{
    $out = '<table>';
    while ($field = $rs->fetch_field()) $out .= "<th>" . $field->name . "</th>";
    while ($linea = $rs->fetch_assoc()) {
        $out .= "<tr>";
        foreach ($linea as $valor_col) $out .= '<td>' . $valor_col . '</td>';
        $out .= "</tr>";
    }
    $out .= "</table>";
    return $out;
}

?>

<div class="container" id="box">
    <div class="main">
        <ul class="tabs">

            <?php
            $arrayOfTables = getTables();
            $arrayLength = count($arrayOfTables);

            for ($x = 0; $x < $arrayLength; $x++) {

                echo "<li><input type=\"radio\" checked name=\"tabs\" id=\"tab";
                echo $x + 1;
                echo "\">";
                echo "<label for=\"tab" . ($x + 1) . "\">";
                echo $arrayOfTables[$x];
                echo "</label><div id=\"tab-content" . ($x + 1) . "\" class=\"tab-content animated fadeIn\">";
                echo getHTMLTable(selectAllFromTable($arrayOfTables[$x]));
                echo "</div></li>";
            }
            ?>
        </ul>
    </div>
</div>

<?php
function sqlQuery($SQLQuery)
{
    if (!empty($SQLQuery) && isset($SQLQuery)) {
        $pos = stripos($SQLQuery, 'drop');
        if ($pos === false) {
            $con = connectToDB();
            mysqli_set_charset($con, "utf8");
            $query = stripslashes($SQLQuery);
            $result = mysqli_query($con, $query);
            if ($result === FALSE) {
                echo "<script>";
                echo "document.getElementById(\"queryResultBox\").style.backgroundColor=\"red\";";
				echo "document.querySelectorAll('li').forEach((li) => {
						li.addEventListener('click', (event) => {
							console.log(li.classList[1]);
							document.getElementById(\"queryResultBox\").innerHTML = \"\";
							});
						});";
                echo "</script>";
                die("Error executing statement: " . mysqli_error($con));
            } else {

                $count = 0;
                $resultsArray = array();

                echo getHtmlTable($result);

                while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                    array_push($resultsArray, $row[1]);
                }
            }
            mysqli_close($con);
            return $resultsArray;
        } else {
            echo "<script>";
            echo "document.getElementById(\"queryResultBox\").innerHTML = \"Can not use DROP!\";";
            echo "document.getElementById(\"queryResultBox\").style.backgroundColor=\"red\";";
			echo "document.querySelectorAll('li').forEach((li) => {
				li.addEventListener('click', (event) => {
					console.log(li.classList[1]);
					document.getElementById(\"queryResultBox\").innerHTML = \"\";
				});
			});";
            echo "</script>";
        }
    } else {
        echo "<script>";
        echo "document.getElementById(\"queryResultBox\").innerHTML = \"Empty Result!\";";
		echo "document.querySelectorAll('li').forEach((li) => {
				li.addEventListener('click', (event) => {
					console.log(li.classList[1]);
					document.getElementById(\"queryResultBox\").innerHTML = \"\";
				});
			});";
        echo "</script>";
    }
}

?>



<div id="query_box" >
    <form id="qry-form" role="form" method="post" action="index.php" style="margin-top: 150px">
	
        <textarea name="query" rows="10" cols="50" placeholder="Post your query here ..." ></textarea>
        <br>
        <button type="submit" class="btn">Submit</button>
    </form>
</div>
<div id="queryResultBox" style="margin-top: 400px">
    <?php
	if (!empty($_POST)) {
        $postResult = $_POST['query'];
        $result = sqlQuery(strtolower($postResult));
        $maxItemCount = sizeof($result);
        for ($index = 0; $index < $maxItemCount; $index++) {
            echo $result[$index];
        }
    }
    ?>
</div>


</body>
</html>