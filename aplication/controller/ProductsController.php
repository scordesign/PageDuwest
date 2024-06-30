<?php
class Products
{


    public function __construct()
    {
    }

    public function deleteDocs(): string
    {
        $returnFields = array();
        try {


            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();


            $id = $_POST["id"];
            $docName = $_POST["docName"];
            $type = $_POST["type"];



            $consulta = "SELECT * FROM products WHERE id = :id ";

            // Preparar la consulta
            $stmt = $pdo->prepare($consulta);

            // Asignar valores a los parámetros (en este caso, solo uno)

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Ejecutar la consulta
            $stmt->execute();
            // Obtener los resultados
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            $files = "";
            if ($type === 0 || $type === "0") {
                $files = $resultado['listDocs'];
            } else {
                $files = $resultado['listImg'];
            }


            $files = str_contains(str_replace("\\", "", $files), "\"" . $docName . "\"") ? str_replace("\"" . $docName . "\"", "", str_replace("\\", "", $files)) : $files;
            $files = str_contains($files, '[,') ? str_replace("[,", "[", $files) : $files;
            $files = str_ends_with($files, ',]') ? str_replace(",]", "]", $files) : $files;
            $files = str_contains($files, ',,') ? str_replace(",,", ",", $files) : $files;


            unlink(str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/" . $docName);

            $sSql = "";

            if ($type === 0 || $type === "0") {
                $sSql = "update products set `listDocs` = :files where `id` = :id ";
            } else {
                $sSql = "update products set `listImg` = :files where `id` = :id ";
            }

            $stmt = $pdo->prepare($sSql);

            $stmt->bindParam(':files', $files);
            $stmt->bindParam(':id', $id);


            // Ejecutar la sentencia SQL con los valores correspondientes
            $stmt->execute();



            $returnFields["status"] = 200;
            $returnFields["message"] = "Registrado correctamente";

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);

        } catch (\Throwable $e) {
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);
        }
        // Iterar sobre el resultado
    }


    public function editProducts(): string
    {
        $returnFields = array();
        try {
            $filesNames = array();
            $imagesNames = array();


            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();

            $files = $_FILES["files"];
            $images = $_FILES["images"];
            $logo = $_FILES["logo"];

            if ($_POST["name"] === null) {
                $returnFields["status"] = 406;
                $returnFields["message"] = "Nombre de producto requerid";
                $return = json_encode($returnFields);
                return $return;
            }


            $id = $_POST["id"];
            $filters = $_POST["filters"] === null ? "" : substr($_POST["filters"], 0, strlen($_POST["filters"]) - 1);
            $description = $_POST["description"] === null ? "" : $_POST["description"];
            $name = $_POST["name"] === null ? "" : $_POST["name"];
            $section = $_POST["section"] === null ? "" : $_POST["section"];
            $amount = $_POST["amount"] === null ? "" : substr($_POST["amount"], 0, strlen($_POST["amount"]) - 1);
            $amountOther = $_POST["amountOther"] === null ? "" : $_POST["amountOther"];

            $filters = str_starts_with($filters, ',') ? substr($filters, 1) : $filters;
            $amount = str_starts_with($amount, ',') ? substr($amount, 1) : $amount;


            $consulta = "SELECT * FROM products WHERE id = :id ";

            // Preparar la consulta
            $stmt = $pdo->prepare($consulta);

            // Asignar valores a los parámetros (en este caso, solo uno)

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Ejecutar la consulta
            $stmt->execute();
            // Obtener los resultados
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];
            $logopatch = $resultado["logo"] == null ? "" : $resultado["logo"];

            // var_dump($logo);

            if (($logo["name"] == null ?"": $logo["name"]) !== "" ) {
                if ($logopatch !== "") {
                    unlink(str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/" . $logopatch);
                }
                $directoryLogo = str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/img/prueba/" . $name . "/images/logo";
                if (!is_dir($directoryLogo)) {
                    // Crear la carpeta con permisos 0777 (lectura, escritura y ejecución para todos)
                    mkdir($directoryLogo, 0777, true);

                    // Establecer permisos adicionales si es necesario
                    chmod($directoryLogo, 0777);
                }
                $rutaArchivoLogo = "";

                if ($logo !== null) {
                    // Mover archivo al directorio deseado
                    $rutaArchivoLogo = $directoryLogo . "/" . basename($logo["name"]);
                    move_uploaded_file($logo["tmp_name"], $rutaArchivoLogo);
                    $logopatch = "/img/prueba/" . $name . "/images/logo/" . basename($logo["name"]);
                    // Agregar nombre del archivo a la lista
                }
            }

            $imagesNamesString = str_replace("\\", "", $resultado['listImg']);
            if ($images !== null || !empty($images)) {
                $directoryimage = str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/img/prueba/" . $name . "/images";
                if (!is_dir($directoryimage)) {
                    // Crear la carpeta con permisos 0777 (lectura, escritura y ejecución para todos)
                    mkdir($directoryimage, 0777, true);

                    // Establecer permisos adicionales si es necesario
                    chmod($directoryimage, 0777);
                }
                foreach ($images["name"] as $key => $nombre) {
                    // Mover archivo al directorio deseado
                    if ($nombre != null || $nombre != "") {
                        $rutaArchivo = $directoryimage . "/" . basename($images["name"][$key]);
                        move_uploaded_file($images["tmp_name"][$key], $rutaArchivo);
                        // Agregar nombre del archivo a la lista
                        $imagesNames[] = /*$directoryimage .*/ "img/prueba/" . $name . "/images/" . basename($images["name"][$key]);
                    }
                }
                $imagesNamesString = substr(str_replace("\\", "", json_encode($imagesNames)), 0, strlen(str_replace("\\", "", json_encode($imagesNames))) - 1) . "," . substr(str_replace("\\", "", $resultado['listImg']), 1);
                $imagesNamesString = str_contains($imagesNamesString, '[,') ? str_replace("[,", "[", $imagesNamesString) : $imagesNamesString;
                $imagesNamesString = str_ends_with($imagesNamesString, ',]') ? str_replace(",]", "]", $imagesNamesString) : $imagesNamesString;
                $imagesNamesString = str_contains($imagesNamesString, ',,') ? str_replace(",,", ",", $imagesNamesString) : $imagesNamesString;
            }
            $filesNamesString = str_replace("\\", "", $resultado['listDocs']);
            if ($files !== null || !empty($files)) {
                $directoryFile = str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/img/prueba/" . $name . "/files";

                if (!is_dir($directoryFile)) {
                    // Crear la carpeta con permisos 0777 (lectura, escritura y ejecución para todos)
                    mkdir($directoryFile, 0777, true);

                    // Establecer permisos adicionales si es necesario
                    chmod($directoryFile, 0777);
                }

                foreach ($files["name"] as $key => $nombre) {
                    if ($nombre != null || $nombre != "") {
                        // Mover archivo al directorio deseado
                        $rutaArchivo = $directoryFile . "/" . basename($files["name"][$key]);
                        move_uploaded_file($files["tmp_name"][$key], $rutaArchivo);
                        // Agregar nombre del archivo a la lista
                        $filesNames[] =/* $directoryFile .*/ "img/prueba/" . $name . "/files/" . basename($files["name"][$key]);
                    }

                }
                $filesNamesString = substr(str_replace("\\", "", json_encode($filesNames)), 0, strlen(str_replace("\\", "", json_encode($filesNames))) - 1) . "," . substr(str_replace("\\", "", $resultado['listDocs']), 1);
                $filesNamesString = str_contains($filesNamesString, '[,') ? str_replace("[,", "[", $filesNamesString) : $filesNamesString;
                $filesNamesString = str_ends_with($filesNamesString, ',]') ? str_replace(",]", "]", $filesNamesString) : $filesNamesString;
                $filesNamesString = str_contains($filesNamesString, ',,') ? str_replace(",,", ",", $filesNamesString) : $filesNamesString;
            }



            $stmt = $pdo->prepare("update products set `name` =:name,`description` =:description,`listImg` =:listImg,`listDocs` =:listDocs,`filters` =:filters,`section` =:section,`amount` = :amount ,`amountOther` =:amountOther,`logo` =:logo where id =:id");


            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':listImg', $imagesNamesString);
            $stmt->bindParam(':listDocs', $filesNamesString);
            $stmt->bindParam(':filters', $filters);
            $stmt->bindParam(':section', $section);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':amountOther', $amountOther);
            $stmt->bindParam(':logo', $logopatch);
            $stmt->bindParam(':id', $id);
            // Ejecutar la sentencia SQL con los valores correspondientes
            $stmt->execute();



            $returnFields["status"] = 200;
            $returnFields["message"] = "Editado correctamente";

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);

        } catch (\Throwable $e) {
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);
        }
        // Iterar sobre el resultado
    }

    public function deleteProducts(): string
    {
        $returnFields = array();
        $imgList = array();
        $fileList = array();
        try {



            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();



            $id = $_POST["id"];




            $consulta = "SELECT * FROM products WHERE id = :id ";

            // Preparar la consulta
            $stmt = $pdo->prepare($consulta);

            // Asignar valores a los parámetros (en este caso, solo uno)

            $stmt->bindParam(':id', $id, PDO::PARAM_INT);

            // Ejecutar la consulta
            $stmt->execute();
            // Obtener los resultados
            $resultado = $stmt->fetchAll(PDO::FETCH_ASSOC)[0];

            $imgList = json_decode(str_replace("\\", "", $resultado['listImg']), true);
            $fileList = json_decode(str_replace("\\", "", $resultado['listDocs']), true);


            $this->deletedirectory(str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/img/prueba/" . $resultado['name']);

            // unlink(str_replace("\aplication\controller", "", __DIR__) . "/img/prueba/".$resultado['name'] );
            // foreach ($imgList as $key => $value) {
            //     unlink(str_replace("\aplication\controller", "", __DIR__) . "/" . $value);
            // }

            // foreach ($fileList as $key => $value) {
            //     unlink(str_replace("\aplication\controller", "", __DIR__) . "/" . $value);
            // }



            $stmt = $pdo->prepare("delete from products  where id =:id");



            $stmt->bindParam(':id', $id);
            // Ejecutar la sentencia SQL con los valores correspondientes
            $stmt->execute();



            $returnFields["status"] = 200;
            $returnFields["message"] = "Eliminado correctamente";

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);

        } catch (\Throwable $e) {
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);
        }
        // Iterar sobre el resultado
    }

    function deletedirectory($directorio): bool
    {
        if (is_dir($directorio)) {
            $archivos = glob($directorio . '/*'); // Obtener todos los archivos y subdirectorios dentro del directorio
            foreach ($archivos as $archivo) {
                try {
                    if (is_file($archivo)) {
                        // Si es un archivo, eliminarlo
                        unlink($archivo);
                    } elseif (is_dir($archivo)) {
                        // Si es un directorio, llamar recursivamente a esta función para eliminarlo
                        $this->deletedirectory($archivo);
                    }
                } catch (\Throwable $th) {
                    //throw $th;
                }

            }
            // Una vez que todos los archivos y subdirectorios están eliminados, eliminar el directorio principal
            rmdir($directorio);
            return true;
        } else {
            return false;
        }

    }

    public function addProducts(): string
    {
        $returnFields = array();
        try {
            $filesNames = array();
            $imagesNames = array();


            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();

            $files = $_FILES["files"];
            $images = $_FILES["images"];

            $logo = $_FILES["logo"];
            if ($_POST["name"] === null) {
                $returnFields["status"] = 406;
                $returnFields["message"] = "Nombre de producto requerid";
                $return = json_encode($returnFields);
                return $return;
            }



            $filters = $_POST["filters"] === null ? "" : substr($_POST["filters"], 0, strlen($_POST["filters"]) - 1);
            $description = $_POST["description"] === null ? "" : $_POST["description"];
            $name = $_POST["name"] === null ? "" : $_POST["name"];
            $section = $_POST["section"] === null ? "" : $_POST["section"];
            $amount = $_POST["amount"] === null ? "" : substr($_POST["amount"], 0, strlen($_POST["amount"]) - 1);
            $amountOther = $_POST["amountOther"] === null ? "" : $_POST["amountOther"];
            $imagesNamesString = "[]";

            $directoryLogo = str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/img/prueba/" . $name . "/images/logo";
            if (!is_dir($directoryLogo)) {
                // Crear la carpeta con permisos 0777 (lectura, escritura y ejecución para todos)
                mkdir($directoryLogo, 0777, true);

                // Establecer permisos adicionales si es necesario
                chmod($directoryLogo, 0777);
            }
            $rutaArchivoLogo = "";
            $logopatch = "";
            if ($logo !== null) {
                // Mover archivo al directorio deseado
                $rutaArchivoLogo = $directoryLogo . "/" . basename($logo["name"]);
                move_uploaded_file($logo["tmp_name"], $rutaArchivoLogo);
                $logopatch = "/img/prueba/" . $name . "/images/logo/" . basename($logo["name"]);
                // Agregar nombre del archivo a la lista
            }




            if ($images !== null) {
                $directoryimage = str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/img/prueba/" . $name . "/images";
                if (!is_dir($directoryimage)) {
                    // Crear la carpeta con permisos 0777 (lectura, escritura y ejecución para todos)
                    mkdir($directoryimage, 0777, true);

                    // Establecer permisos adicionales si es necesario
                    chmod($directoryimage, 0777);
                }
                foreach ($images["name"] as $key => $nombre) {
                    if ($nombre != null || $nombre != "") {
                        // Mover archivo al directorio deseado
                        $rutaArchivo = $directoryimage . "/" . basename($images["name"][$key]);
                        move_uploaded_file($images["tmp_name"][$key], $rutaArchivo);
                        // Agregar nombre del archivo a la lista
                        $imagesNames[] = /*$directoryimage .*/ "img/prueba/" . $name . "/images/" . basename($images["name"][$key]);
                    }
                }
                $imagesNamesString = json_encode($imagesNames);
            }
            $filesNamesString = "[]";
            if ($files !== null) {
                $directoryFile = str_replace("/aplication/controller", "", str_replace("\\", "/", __DIR__)) . "/img/prueba/" . $name . "/files";

                if (!is_dir($directoryFile)) {
                    // Crear la carpeta con permisos 0777 (lectura, escritura y ejecución para todos)
                    mkdir($directoryFile, 0777, true);

                    // Establecer permisos adicionales si es necesario
                    chmod($directoryFile, 0777);
                }

                foreach ($files["name"] as $key => $nombre) {
                    // Mover archivo al directorio deseado
                    if ($nombre != null || $nombre != "") {
                        $rutaArchivo = $directoryFile . "/" . basename($files["name"][$key]);
                        move_uploaded_file($files["tmp_name"][$key], $rutaArchivo);
                        // Agregar nombre del archivo a la lista
                        $filesNames[] =/* $directoryFile .*/ "img/prueba/" . $name . "/files/" . basename($files["name"][$key]);
                    }
                }

                $filesNamesString = json_encode($filesNames);
            }



            $stmt = $pdo->prepare("INSERT INTO products (`name`,`description`,`listImg`,`listDocs`,`filters`,`section`,`amount`,`logo`,`amountOther`) VALUES (:name,:description,:listImg,:listDocs,:filters,:section,:amount,:logo,:amountOther)");


            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':listImg', $imagesNamesString);
            $stmt->bindParam(':listDocs', $filesNamesString);
            $stmt->bindParam(':filters', $filters);
            $stmt->bindParam(':section', $section);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':logo', $logopatch);
            $stmt->bindParam(':amountOther', $amountOther);
            // Ejecutar la sentencia SQL con los valores correspondientes
            $stmt->execute();



            $returnFields["status"] = 200;
            $returnFields["message"] = "Registrado correctamente";

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);

        } catch (\Throwable $e) {
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);
        }
        // Iterar sobre el resultado
    }

    public function getProducts(): string
    {
        $returnFields = array();
        $Ssql = "";
        try {
            $page = !isset($_GET["page"]) ? 1 : $_GET["page"];
            $section = !isset($_GET["section"]) ? "" : "#" . $_GET["section"];
            $filters = !isset($_GET["filters"]) ? "" : substr($_GET["filters"], 0, strlen($_GET["filters"]) - 1);
            $search = !isset($_GET["search"]) ? "" : $_GET["search"];

            $filters = str_replace("{", "%", $filters);
            $filters = str_replace("}", "%", $filters);

            $Ssql = "select * from products where section=:section and ";
            if ($search !== "") {
                $search = '%' . $search . '%';
                $Ssql .= "`name` like :name and ";
            }

            if ($filters !== "") {

                $filtersList = explode(",", $filters);
                $Ssql .= "(";
                foreach ($filtersList as $filter) {
                    $Ssql .= "`filters` like :filter" . str_replace("%", "", $filter) . " or ";
                }
                $Ssql = str_ends_with($Ssql, ' or ') ? substr($Ssql, 0, strlen($Ssql) - 3) : $Ssql;
                $Ssql .= ")";
            }
            $Ssql = str_ends_with($Ssql, ' and ') ? substr($Ssql, 0, strlen($Ssql) - 4) : $Ssql;
            $Ssql = str_ends_with($Ssql, ' where ') ? substr($Ssql, 0, strlen($Ssql) - 6) : $Ssql;
            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();

            // count
            $statementCount = $pdo->prepare(str_replace(" * ", " count(*) as count ", $Ssql));

            if ($search !== "") {
                $statementCount->bindParam(':name', $search, PDO::PARAM_STR);
            }

            if ($filters !== "") {
                $filtersList = explode(",", $filters);
                foreach ($filtersList as $filter) {
                    $filterSearch = "%{" . str_replace("%", "", $filter) . "}%";
                    $statementCount->bindParam(":filter" . str_replace("%", "", $filter), $filterSearch, PDO::PARAM_STR);
                }
            }


            $statementCount->bindParam(":section", $section, PDO::PARAM_STR);

            $statementCount->execute();

            $resultadoCount = $statementCount->fetchAll(PDO::FETCH_ASSOC);

            $count = $resultadoCount[0]["count"];


            // data
            $statement = $pdo->prepare($Ssql . " LIMIT " . $page * 10 . " OFFSET 0" . ($page * 10) - 10);
            $statement->bindParam(":section", $section, PDO::PARAM_STR);
            if ($search !== "") {
                $statement->bindParam(':name', $search, PDO::PARAM_STR);
            }

            if ($filters !== "") {
                $filtersList = explode(",", $filters);
                foreach ($filtersList as $filter) {
                    $filterSearch = "%{" . str_replace("%", "", $filter) . "}%";
                    $statement->bindParam(":filter" . str_replace("%", "", $filter), $filterSearch, PDO::PARAM_STR);
                }
            }

            $statement->execute();
            $resultados = $statement->fetchAll(PDO::FETCH_ASSOC);
            $resultadosReturn = array();

            $i = 0;
            foreach ($resultados as $resultado) {
                $resultadosReturnEach = array();
                $resultadosReturnEach['listDocs'] = json_decode($resultado['listDocs'], true);
                $resultadosReturnEach['listImg'] = json_decode($resultado['listImg'], true);
                $resultadosReturnEach['name'] = $resultado['name'];
                $resultadosReturnEach['description'] = $resultado['description'];
                $resultadosReturnEach['amount'] = $resultado['amount'];
                $resultadosReturnEach['amountOther'] = $resultado['amountOther'];
                $resultadosReturnEach['id'] = $resultado['id'];
                $resultadosReturnEach['logo'] = $resultado['logo'];
                $resultadosReturn[$i] = $resultadosReturnEach;
                $i++;
            }

            $returnFields["data"] = $resultadosReturn;
            $returnFields["Page"] = $page;
            $returnFields["Total"] = $count;
            $returnFields["status"] = 200;
            $returnFields["message"] = "Correcto";

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);

        } catch (\Throwable $e) {
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();

            $returnProduct = json_encode($returnFields);

            return json_encode($returnProduct);
        }
        // Iterar sobre el resultado
    }

    public function getProduct(): string
    {
        $returnFields = array();
        $Ssql = "";
        try {
            $id = !isset($_GET["id"]) ? 0 : $_GET["id"];

            $Ssql = "select * from products where id= :id";


            $conexion = new Conexion();
            $pdo = $conexion->obtenerConexion();

            $statement = $pdo->prepare($Ssql);

            $statement->bindParam(':id', $id, PDO::PARAM_INT);




            $statement->execute();
            $resultados = $statement->fetchAll(PDO::FETCH_ASSOC);


            $resultadosinfo = array();
            $resultadosReturn = array();

            foreach ($resultados as $resultado) {
                $resultadosReturn = array();
                $resultadosReturn['listDocs'] = json_decode($resultado['listDocs'], true);
                $resultadosReturn['listImg'] = json_decode($resultado['listImg'], true);
                $resultadosReturn['name'] = $resultado['name'];
                $resultadosReturn['description'] = $resultado['description'];
                $resultadosReturn['amount'] = $resultado['amount'];
                $resultadosReturn['id'] = $resultado['id'];
                $resultadosReturn['filters'] = $resultado['filters'];
                $resultadosReturn['amountOther'] = $resultado['amountOther'];
                $resultadosReturn['logo'] = $resultado['logo'];

                $resultadosReturn['amountImgs'] = "";
                $resultadosReturn['amountName'] = "";

                if (($resultado['amount'] == null ? "" : $resultado['amount']) !== "") {
                    $resultado['amount'] = str_replace("{", "", $resultado['amount']);
                    $resultado['amount'] = str_replace("}", "", $resultado['amount']);

                    $Ssql = "select * from amount where id in ( " . $resultado['amount'] . ")";

                    $statement1 = $pdo->prepare($Ssql);

                    // $statement1->bindParam(':amount', $resultado['amount'], PDO::PARAM_);

                    $statement1->execute();
                    $resultados1 = $statement1->fetchAll(PDO::FETCH_ASSOC);


                    $amountImg = "";
                    $amountDesc = "";
                    foreach ($resultados1 as $resultado1) {
                        $amountImg .= $resultado1["patch"] . ",";
                        $amountDesc .= $resultado1["name"] . ", ";
                    }
                    $amountImg = str_ends_with($amountImg, ',') ? substr($amountImg, 0, -1) : $amountImg;
                    $amountDesc = str_ends_with($amountDesc, ', ') ? substr($amountDesc, 0, -2) : $amountDesc;

                    $resultadosReturn['amountImgs'] = explode(",", $amountImg) ; 
                    $resultadosReturn['amountName'] = $amountDesc . (( $resultado['amountOther'] == null)? "" : ",". $resultado['amountOther'] ); 
                }
            }

            $returnFields["data"] = $resultadosReturn;
            $returnFields["status"] = 200;
            $returnFields["info"] = $resultadosinfo;
            $returnFields["message"] = "Correcto";

            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);

        } catch (\Throwable $e) {
            $returnFields["status"] = 500;
            $returnFields["message"] = $e->getMessage();
            // $returnFields["info"] = $Ssql;
            $returnProduct = json_encode($returnFields);


            return json_encode($returnProduct);
        }
        // Iterar sobre el resultado
    }
}
?>