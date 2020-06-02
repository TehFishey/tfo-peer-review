<?php
/************************************************************************************** 
 * Rate-Limiter Utility Object
 * 
 * Description:
 * A Rate-Limiter based on the Token-Bucket algorithm, which stores its cache in an 
 * SQL table. Constructor properties define the unique identifiers used for each bucket
 * (user IP, in our case), the size of each bucket, and the number of tokens which
 * regenerate per second.
 * 
 * Adapted from code by Ryan Britton (https://ryanbritton.com/2016/11/rate-limiting-in-php-with-token-bucket/)
 * 
 * Methods:
 * ->consume(int $tokens) - Returns True if the number of available tokens is greater than
 *                          $tokens (decrementing available tokens by that much if so); otherwise
 *                          returns false.
 **************************************************************************************/
class RateLimiter {
    /* Constants to map from tokens per unit to tokens per second */
    const MILLISECOND = 0.001;
    const SECOND = 1;
    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
     
    const MICROTIME_DELTA = 0.0001; // microtime(true) has four significant digits to the 
                                    // right of the decimal
     
    private $_db;
    private $_table_name = 'RateLimits';
    protected $_identifier;
    protected $_bucket_capacity;
    protected $_tokens_per_second;
     
    public static function compare_microtimes($m1, $m2) {
        if (($m2 == 0 && ($m1 == 0 || abs($m1 - $m2) < static::MICROTIME_DELTA)) ||
             abs(($m1 - $m2) / $m2) < static::MICROTIME_DELTA) {
            return 0;
        }
         
        if ($m1 > $m2) { return 1; }
        return -1;
    }
     
    public function __construct($db, $identifier, $bucket_capacity, $tokens_per_second) {
        $this->_db = $db;
        assert($bucket_capacity > 0, "Token Bucket capacity must be > 0");
     
        $this->_identifier = $identifier;
        $this->_bucket_capacity = $bucket_capacity;
        $this->_tokens_per_second = $tokens_per_second;
    }
     
    public function consume($token_count = 1) {
        
        if ($token_count > $this->_bucket_capacity) { return false; }
         
        $microtime = $this->_tokens_to_seconds($token_count);
        $full_microtime = $this->_tokens_to_seconds($this->_bucket_capacity);
        
        // We need to ensure the operation is atomic, so we begin an explicit transaction
        // and lock the record.
        $this->_db->beginTransaction();
        $query = $this->_db->prepare("SELECT * FROM {$this->_table_name} WHERE ip = :ip FOR UPDATE");
        $query->execute([
            ':ip' => $this->_identifier
        ]);
        $record = $query->fetch(PDO::FETCH_ASSOC);
        if (!is_array($record)) {
            // Bucket does not exist, run the provided bootstrap closure
            if ($this->_bootstrap($this->_bucket_capacity - $token_count)) {
                $this->_db->commit();
                return true;
            }
             
            $this->_db->rollBack();
            return false;
        }
         
        // Check for availability, capping it to the capacity of the bucket
        $now = microtime(true);
        $available = min($now - $record['microtime'], $full_microtime);
        if (RateLimiter::compare_microtimes($microtime, $available) > 0) {
            $this->_db->rollBack();
            return false;
        }
         
        // Consume the tokens, purging any that are overfilled
        $query = $this->_db->prepare("UPDATE {$this->_table_name} SET microtime = :time WHERE ip = :ip");
        $query->execute([
            ':ip' => $this->_identifier,
            ':time' => $now - $available + $microtime,
        ]);
        $this->_db->commit();
        return true;
    }
     
    protected function _bootstrap($initialTokens) {
        $microtime = microtime(true) - $this->_tokens_to_seconds($initialTokens);

        $query = $this->_db->prepare("INSERT INTO {$this->_table_name} (ip, microtime) VALUES (:ip, :time)");
        $query->execute([
            ':ip' => $this->_identifier,
            ':time' => $microtime,
        ]);
        return true;
    }
     
    protected function _tokens_to_seconds($tokens) {
        return $tokens / $this->_tokens_per_second;
    }
     
    protected function _seconds_to_tokens($seconds) {
        return (int) $seconds * $this->_tokens_per_second;
    }
}