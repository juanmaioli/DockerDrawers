<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=utf-8");
mb_internal_encoding('UTF-8');
mb_http_output('UTF-8');

// Configuración de errores para que mysqli lance excepciones
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$usr_timezone = -3;
include("../config.php");

try {
    $conn = get_db_connection();
    $fecha = date("Y-m-d H:i:s");
    $date = new DateTime($fecha);
    $date->setTimezone(new DateTimeZone($usr_timezone));
    
    $tipoSql = $_GET['id'] ?? '';
    $parametro = explode("-", $tipoSql);
    $tarea = $parametro[0];
    $stmt = null;
    $result = null;

    switch ($tarea) {
        case 'list':
            if(($parametro[2] ?? 0) == 0){
                $sql = "SELECT drawers_category.category_name, drawers_category.category_color, drawers_drawer.drawer_id, drawers_drawer.drawer_name, drawers_drawer.drawer_owner, drawers_drawer.drawer_delete, drawers_drawer.drawer_location, drawers_drawer.drawer_image, drawers_drawer.drawer_description, (SELECT GROUP_CONCAT( item_name SEPARATOR ',' ) FROM drawers_items WHERE item_drawer = drawers_drawer.drawer_id ) AS items_included, (SELECT sum(item_amount) as total FROM drawers_items WHERE item_drawer = drawers_drawer.drawer_id GROUP BY item_drawer) AS items_total, (SELECT sum(total_price) as total_price FROM total_price_drawer WHERE drawer = drawers_drawer.drawer_id) AS drawer_price FROM drawers_drawer LEFT JOIN drawers_category ON drawers_drawer.drawer_category = drawers_category.category_id WHERE drawer_owner = ? AND drawer_delete = 0 ORDER BY drawer_name";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $parametro[1]);
            }else{
                $sql = "SELECT drawers_category.category_name, drawers_category.category_color, drawers_drawer.drawer_id, drawers_drawer.drawer_name, drawers_drawer.drawer_owner, drawers_drawer.drawer_delete, drawers_drawer.drawer_location, drawers_drawer.drawer_image, drawers_drawer.drawer_description, (SELECT GROUP_CONCAT( item_name SEPARATOR ',' ) FROM drawers_items WHERE item_drawer = drawers_drawer.drawer_id ) AS items_included, (SELECT sum(item_amount) as total FROM drawers_items WHERE item_drawer = drawers_drawer.drawer_id GROUP BY item_drawer) AS items_total, (SELECT sum(total_price) as total_price FROM total_price_drawer WHERE drawer = drawers_drawer.drawer_id) AS drawer_price FROM drawers_drawer LEFT JOIN drawers_category ON drawers_drawer.drawer_category = drawers_category.category_id WHERE drawer_owner = ? AND drawer_delete = 0 AND drawer_category = ? ORDER BY drawer_name";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $parametro[1], $parametro[2]);
            }
            break;
        case 'view':
            $sql = "SELECT drawers_drawer.drawer_id, drawers_drawer.drawer_name, drawers_drawer.drawer_category, drawers_drawer.drawer_owner, drawers_drawer.drawer_location, drawers_drawer.drawer_description, drawers_drawer.drawer_image, drawers_drawer.drawer_image_full, drawers_drawer.drawer_date, drawers_drawer.drawer_update, drawers_drawer.drawer_delete, drawers_category.category_name, drawers_category.category_color FROM drawers_drawer INNER JOIN drawers_category ON drawers_drawer.drawer_category = drawers_category.category_id WHERE drawer_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $parametro[1]);
            break;
        case 'categorylist':
            $sql = "SELECT drawers_category.category_id, drawers_category.category_name, drawers_category.category_color, ( SELECT sum( item_amount ) AS total FROM drawers_items WHERE item_category = drawers_category.category_id ) AS ItemsPerCategory, ( SELECT count(*) AS total FROM drawers_drawer WHERE drawer_category = drawers_category.category_id GROUP BY drawer_category ) AS DrawersPerCategory, ( SELECT sum( total_price ) FROM total_price WHERE category = drawers_category.category_id GROUP BY category  ) AS DrawersPrice FROM drawers_category ORDER BY category_name";
            $result = $conn->query($sql);
            break;
        case 'categoryview':
            $sql = "SELECT drawers_category.category_id, drawers_category.category_name, drawers_category.category_color FROM drawers_category WHERE category_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $parametro[1]);
            break;
        case 'itemview':
            $sql = "SELECT drawers_items.item_id, drawers_items.item_drawer, drawers_items.item_name, drawers_items.item_amount, drawers_items.item_price, drawers_items.item_description, drawers_items.item_image, drawers_items.item_owner, drawers_items.item_category, drawers_items.item_brand, drawers_items.item_model, drawers_category.category_name, drawers_category.category_color, drawers_drawer.drawer_name FROM drawers_items LEFT JOIN drawers_category ON drawers_items.item_category = drawers_category.category_id LEFT JOIN drawers_drawer ON drawers_items.item_drawer = drawers_drawer.drawer_id WHERE item_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $parametro[1]);
            break;
        case 'itemlist':
            $sql = "SELECT drawers_category.category_name, drawers_category.category_color, drawers_items.item_id, drawers_items.item_drawer, drawers_items.item_name, drawers_items.item_image, drawers_items.item_amount, drawers_items.item_price, drawers_items.item_description, drawers_items.item_category, drawers_items.item_owner, drawers_items.item_update, drawers_items.item_date FROM drawers_items INNER JOIN drawers_category ON drawers_items.item_category = drawers_category.category_id WHERE item_drawer = ? AND item_owner = ? AND item_delete = 0 ORDER BY item_name ASC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $parametro[1], $parametro[2]);
            break;
        case 'itemsall':
            if(($parametro[2] ?? 0) == 0){
                $sql = "SELECT drawers_category.category_name, drawers_category.category_color, drawers_items.item_id, drawers_items.item_drawer, drawers_items.item_name, drawers_items.item_image, drawers_items.item_amount, drawers_items.item_price, drawers_items.item_description, drawers_items.item_brand, drawers_items.item_model, drawers_items.item_category, drawers_items.item_owner, drawers_items.item_update, drawers_items.item_date, drawers_drawer.drawer_name, drawers_brand.brand_name FROM drawers_items INNER JOIN drawers_category ON drawers_items.item_category = drawers_category.category_id INNER JOIN drawers_drawer ON drawers_items.item_drawer = drawers_drawer.drawer_id INNER JOIN drawers_brand ON drawers_items.item_brand = drawers_brand.brand_id WHERE item_owner = ? AND item_delete = 0 ORDER BY item_name ASC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $parametro[1]);
            }else{
                $sql = "SELECT drawers_category.category_name, drawers_category.category_color, drawers_items.item_id, drawers_items.item_drawer, drawers_items.item_name, drawers_items.item_image, drawers_items.item_amount, drawers_items.item_price, drawers_items.item_description, drawers_items.item_brand, drawers_items.item_model, drawers_items.item_category, drawers_items.item_owner, drawers_items.item_update, drawers_items.item_date, drawers_drawer.drawer_name, drawers_brand.brand_name FROM drawers_items INNER JOIN drawers_category ON drawers_items.item_category = drawers_category.category_id INNER JOIN drawers_drawer ON drawers_items.item_drawer = drawers_drawer.drawer_id INNER JOIN drawers_brand ON drawers_items.item_brand = drawers_brand.brand_id WHERE item_owner = ? AND item_delete = 0 AND item_category = ? ORDER BY item_name ASC";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $parametro[1], $parametro[2]);
            }
            break;
        case 'totalprice':
            $sql = "SELECT sum(total_price) as total FROM total_price";
            $result = $conn->query($sql);
            break;
        case 'categoryprice':
            $sql = "SELECT drawers_category.category_name AS Categoria, drawers_category.category_id AS ID, ( SELECT sum( total_price ) FROM total_price WHERE category = drawers_category.category_id GROUP BY category ) AS category_price FROM drawers_items INNER JOIN drawers_category ON drawers_items.item_category = drawers_category.category_id GROUP BY item_category ORDER BY category_price DESC LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $parametro[1]);
            break;
        case 'categorytotal':
            $sql = "SELECT drawers_category.category_name as Categoria, drawers_category.category_id AS ID,sum(item_amount) AS Total FROM drawers_items INNER JOIN drawers_category ON drawers_items.item_category = drawers_category.category_id GROUP BY item_category order by total desc limit ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $parametro[1]);
            break;
        case 'bookmarks':
            $sql = "SELECT * FROM drawers_fav WHERE fav_delete = 0 ORDER BY fav_title";
            $result = $conn->query($sql);
            break;
        case 'bookmarksdel':
            $sql = "SELECT * FROM drawers_fav ORDER BY fav_title";
            $result = $conn->query($sql);
            break;
        case 'deleteBookmark':
            $sql = "UPDATE drawers_fav SET fav_delete = 1 WHERE fav_mla = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $parametro[1]);
            $stmt->execute();
            echo json_encode(["status" => "success"]);
            exit;
        case 'restoreBookmark':
            $sql = "UPDATE drawers_fav SET fav_delete = 0 WHERE fav_mla = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $parametro[1]);
            $stmt->execute();
            echo json_encode(["status" => "success"]);
            exit;
        case 'clearBookmark':
            $sql = "TRUNCATE TABLE drawers_fav";
            $conn->query($sql);
            echo json_encode(["status" => "success"]);
            exit;
        case 'brandlist':
            $sql = "SELECT * FROM drawers_brand ORDER BY brand_name";
            $result = $conn->query($sql);
            break;
        case 'lastitems':
            $limit = (int)($parametro[1] ?? 5);
            $owner = (int)($parametro[2] ?? 0);
            $sql = "SELECT item_id, item_name, item_image, item_date, category_color, item_drawer FROM drawers_items LEFT JOIN drawers_category ON item_category = category_id WHERE item_owner = ? AND item_delete = 0 ORDER BY item_date DESC LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $owner, $limit);
            break;
        case 'fullestdrawers':
            $limit = (int)($parametro[1] ?? 5);
            $owner = (int)($parametro[2] ?? 0);
            $sql = "SELECT d.drawer_id, d.drawer_name, d.drawer_image, c.category_color, SUM(i.item_amount) as total_items FROM drawers_drawer d LEFT JOIN drawers_items i ON d.drawer_id = i.item_drawer LEFT JOIN drawers_category c ON d.drawer_category = c.category_id WHERE d.drawer_owner = ? AND d.drawer_delete = 0 AND i.item_delete = 0 GROUP BY d.drawer_id ORDER BY total_items DESC LIMIT ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $owner, $limit);
            break;
        case 'search':
            $term = "%" . ($parametro[1] ?? '') . "%";
            $owner = (int)($parametro[2] ?? 0);
            $rawdata = array();

            // Search Items
            $sql_items = "SELECT 'item' as type, item_id as id, item_name as name, item_description as description, item_image as image, category_color, category_name, item_drawer as drawer_id, (SELECT drawer_name FROM drawers_drawer WHERE drawer_id = item_drawer) as drawer_name FROM drawers_items LEFT JOIN drawers_category ON item_category = category_id WHERE item_owner = ? AND item_delete = 0 AND (item_name LIKE ? OR item_description LIKE ? OR item_model LIKE ?)";
            $stmt_items = $conn->prepare($sql_items);
            $stmt_items->bind_param("isss", $owner, $term, $term, $term);
            $stmt_items->execute();
            $res_items = $stmt_items->get_result();
            while($row = $res_items->fetch_assoc()) $rawdata[] = $row;
            $stmt_items->close();

            // Search Drawers
            $sql_drawers = "SELECT 'drawer' as type, drawer_id as id, drawer_name as name, drawer_description as description, drawer_image as image, category_color, category_name, NULL as drawer_id, NULL as drawer_name FROM drawers_drawer LEFT JOIN drawers_category ON drawer_category = category_id WHERE drawer_owner = ? AND drawer_delete = 0 AND (drawer_name LIKE ? OR drawer_description LIKE ? OR drawer_location LIKE ?)";
            $stmt_drawers = $conn->prepare($sql_drawers);
            $stmt_drawers->bind_param("isss", $owner, $term, $term, $term);
            $stmt_drawers->execute();
            $res_drawers = $stmt_drawers->get_result();
            while($row = $res_drawers->fetch_assoc()) $rawdata[] = $row;
            $stmt_drawers->close();

            echo json_encode($rawdata, JSON_UNESCAPED_UNICODE);
            $conn->close();
            exit;
        default:
            throw new Exception("Invalid task: " . $tarea);
    }

    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
    }

    if ($result) {
        $rawdata = array();
        $i=0;
        while($row = $result->fetch_assoc())
        {
            $rawdata[$i] = $row;
            $i++;
        }
        echo json_encode($rawdata, JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([]);
    }

    if ($stmt) $stmt->close();
    $conn->close();

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "error" => true,
        "message" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
?>