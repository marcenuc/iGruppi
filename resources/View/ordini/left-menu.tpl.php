<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" href="#collapseOne"><span class="glyphicon glyphicon-filter"></span> Stato</a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
    <?php 
    if(count($this->statusArray) > 0):
        foreach ($this->statusArray as $key => $stato): ?>
        <?php if( $this->fObj->hasFilterByField("stato") && $stato == $this->fObj->getFilterByField("stato") ): ?>
        <a class="selected" data-toggle="tooltip" title="Rimuovi filtro" href="<?php echo $this->fObj->createUrlForStato($stato, true); ?>"><?php echo $stato; ?> <span class="glyphicon glyphicon-remove pull-right"></span></a>
        <?php else: ?>
        <a href="<?php echo $this->fObj->createUrlForStato($stato); ?>"><?php echo $stato; ?></a>
        <?php endif; ?>
    <?php 
        endforeach; 
    endif; ?>
      </div>
    </div>
  </div>
  <!--
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" href="#collapseThree">Periodo</a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse<?php //if($this->fObj->hasFilterByField("periodo")) { echo " in"; } ?>">
      <div class="panel-body">
        <h4><a href="<?php //echo $this->fObj->createUrlForPeriodo("201310"); ?>">Ottobre 2013</a></h3>
        <h4><a href="<?php //echo $this->fObj->createUrlForPeriodo("201309"); ?>">Settembre 2013</a></h3>
      </div>
    </div>
  </div>
  -->
</div>
<script>    
    $('.selected').tooltip('hide');
</script>
