<?php
/**
 * Description of Index
 *
 * @author gullo
 */
class Controller_Gruppo extends MyFw_Controller {

    private $_userSessionVal;
    private $_iduser;
    
    function _init() {
        $auth = Zend_Auth::getInstance();
        $this->_iduser = $auth->getIdentity()->iduser;
        $this->_userSessionVal = new Zend_Session_Namespace('userSessionVal');
    }

    function indexAction() {
        $gObj = new Model_Groups();
        $this->view->group = $gObj->getGroupById($this->_userSessionVal->idgroup);
    }

    
    function iscrittiAction() {
        
        // get All Iscritti in Group
        $sql = "SELECT u.*, ug.attivo "
              ." FROM users_group AS ug"
              ." LEFT JOIN users AS u ON ug.iduser=u.iduser"
              ." WHERE ug.idgroup= :idgroup"
              ." ORDER BY u.cognome";
        //echo $sql; die;
        $sth = $this->getDB()->prepare($sql);
        $sth->execute(array('idgroup' => $this->_userSessionVal->idgroup));
        
        // check IDfondatore
        $gObj = new Model_Groups();
        $arFounders = $gObj->getArFoundersId($this->_userSessionVal->idgroup);
        $auth = Zend_Auth::getInstance();
        $this->view->imFondatore = in_array($auth->getIdentity()->iduser, $arFounders);
        
        
//        Zend_Debug::dump($sth->rowCount()); die;
        $this->view->list = $sth->fetchAll(PDO::FETCH_CLASS);
    }





}
?>