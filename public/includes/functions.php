<?php
// public/includes/functions.php
require_once __DIR__ . '/image.php';
require_once __DIR__ . '/lang.php';
require_once __DIR__ . '/../../includes/functions.php';

/**
 * Fetch active places with pagination and filtering
 */
function getPlaces(PDO $pdo, array $filters, int $limit, int $offset): array {
    $where = ["p.status = 'active'"];
    $params = [];
    
    if (!empty($filters['category_id'])) {
        $where[] = "p.category_id = :category_id";
        $params['category_id'] = $filters['category_id'];
    }
    if (!empty($filters['region_id'])) {
        $where[] = "p.region_id = :region_id";
        $params['region_id'] = $filters['region_id'];
    }
    if (!empty($filters['search'])) {
        $where[] = "(p.name_uz LIKE :search OR p.name_ru LIKE :search OR p.name_en LIKE :search)";
        $params['search'] = "%" . $filters['search'] . "%";
    }
    
    $whereSql = "WHERE " . implode(' AND ', $where);
    $limitSql = "LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    $sql = "
        SELECT p.*, c.name_uz as category_name, c.name_ru as category_name_ru, c.name_en as category_name_en, 
               r.name_uz as region_name, r.name_ru as region_name_ru, r.name_en as region_name_en
        FROM places p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN regions r ON p.region_id = r.id
        $whereSql
        ORDER BY p.id DESC
        $limitSql
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Fetch active historical people with pagination and filtering
 */
function getPeople(PDO $pdo, array $filters, int $limit, int $offset): array {
    $where = ["p.status = 'active'"];
    $params = [];
    
    if (!empty($filters['region_id'])) {
        $where[] = "p.region_id = :region_id";
        $params['region_id'] = $filters['region_id'];
    }
    if (!empty($filters['search'])) {
        $where[] = "(p.name_uz LIKE :search OR p.name_ru LIKE :search OR p.name_en LIKE :search)";
        $params['search'] = "%" . $filters['search'] . "%";
    }
    if (!empty($filters['era_range'])) {
        $where[] = "p.born_year BETWEEN :era_start AND :era_end";
        $params['era_start'] = $filters['era_range'][0];
        $params['era_end'] = $filters['era_range'][1];
    }
    
    $whereSql = "WHERE " . implode(' AND ', $where);
    $limitSql = "LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    $sql = "
        SELECT p.*, r.name_uz as region_name, r.name_ru as region_name_ru, r.name_en as region_name_en
        FROM people p
        LEFT JOIN regions r ON p.region_id = r.id
        $whereSql
        ORDER BY p.id DESC
        $limitSql
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Fetch active restaurants with pagination and filtering
 */
function getRestaurants(PDO $pdo, array $filters, int $limit, int $offset): array {
    $where = ["1=1"];
    $params = [];
    
    if (!empty($filters['region_id'])) {
        $where[] = "r.region_id = :region_id";
        $params['region_id'] = $filters['region_id'];
    }
    if (!empty($filters['price_level'])) {
        $where[] = "r.price_level = :price_level";
        $params['price_level'] = $filters['price_level'];
    }
    if (!empty($filters['search'])) {
        $where[] = "(r.name LIKE :search OR r.cuisine_type LIKE :search)";
        $params['search'] = "%" . $filters['search'] . "%";
    }
    
    $whereSql = "WHERE " . implode(' AND ', $where);
    $limitSql = "LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    $sql = "
        SELECT r.*, reg.name_uz as region_name, reg.name_ru as region_name_ru, reg.name_en as region_name_en 
        FROM restaurants r
        LEFT JOIN regions reg ON r.region_id = reg.id
        $whereSql
        ORDER BY r.id DESC
        $limitSql
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Fetch active hotels with pagination and filtering
 */
function getHotels(PDO $pdo, array $filters, int $limit, int $offset): array {
    $where = ["h.status = 'active'"];
    $params = [];
    
    if (!empty($filters['region_id'])) {
        $where[] = "h.region_id = :region_id";
        $params['region_id'] = $filters['region_id'];
    }
    if (!empty($filters['stars'])) {
        $where[] = "h.stars = :stars";
        $params['stars'] = $filters['stars'];
    }
    if (!empty($filters['search'])) {
        $where[] = "(h.name_uz LIKE :search OR h.name_ru LIKE :search)";
        $params['search'] = "%" . $filters['search'] . "%";
    }
    
    $whereSql = "WHERE " . implode(' AND ', $where);
    $limitSql = "LIMIT " . (int)$limit . " OFFSET " . (int)$offset;
    
    $sql = "
        SELECT h.*, reg.name_uz as region_name, reg.name_ru as region_name_ru, reg.name_en as region_name_en 
        FROM hotels h
        LEFT JOIN regions reg ON h.region_id = reg.id
        $whereSql
        ORDER BY h.id DESC
        $limitSql
    ";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt->fetchAll();
}

/**
 * Generic count function for pagination with filter parsing
 */
function countRows(PDO $pdo, string $table, array $filters): int {
    $where = [];
    if ($table === 'restaurants') {
        $where[] = "1=1";
    } else {
        $where[] = "status = 'active'";
    }
    $params = [];
    
    if (!empty($filters['category_id'])) {
        $where[] = "category_id = :category_id";
        $params['category_id'] = $filters['category_id'];
    }
    if (!empty($filters['region_id'])) {
        $where[] = "region_id = :region_id";
        $params['region_id'] = $filters['region_id'];
    }
    if (!empty($filters['price_level'])) {
        $where[] = "price_level = :price_level";
        $params['price_level'] = $filters['price_level'];
    }
    if (!empty($filters['price_range'])) {
        $where[] = "price_range = :price_range";
        $params['price_range'] = $filters['price_range'];
    }
    if (!empty($filters['stars'])) {
        $where[] = "stars = :stars";
        $params['stars'] = $filters['stars'];
    }
    if (!empty($filters['search'])) {
        if ($table === 'places' || $table === 'people') {
            $where[] = "(name_uz LIKE :search OR name_ru LIKE :search OR name_en LIKE :search)";
        } elseif ($table === 'restaurants') {
            $where[] = "(name LIKE :search OR cuisine_type LIKE :search)";
        } elseif ($table === 'hotels') {
            $where[] = "(name_uz LIKE :search OR name_ru LIKE :search)";
        } else {
            $where[] = "name_uz LIKE :search";
        }
        $params['search'] = "%" . $filters['search'] . "%";
    }
    if (!empty($filters['era_range'])) {
        $where[] = "born_year BETWEEN :era_start AND :era_end";
        $params['era_start'] = $filters['era_range'][0];
        $params['era_end'] = $filters['era_range'][1];
    }
    
    $whereSql = "WHERE " . implode(' AND ', $where);
    $allowedTables = ['places', 'people', 'restaurants', 'hotels'];
    if (!in_array($table, $allowedTables)) return 0;
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM {$table} {$whereSql}");
    $stmt->execute($params);
    return (int)$stmt->fetchColumn();
}

/**
 * Fetch hero slider images for a specific page
 */
function getHeroImages(PDO $pdo, string $pageKey): array {
    $stmt = $pdo->prepare("SELECT image_path FROM hero_sliders WHERE page_key = :key ORDER BY sort_order ASC");
    $stmt->execute(['key' => $pageKey]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}
