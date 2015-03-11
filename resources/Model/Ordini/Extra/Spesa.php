<?php
/**
 * Description of generic Spesa
 * @author gullo
 */
class Model_Ordini_Extra_Spesa implements Model_Ordini_Extra_Interface 
{
    /**
     * Descrizione spesa extra
     * @var string
     */
    private $_descrizione;
    
    /**
     * Costo spesa extra
     * @var float
     */
    private $_costo;
    
    /**
     * Tipo di Ripartizione della spesa extra
     * @var string
     */
    private $_tipo;
        
    
    /**
     * Init values by serialized Array
     * @param string $descrizione
     * @param float $costo
     * @param string $tipo
     */
    public function __construct($descrizione, $costo, $tipo) 
    {
        $this->_descrizione = $descrizione;
        $this->_costo = $costo;
        $this->_tipo = $tipo;
    }
    
    
    /**
     * Return Descrizione of Extra
     * @return string
     */
    public function getDescrizione()
    {
        return $this->_descrizione;
    }
    
    /**
     * Return Costo of Extra
     * @return float
     */
    public function getCosto()
    {
        return $this->_costo;
    }
    
    /**
     * Return Tipo of Extra
     * @return string
     */
    public function getTipo()
    {
        return $this->_tipo;
    }
    

    
    
    
    
}