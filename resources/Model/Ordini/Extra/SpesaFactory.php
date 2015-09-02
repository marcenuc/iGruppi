<?php
/**
 * Description of SpesaFactory
 *
 * @author gullo
 */
class Model_Ordini_Extra_SpesaFactory {
    
    static function Create($descrizione, $costo, $tipo)
    {
        switch( $tipo )
        {
            case "RU": return new Model_Ordini_Extra_SpesaRipartitaUtente($descrizione, $costo, $tipo);
            case "RI": return new Model_Ordini_Extra_SpesaRipartitaImporto($descrizione, $costo, $tipo);
            case "FU": return new Model_Ordini_Extra_SpesaFissaUtente($descrizione, $costo, $tipo);
            default: return new Model_Ordini_Extra_SpesaRipartitaUtente($descrizione, $costo, $tipo);
        }
    }
}
