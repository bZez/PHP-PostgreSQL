<link rel="stylesheet" href="assets/css/bootstrap.min.css">
<link rel="stylesheet" href="main.css">
<body class="m-5 bg-dark">
<div class="container">
    <?php
    include "config.php";
    // Connexion, sélection de la base de données
    $dbconn = pg_connect("host=$host dbname=$db user=$user password=$pwd")
    or die('Connexion impossible : ' . pg_last_error());

    //Liste des schemas
    $get_schemas = "select pg.nspname from pg_catalog.pg_namespace pg WHERE pg.nspname NOT LIKE '%pg%' AND pg.nspname NOT LIKE '%info%'";
    $schemas = pg_query($get_schemas) or die('Échec de la requête : ' . pg_last_error());

    ?>
    <form id='schema' method='post' action=''>
        <select name='schema' class='custom-select bg-dark text-white border-0 shadow '>
            <option>Select schema...</option>
            <?php
            while ($schema = pg_fetch_object($schemas)) {
                foreach ($schema as $sch_name => $sch_value) {
                    if ($sch_value) {
                        echo "<option value='$sch_value'>$sch_value</option>";
                    }
                }
            }
            ?>
        </select>
    </form>
    <?php

    if (isset($_POST['schema'])) {
        $selected_schema = '' . $_POST['schema'] . '';
    } else {
        $selected_schema = 'public';
    }

    // Exécution de la requête SQL
    $get_tables = "SELECT * FROM information_schema.tables WHERE table_schema = '$selected_schema' AND TABLE_TYPE = 'BASE TABLE' ORDER BY TABLE_NAME";
    $tables = pg_query($get_tables) or die('Échec de la requête : ' . pg_last_error());

    $tables_count = 0; ?>
    <table class='table text-center ml-auto mr-auto mt-3 table-striped table-hover table-dark shadow table-bordered'>
        <thead class='thead-dark'>
        <tr>
            <th>Table_catalog</th>
            <th>Table_schema</th>
            <th>Table_name</th>
            <th>Table_type</th>
            <th>Is_insertable_into</th>
            <th>Is_type</th>
        </tr>
        </thead>
        <tbody>
        <?php
        while ($table = pg_fetch_object($tables)) {
            echo "<tr>";

            foreach ($table as $t_name => $t_value) {
                if ($t_value) {
                    echo "<td>$t_value</td>";

                    /*
                                $get_columns = "SELECT * FROM information_schema.columns WHERE table_schema ='gracethd' AND table_name = '$table->table_name'";
                                $columns = pg_query($get_columns) or die('Échec de la requête : ' . pg_last_error());
                                while ($column = pg_fetch_object($columns)) {
                                    echo "\t<tr>\n";
                                        foreach($column as $c_name => $c_value) {
                                            if ($c_value) {
                                                echo "\t\t<td>$c_value</td>\n";
                                            }

                                        }
                                    echo "\t</tr>\n";
                                }

                    */
                }
            }
            echo "</tr>";
            $tables_count++;
        }
        // Libère le résultat
        pg_free_result($tables);

        // Ferme la connexion
        pg_close($dbconn);
        ?>
        </tbody>
        <div class='m-auto bg-light shadow shadow-inset rounded p-3 text-center'>
            <h1><?php echo $tables_count ?> tables in <?php echo $selected_schema ?></h1>
        </div>
    </table>
</div>
<script src="assets/js/jquery-3.3.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script>
    $(function () {
        $('#schema').change(function () {
            if ($('select').val()) {
                $('#schema').submit();
            }
        });
    })
</script>
</body>

