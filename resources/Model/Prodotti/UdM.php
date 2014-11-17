<?php
/**
 * Description of UdM
 * 
 * @author gullo
 */
class Model_Prodotti_UdM {
    
    private $_arUdM = array(
        'Bottiglia' => 'Bottiglia',
        'Confezione' => 'Confezione',
        'Pezzo'      => 'Pezzo',
        'Kg'         => 'Kg',
        'Litro'      => 'Litro'
    );
    
    function getArUdm(){
        return $this->_arUdM;
    }
    
}