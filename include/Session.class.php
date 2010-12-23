<?php
/**
 * Session
 * 
 * Verwaltet eine Sitzung. Im Grunde nur ein Wrapper um die PHP-eigene
 * Sessionverwaltung. Das Verwenden dieser Klasse statt der PHP-eigenen
 * Sessionverwaltung ermöglicht jedoch einfachere Wechsel des zugrundeliegenden
 * Systems.
 * 
 * Verwendet das Singleton-Pattern, da es immer nur eine Session geben kann.
 * 
 * @author: Robert Nitsch <dev@robertnitsch.de>
 */
class Session {
	
	private $instance = null;
	
    private function Session() {
    	session_set_cookie_params(86400*30*12, '', 'np.bmaker.net');
    	session_start();
    }
    
    /**
     * Gibt eine Instanz zurück (Singleton).
     */
    public static function getInstance() {
    	if($this->instance == null)
    		$this->instance = new Session();
    	return $this->instance;
    }
    
    public final function __clone__() {}
    
    /**
     * Gibt den Wert einer Session-Variablen zurück. Wurde
     * die Session-Variable noch nicht gesetzt, wird $default
     * zurückgegeben. Das ist false, sofern nicht anders angegeben.
     */
    public function get($name, $default=false) {
    	if(isset($_SESSION[$name]))
    		return $_SESSION[$name];
    	
    	return $default;
    }
    
    /**
     * Setzt den Wert einer Session-Variablen.
     */
    public function set($name, $value) {
    	$_SESSION[$name] = $value;
    }
    
    /**
     * Vernichtet die Session (Logout).
     */
    public function destroy() {
    	session_destroy();
    }
    
    /**
     * Vernichtet die Session (Logout).
     * 
     * Alias von destroy().
     */
    public function logout() {
    	$this->destroy();
    }
}
?>