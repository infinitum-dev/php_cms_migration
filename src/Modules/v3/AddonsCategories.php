<?php

/**
 * FYI Infinitum SDK WEB
 *
 * @package FYI
 * @subpackage Infinitum SDK
 * @since 0.0.1
 */

namespace Fyi\Cms\Modules\v3;

class AddonsCategories
{
    protected $connection;
    protected $app;
    
    public function __construct($connection, $app)
	{
        $this->connection = $connection;
        $this->app = $app;
    }

    public function get($params = [])
    {
        if (!isset($params["id_status"]))
        {
            $params["id_status"] = 1;
        }

        $queryBuilder = $this->connection->createQueryBuilder();
        
        $sql = $queryBuilder
            ->select("ac.*")
            ->from("addons_categories", "ac")
            ->join('ac', 'objects_typesApps', 'ot', 'ac.id_type = ot.id_type')
            ->andwhere("ot.id_app = " . $this->app)
            ->andwhere("ac.id_status = " . $params["id_status"]);

        $results = [];
       
        $stmt = $this->connection->query($sql);
        while($object = $stmt->fetch())
        {
            array_push($results, $object);
        }

        return $results;
    }

    public function getCategoryObjects($params = [])
    {
        $queryBuilder = $this->connection->createQueryBuilder();
        
        $sql = $queryBuilder
            ->select("ac.*")
            ->from("addons_categoriesObjects", "ac")
            ->join('ac', 'objects', 'o', 'ac.id_object = o.id_object')
            ->join('o', 'objects_typesApps', 'ot', 'o.id_type = ot.id_type')
            ->andwhere("ac.id_category <> 0")
            ->andwhere("o.id_status = 1")
            ->andwhere("o.published = 1")
            ->andwhere("o.id_app = " . $this->app)
            ->andwhere("ot.id_app = " . $this->app)
            ->andWhere("ac.id_revision IN ( select MAX(id_revision) from addons_categoriesObjects where id_object = ac.id_object )");

        // objectId
        if (isset($params["id_object"]))
        {
            $queryBuilder->andWhere("o.id_object = " . $params["id_object"]);
        }
        
        // offset
        if (isset($params["offset"]))
        {
            $queryBuilder->setFirstResult($params["offset"]);
        }

        // limit
        if (isset($params["limit"]))
        {
            $queryBuilder->setMaxResults($params["limit"]);
        }
        
        $results = [];
       
        $stmt = $this->connection->query($sql);
        while($object = $stmt->fetch())
        {
            array_push($results, $object);
        }

        return $results;
    }
}
