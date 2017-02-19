</div>
<footer>
  <div class="container">
    <div class="row">
      <?php if ($informations) { ?>
      <div class="col-sm-4">
        <h5><?php echo $text_information; ?></h5>
        <ul class="list-unstyled">
          <?php foreach ($informations as $information) { ?>
          <li><a href="<?php echo $information['href']; ?>"><?php echo $information['title']; ?></a></li>
          <?php } ?>
        </ul>
      </div>
      <?php } ?>
      <div class="col-sm-4">
        <h5><?php echo $text_service; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $contact; ?>"><?php echo $text_contact; ?></a></li>
          <li><a href="<?php echo $return; ?>"><?php echo $text_return; ?></a></li>
          <li><a href="<?php echo $sitemap; ?>"><?php echo $text_sitemap; ?></a></li>
        </ul>
      </div>
      <div class="col-sm-4">
        <h5><?php echo $text_extra; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $manufacturer; ?>"><?php echo $text_manufacturer; ?></a></li>
          <li><a href="<?php echo $voucher; ?>"><?php echo $text_voucher; ?></a></li>
          <li><a href="<?php echo $affiliate; ?>"><?php echo $text_affiliate; ?></a></li>
          <li><a href="<?php echo $special; ?>"><?php echo $text_special; ?></a></li>
        </ul>
        <h5><?php echo $text_account; ?></h5>
        <ul class="list-unstyled">
          <li><a href="<?php echo $account; ?>"><?php echo $text_account; ?></a></li>
          <li><a href="<?php echo $order; ?>"><?php echo $text_order; ?></a></li>
          <li><a href="<?php echo $wishlist; ?>"><?php echo $text_wishlist; ?></a></li>
          <li><a href="<?php echo $newsletter; ?>"><?php echo $text_newsletter; ?></a></li>
        </ul>
      </div>
      <?php 
	  //TODO: Comment unused social links:
	  /*<div class="col-sm-3">
    <h5 class="header"><?php echo $text_social_Links; ?></h5>
    <ul class="content list-unstyled">
      <li> <a title="Follow us on <?php echo $text_twitter; ?>" target="_blank" href="https://www.twitter.com/websiteskin"><span class="social twitter">&nbsp;</span><?php echo $text_twitter; ?></a> 
      </li>
      <li> <a href="https://www.facebook.com/websiteskin" target="_blank" title="Follow us on <?php echo $text_facebook; ?>" ><span class="social facebook" >&nbsp;</span><?php echo $text_facebook; ?></a> 
      </li>
      <li>
<a href="https://plus.google.com/+KatrinKopytina" target="_blank" title="Follow us on <?php echo $text_google; ?>" ><span class="social google" >&nbsp;</span><?php echo $text_google; ?></a> 
      </li>
      <li>
<a href="http://www.pinterest.com/websiteskin/pins/" target="_blank" title="Find us on <?php echo $text_pinterest; ?>"><span class="social pinterest" >&nbsp;</span><?php echo $text_pinterest; ?></a> 
      </li>
      <li>
<a href="http://websiteskin.blogspot.ru/" target="_blank" title="Follow us on <?php echo $text_blogspot; ?>" ><span class="social blogspot" >&nbsp;</span><?php echo $text_blogspot; ?></a>
      </li>
    </ul>

      </div>*/
	  ?>
    </div>
    <hr>
    <div class="row">
      <center>
		<p><?php echo $powered; ?></p> 
      </center>
    </div>
  </div>
<script type="text/javascript"><!--
		$(document).ready(function() {
			/*
			var defaults = {
	  			containerID: 'toTop', // fading element id
				containerHoverID: 'toTopHover', // fading element hover id
				scrollSpeed: 1200,
				easingType: 'linear' 
	 		};
			*/
			
			$().UItoTop({ easingType: 'easeOutQuart' });
			
		});
--></script>

</footer>

</body></html>
