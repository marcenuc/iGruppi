<?php
/**
 * This is a Concrete Product GRUPPI for LISTINO
 */
class Model_AF_Listino_Gruppi extends Model_AF_Gruppi
{
    
    
    /**
     * @return Model_Builder_GroupSharing_Parts_Listino
     */
    public function buildGroup()
    {
        $builderGroup = new Model_Builder_GroupSharing_ListinoBuilder();
        $director = new Model_Builder_GroupSharing_Director();
        $group = $director->build($builderGroup);
        return $group;
    }
    
    
    /**
     * Save data to DB
     * @return bool
     */    
    public function saveToDB_Gruppi()
    {
        $db = Zend_Registry::get("db");
        $db->beginTransaction();
        // REMOVE all groups from listini_groups
        $idgroup_master = $this->getMasterGroup()->getIdGroup();
        $idlistino = $this->getMasterGroup()->getId();
        $resd = $db->query("DELETE FROM listini_groups WHERE idlistino='$idlistino' AND idgroup_master='$idgroup_master'");
        if(!$resd) {
            $db->rollBack();
            return false;
        }
        // prepare SQL INSERT
        $sth_insert = $db->prepare("INSERT INTO listini_groups SET idlistino= :idlistino, idgroup_master= :idgroup_master, idgroup_slave= :idgroup_slave, valido_dal= :valido_dal, valido_al= :valido_al, visibile= :visibile");
        foreach($this->getAllGroups() AS $group) {
            $res = $sth_insert->execute($group->dumpValuesForDB());
            if(!$res) {
                $db->rollBack();
                return false;
            }
        }
        return $db->commit();
    }
    

}
