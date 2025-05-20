<?php
require_once("./config/databaseConfig.php");
require_once("./routes/routesFactory.php");
require_once("./controllers/subjectsController.php"); //esta es la unica diferencia entre los tres archivos de routes

routeRequest($conn);
?>