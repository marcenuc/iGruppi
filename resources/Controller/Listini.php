<?php
/**
 * Description of Listini
 *
 * @author gullo
 */
class Controller_Listini extends MyFw_Controller {

    private $_userSessionVal;
    private $_iduser;
    
    function _init() 
    {
        $auth = Zend_Auth::getInstance();
        $this->_iduser = $auth->getIdentity()->iduser;
        $this->view->userSessionVal = $this->_userSessionVal = new Zend_Session_Namespace('userSessionVal');
    }

    function indexAction() 
    {
        // get Elenco Listini per Gruppo
        $lObj = new Model_Db_Listini();
        $cObj = new Model_Db_Categorie();
        $listiniArray = $lObj->getListiniByIdgroup($this->_userSessionVal->idgroup);
        $listini = array();
        if(!is_null($listiniArray)) {
            foreach ($listiniArray as $stdListino) {
                // creates Listino by Abstract Factory Model_AF_ListinoFactory
                $mllObj = new Model_Listini_Listino();
                $mllObj->appendDati()
                       ->appendGruppi()              
                       ->appendCategorie();
                // init Dati by stdClass
                $mllObj->initDati_ByObject($stdListino); 
                
                // set Categories in Listini object
                $categorie = $cObj->getCategoriesByIdListino( $mllObj->getIdListino() );
                // get CATEGORIE by array 
                $mllObj->initCategorie_ByObject($categorie);
                
                // set GROUPS in Listino
                $mllObj->initGruppi_ByObject( $lObj->getGroupsByIdlistino( $mllObj->getIdListino() ) );
                $mllObj->setMyIdGroup($this->_userSessionVal->idgroup);
                
                // IF can Manage Listino put the values at the TOP of array
                if( $mllObj->canManageListino() ) {
                    array_unshift($listini, $mllObj);
                } else {
                    // CHECK VALIDITA' e VISIBILITA'
                    if($mllObj->getValidita()->isValido() &&
                       $mllObj->getVisibile()->getBool() ) 
                    {
                        array_push($listini, $mllObj);
                    }
                }
            }
        }
        
        $this->view->listini = $listini;
        //Zend_Debug::dump($listini);die;
    }
    
    function addAction()
    {
        // init Listino form
        $form = new Form_Listino();
        $form->setAction("/listini/add");
        $form->removeField("idlistino");
        $form->removeField("valido_dal");
        $form->removeField("valido_al");
        $form->removeField("condivisione");
        $form->removeField("visibile");
        $form->removeField("idproduttore");
        
        // get Produttori
        $pObj = new Model_Db_Produttori();
        // modify inline the idproduttore field
        $form->addField('idproduttore', array(
                        'label'     => 'Produttore',
                        'type'      => 'select',
                        'required'  => true,
                        'options'   => $pObj->convertToSingleArray($pObj->getProduttoriByIdRef($this->_iduser), "idproduttore", "ragsoc")
            ));

        if($this->getRequest()->isPost()) {
            // get Post values
            $fv = $this->getRequest()->getPost();
            // check if values are valid
            if( $form->isValid($fv) ) 
            {   
                // BUILD a new Listino
                $mllObj = new Model_Listini_Listino();
                $mllObj->appendDati();
                $mllObj->appendGruppi();
                
                // Get Idproduttore from FORM values
                $idproduttore = $form->getValue("idproduttore");
                
                // set Dati
                $mllObj->setDescrizione($form->getValue("descrizione"));
                $mllObj->setIdProduttore($form->getValue("idproduttore"));
                $mllObj->setCondivisione("PRI"); // Default is Private
                if( $mllObj->saveToDB_Dati() ) 
                {
                    $idlistino = $mllObj->getIdListino();
                    // create a NEW group
                    $group = new stdClass();
                    $group->id = $idlistino;
                    $group->idgroup_master = $this->_userSessionVal->idgroup;
                    $group->idgroup_slave = $this->_userSessionVal->idgroup;
                    // add my group
                    $mllObj->addGroup($group);
                    $resSave = $mllObj->saveToDB_Gruppi();
                    
                    if($resSave) {
                        // ADD ALL prodotti to LISTINO
                        $lModel = new Model_Db_Listini();
                        $lModel->addProdottiToListinoByIdProduttore($idlistino, $idproduttore);

                        // REDIRECT to EDIT
                        $this->redirect("listini", "edit", array('idlistino' => $idlistino, 'updated' => true));
                    }
                }
            }            
        }
        // set Form in the View
        $this->view->form = $form;        
    }
    
