<?php

/**
 * Description of Logger
 * 
 * @author gullo
 */
class Model_Ordini_Logger 
{
    
    
    static function LogVariazioneProdottoByField(Model_Ordini_Ordine $ordine, Model_Prodotto_Mediator_Mediator $prodotto, $field)
    {
        switch ($field) {
            case "costo_ordine":
                return self::LogVariazionePrezzo($ordine, $prodotto);
            
            case "disponibile_ordine":
                return self::LogVariazioneDisponibile($ordine, $prodotto);

            default:
                return;
        }
    }
    
    static private function LogVariazionePrezzo(Model_Ordini_Ordine $ordine, Model_Prodotto_Mediator_Mediator $prodotto)
    {
        if(!is_null($prodotto))
        {
            $descProdotto = $prodotto->getDescrizioneAnagrafica();
            $prezzo = $prodotto->getCostoOrdine();
            $txt = "Il prezzo del prodotto <b>$descProdotto</b> è stato modificato: <b>$prezzo &euro;</b>";
            return self::LogToDB($ordine->getIdOrdine(), $txt);
        }
    }
    
    static private function LogVariazioneDisponibile(Model_Ordini_Ordine $ordine, Model_Prodotto_Mediator_Mediator $prodotto)
    {
        if(!is_null($prodotto))
        {
            $descProdotto = $prodotto->getDescrizioneAnagrafica();
            $disponibile = $prodotto->getDisponibileOrdine();
            if($disponibile == "S") 
            {
                $txt = "Il prodotto <b>$descProdotto</b> è stato reso disponibile!</b>";
            } else {
                $txt = "Il prodotto <b>$descProdotto</b> è stato reso <b>NON</b> disponibile!</b>";                
            }
            return self::LogToDB($ordine->getIdOrdine(), $txt);
        }
    }
    
    static private function LogToDB($idordine, $descrizione) 
    {
        $db = Zend_Registry::get("db");
        $sth = $db->prepare("INSERT INTO ordini_variazioni SET idordine= :idordine, data=NOW(), descrizione= :descrizione");
        return $sth->execute(array('idordine' => $idordine, 'descrizione' => $descrizione));
    }
    
    
}