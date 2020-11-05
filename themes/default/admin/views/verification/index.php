<div id="content">
    <div class="row">
    		<?php
			foreach($url_data['user'] as $verify){
			?>
            <div class="col-lg-3 col-xs-6">
              <div class="small-box <?= $verify['color'] ?>">
                <div class="inner" style="margin-bottom:0px; padding:15px;">
                  <h3><?= $verify['title'] ?> </h3>
                 <h3 class="text-center"><?= $verify['inactive'] ?> </h3>
                </div>
                <div class="icon">
                  <i class="fa fa-id-card-o" aria-hidden="true"></i>
                </div>
                <a href="<?= $verify['link'] ?>" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
              </div>
            </div>
            <?php
			}
			?>
          </div>
</div>