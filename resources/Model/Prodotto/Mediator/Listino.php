<?php
/**
 * This is a PRODOTTO for LISTINO
 */
class Model_Prodotto_Mediator_Listino
    extends Model_Prodotto_Mediator_AbstractProduct
{
    /**
     * Table listini_prodotti fields
     * @var array
     */
    protected $_data = array(
            'idlistino',
            'idprodotto',
            'costo_listino',
            'note_listino',
            'attivo_listino'
        );
    
    public function __construct(Model_Prodotto_Mediator_MediatorInterface $medium) 
    {
        parent::__construct($medium);
    }
    
    /**
     * Verifica se il prodotto è nel Listino (se NON esiste il record in listini_prodotti idlistino è NULL!)
     * @return bool
     */
    public function isInListino() 
    {
        return is_null($this->getIdListino()) ? false : true;
    }
    
    
/* ***********************************************
 *  GET properties
 */
    
    /**
     * @return mixed
     */
    public function getIdListino()
    {
        return $this->_getValue("idlistino");
    }
    
    /**
     * @return float
     */
    public function getCostoListino()
    {
        return $this->_getValue("costo_listino");
    }
    
    /**
     * @return string
     */
    public function getNoteListino()
    {
        return $this->_getValue("note_listino");
    }

    /**
     * @return bool
     */
    public function getAttivoListino()
    {
        return $this->_getValue("attivo_listino");
    }

/* ***********************************************
 *  SET properties
 */

    
    /**
     * @param mixed $id
     */
    public function setIdListino($id)
    {
        $this->_setValue("idlistino", $id);
    }
    
    /**
     * @param mixed $id
     */
    public function setIdProdotto($id)
    {
        $this->_setValue("idprodotto", $id);
    }
    
    /**
     * @param float $c
     */
    public function setCostoListino($c)
    {
        $this->_setValue("costo_listino", $c);
    }
    
    /**
     * @param string $note
     */
    public function setNoteListino($note)
    {
        $this->_setValue("note_listino", $note);
    }
    
    /**
     * @param mixed $flag
     */
    public function setAttivoListino($flag)
    {
        $this->_setValue("attivo_listino", $this->filterFlag($flag));
    }
    
    
}
