<?php

$data = array();
do_action('wcbt/layout/compare/container/before', compact('data'));
?>
<div id="wcbt-compare-table" class="wcbt-container">
<!--   <div class="wcbt-container">-->
<!--       <h1 class="compare-page-title">--><?php //the_title(); ?><!--</h1>-->
<!--   </div>-->
   <div class="wcbt-container">
       <div class="wcbt-compare-box">
           <div class="wcbt-wave-loading">
           </div>
       </div>
   </div>
</div>
<?php
do_action('wcbt/layout/compare/container/after', compact('data'));

