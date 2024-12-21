<?php
namespace MythicalClient\Chat;

use MythicalClient\Chat\interface\UserActivitiesTypes;

class UserActivities {
    
    /**
     * Add user activity
     * 
     * @param string $uuid User UUID
     * @param string|\MythicalClient\Chat\interface\UserActivitiesTypes $type Activity type
     * @param string $ipv4 IP address
     * 
     * @return bool
     */
    public static function add(string $uuid, string|UserActivitiesTypes $type, string $ipv4) : bool {
        $dbConn = Database::getPdoConnection();
        
        $stmt = $dbConn->prepare("INSERT INTO " . self::getTable() . " (user, action, ip_address) VALUES (:user, :action, :ip_address)");
        return $stmt->execute([
            ':user' => $uuid,
            ':action' => $type,
            ':ip_address' => $ipv4
        ]);
    }
    /**
     * Get user activities
     * 
     * @param string $uuid User UUID
     * 
     * @return array
     */
    public static function get(string $uuid) : array {
        $dbConn = Database::getPdoConnection();
        
        $stmt = $dbConn->prepare("SELECT * FROM " . self::getTable() . " WHERE user = :user LIMIT 125");
        $stmt->execute([
            ':user' => $uuid
        ]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get table name
     * 
     * @return string Table name
     */
    public static function getTable() : string {
        return "mythicalclient_users_activities";
    }
}