    function editAction()
    {
        $idlistino = $this->getParam("idlistino");
        if(is_null($idlistino)) 
        {
            $this->redirect("listini", "index");
        }
        
        // init Listino DB Model to get data
        $lObj = new Model_Db_Listini();
        $listino = $lObj->getListinoById($idlistino);

        // Create Listino Chain objects
        $mllObj = new Model_Listini_Listino();
        $mllObj->appendDati()
               ->appendGruppi()
               ->appendProdotti()
               ->appendCategorie();
        
        // set DATI in Listino
        $mllObj->initDati_ByObject($listino);
        
        // check canManageListino, controllo per i furbi (non autorizzati)
        if(!$mllObj->canManageListino()) {
            $this->redirect("index", "error", array('code' => 401));
        }
        
        // set GROUPS in Listino
        $mllObj->initGruppi_ByObject( $lObj->getGroupsByIdlistino($idlistino) );
        $mllObj->setMyIdGroup($this->_userSessionVal->idgroup);
        // add All PRODOTTI by Listino
        $objModel = new Model_Db_Prodotti();
        $prodotti = $objModel->getProdottiByIdListino($idlistino);
        $mllObj->initProdotti_ByObject( $prodotti );

        // get CATEGORIE by array $prodotti
        $mllObj->initCategorie_ByObject($prodotti);

        // init Listino form
        $form = new Form_Listino();
        $form->setAction("/listini/edit/idlistino/$idlistino");
        
        /**
         * DISABLE some fields IF cannot manage them
         */
        if(!$mllObj->canEditName())
        {
            $form->getField("descrizione")->setDisabled();
        }
        if(!$mllObj->canSetValidita())
        {
            $form->getField("valido_dal")->setDisabled();
            $form->getField("valido_al")->setDisabled();
        }
        if(!$mllObj->canManageCondivisione())
        {
            $form->getField("condivisione")->setDisabled();
            $form->getField("groups")->setDisabled();
        }
        

        if($this->getRequest()->isPost()) {
            // get Post values
            $fv = $this->getRequest()->getPost();

            // set values null for validita if it was not set
            if( $mllObj->canSetValidita() && $fv["validita"] != "S" ) {
                $fv["valido_dal"] = $fv["valido_al"] = null;
            }
            // check if values are valid
            if( $form->isValid($fv) )
            {   
                // Save DATI if this use CAN (by Permissions)
                if($mllObj->canEditName()) {
                    $mllObj->setDescrizione($form->getValue("descrizione"));
                }
                if($mllObj->canManageCondivisione()) {
                    $mllObj->setCondivisione($form->getValue("condivisione"));
                    // Rest GROUPS by Sharing
                    $groupsToShare = isset($fv["groups"]) ? $fv["groups"] : array();
                    $mllObj->resetGroups($form->getValue("condivisione"), $groupsToShare);
                }
                if($mllObj->canSetValidita()) {
                    $mllObj->getMyGroup()->setValidita($form->getValue("valido_dal"), $form->getValue("valido_al"));
                }
                $mllObj->getMyGroup()->setVisibile( $form->getValue("visibile") );
                
                // SAVE ALL DATA CHANGED TO DB
                $resSaveDati = $mllObj->saveToDB_Dati();
                $resSaveGruppi = $mllObj->saveToDB_Gruppi();
                
                // REDIRECT
                if($resSaveDati && $resSaveGruppi) {
                    $this->redirect("listini", "edit", array('idlistino' => $idlistino, 'updated' => true));
                }
            }
        } else {
            // build array values for form
            $form->setValues($mllObj->getDatiValues());
            $form->setValue("valido_dal", $mllObj->getValidita()->getDal(MyFw_Form_Filters_Date::_MYFORMAT_DATE_VIEW));
            $form->setValue("valido_al", $mllObj->getValidita()->getAl(MyFw_Form_Filters_Date::_MYFORMAT_DATE_VIEW));
            $form->setValue("visibile", $mllObj->getVisibile()->getString());
            $form->setValue("groups", $mllObj->getAllIdgroups());

        }
        
        $this->view->listino = $mllObj;
        // set Form in the View
        $this->view->form = $form;
        $this->view->updated = $this->getParam("updated");
    }
    
    function viewAction()
    {
        $idlistino = $this->getParam("idlistino");
        if(is_null($idlistino)) 
        {
            $this->redirect("listini", "index");
        }
        
        // init Listino DB Model to get data
        $lObj = new Model_Db_Listini();
        $listino = $lObj->getListinoById($idlistino);

        // Create Listino Chain objects
        $mllObj = new Model_Listini_Listino();
        $mllObj->appendDati()
               ->appendGruppi()
               ->appendProdotti()
               ->appendCategorie();
        
        // set DATI in Listino
        $mllObj->initDati_ByObject($listino);
        
        // set GROUPS in Listino
        $mllObj->initGruppi_ByObject( $lObj->getGroupsByIdlistino($idlistino) );
        $mllObj->setMyIdGroup($this->_userSessionVal->idgroup);
        // add All PRODOTTI by Listino
        $objModel = new Model_Db_Prodotti();
        $prodotti = $objModel->getProdottiByIdListino($idlistino);
        $mllObj->initProdotti_ByObject( $prodotti );

        // get CATEGORIE by array $prodotti
        $mllObj->initCategorie_ByObject($prodotti);
        
        $this->view->listino = $mllObj;
    }

    
    function importaAction()
    {
        $layout = Zend_Registry::get("layout");
        $layout->disableDisplay();
        
        $idlistino = $this->getParam("idlistino");
        $idprodotto = $this->getParam("idprodotto");
        
        $lModel = new Model_Db_Listini();
        $res = $lModel->addProdottoToListinoByIdProdotto($idlistino, $idprodotto);
        $result = array('res' => $res);
        
        echo json_encode($result);
    }
    
    function updateprodottilistinoAction()
    {
        $layout = Zend_Registry::get("layout");
        $layout->disableDisplay();
        
        $idlistino = $this->getParam("idlistino");
        $idprodotto = $this->getParam("idprodotto");
        $field = $this->getParam("field");
        $value = $this->getParam("value");
        
        $lModel = new Model_Db_Listini();
        $res = false;
        switch ($field) {
            case "attivo_listino":
            case "costo_listino":
                $res = $lModel->updateListinoProdotti($idlistino, $idprodotto, $field, $value);
                break;
        }
        
        echo json_encode(array('res' => $res));
    }
    
    function updatedatalistinoAction()
    {
        $layout = Zend_Registry::get("layout");
        $layout->disableDisplay();
        
        $idlistino = $this->getParam("idlistino");
        $lModel = new Model_Db_Listini();
        $res = $lModel->updateDataListino($idlistino);
        echo json_encode(array('res' => $res));
    }    
}