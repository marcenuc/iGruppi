<form id="edit_listino_form" action="<?php echo $this->form->getAction(); ?>" method="post" class="f1n120">

    <div class="pull-right">
        <button type="submit" class="btn btn-success btn-mylg">SALVA</button>
    </div>


    <h2 class="titolo">Modifica Listino <strong><?php echo $this->listino->getDescrizione(); ?></strong></h2>
    <h3 class="subtitolo">Produttore: <strong><?php echo $this->listino->getProduttoreName(); ?></strong><br />
        <small>Data Listino: <strong id="listino_user_update"><?php echo $this->date( $this->listino->getDataListino(), '%d/%m/%Y' ); ?></strong> 
<?php if($this->listino->canSetDataListino()): ?>
            <button type="button" data-loading-text="Aggiorno..." class="btn btn-warning btn-xs" id="btn_listino_user_update" href="javascript:void(0);" onclick="jx_ListinoUpdateData(<?php echo $this->listino->getIdListino(); ?>);">Aggiorna</button>
<?php endif; ?>
        </small>
    </h3>
    <div class="row">
      <div class="col-md-12">

<?php if($this->updated): ?>
        <div class="alert alert-success alert-dismissable">
          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
          Listino aggiornato con <strong>successo</strong>!
        </div>
<?php endif; ?>
      
        <ul class="nav nav-tabs" id="myTab">
          <li class="active"><a href="#dati" data-toggle="tab">Dati listino</a></li>
          <li><a href="#sharing" data-toggle="tab">Condivisione</a></li>
          <li><a href="#prodotti" data-toggle="tab">Prodotti</a></li>
    <?php 
        $outOfListino = $this->listino->countOutOfListino();
        if($outOfListino): ?>
          <li><a href="#new-prodotti" data-toggle="tab">Nuovi Prodotti <span class="badge"><?php echo $outOfListino; ?></span></a></li>
    <?php endif; ?>
        </ul>
      
        <div class="tab-content">
          <div class="tab-pane active" id="dati">
              <?php include $this->template('listini/edit.dati.tpl.php'); ?>
          </div>
          <div class="tab-pane" id="sharing">
              <?php include $this->template('listini/edit.condivisione.tpl.php'); ?>
          </div>
          <div class="tab-pane" id="prodotti">
              <?php echo $this->partial('listini/edit.prodotti.tpl.php', array('listino' => $this->listino)); ?>
          </div>
    <?php if($outOfListino): ?>
          <div class="tab-pane" id="new-prodotti">
              <?php echo $this->partial('listini/edit.new-prodotti.tpl.php', array('listino' => $this->listino)); ?>
          </div>
    <?php endif; ?>
        </div>

        <?php echo $this->form->renderField('idproduttore'); ?>
        <?php echo $this->form->renderField('idlistino'); ?>
  </div>
</div>
</form>
<script>
    $(function() {
        // get Hash from URL
        hash = window.location.hash;
        // set TAB by hash (if it exists)
        $('a[href="' + hash + '"]').tab('show');
        
        // Run always to init
        setCondivisione();
        $('#condivisione').change(setCondivisione);
        setValidita();
        $('#validita').change(setValidita);
        
        $('#valido_dal').datetimepicker({
            lang:   'it',
            format: 'd/m/Y',
            timepicker:false,
            onShow: function( ct ){
                if($('#valido_al').val())
                {
                    this.setOptions({ 
                        maxDate: moment($('#valido_al').val(), "DD/MM/YYYY").subtract('days', 1).format("YYYY/MM/DD")
                    })
                }
            }
        });
        
        $('#valido_al').datetimepicker({
            lang:'it',
            format:'d/m/Y',
            timepicker:false,
            onShow:function( ct ){
                if($('#valido_dal').val())
                {
                    this.setOptions({ 
                        minDate: moment($('#valido_dal').val(), "DD/MM/YYYY").add('days', 1).format("YYYY/MM/DD")
                    })
                }
              }
        });
    });
    
    function setCondivisione()
    {
        if($('#condivisione').val() === "SHA")
        {
            $('#d_sharing').show();
        } else {
            $('#d_sharing').hide();
        }        
    }
    
    function setValidita()
    {
        if($('#validita').val() === "S")
        {
            $('#d_validita').show();
        } else {
            $('#d_validita').hide();
        }        
    }
    
</script>