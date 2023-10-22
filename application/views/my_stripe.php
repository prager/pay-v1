<!DOCTYPE html>
<html>
<head>
    <title>Pay v1</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style type="text/css">

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 16px;
        }

        h2 {
            font-weight: bold;
        }

        .panel-title {
            display: inline;
            font-weight: bold;
            font-size: 20px;
        }

        .display-table {
            display: table;
        }

        .display-tr {
            display: table-row;
        }

        .display-td {
            display: table-cell;
            vertical-align: middle;
            width: 70%;
        }

        .lbl-font {

            font-size: 16px;
        }
    </style>
</head>
<body>
<div class="container">
	<div class="row">
		<div class="col text-center">
            <?php 
                $payType = '';
                if($payment_type == 'mdarc-payment') {
                    $payType = 'MDARC';
                }
            ?>
			<h2><?php echo $payType; ?> Payments</h2>
			Inspired by <a href="https://www.itsolutionstuff.com/post/stripe-payment-gateway-integration-in-codeigniter-exampleexample.html" target="_blank">Hardik Savani</a><br>
            <small><a href="https://stripe.com/docs/testing#cards" target="_blank" class="text-decoration-none">(Testing Mode)</a></small>
		</div>
	</div>
	<div class="row">
		<div class="col">&nbsp;</div>
	</div>
    <div class="row mt-3">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default credit-card-box">
                <div class="panel-heading display-table" >
                    <div class="row display-tr" >
                        <h2 class="panel-title display-td">Payment Details</h2>
                        <div class="display-td" >
                            <img class="img-responsive pull-right" src="https://files.kulisek.org/cc.png">
                        </div>
                    </div> 
                </div>
                <div class="panel-body">
                    <?php if($this->session->flashdata('success')){ ?>
                    <div class="alert alert-success text-center">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
                            <p><?php echo $this->session->flashdata('success'); ?></p>
                        </div>
                    <?php } ?>

                    <form role="form" action="<?php echo base_url(); ?>index.php/stripePost" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="<?php echo $this->config->item('stripe_key') ?>" id="payment-form">
					
                        <div class='form-row row'>
                            <div class='col-sm-12 form-group required'>
                                <label>Name on Card</label> <input class='form-control' size='4' type='text'>
                            </div>
                        </div>

						<div class='form-row row'>
                            <div class='col-sm-12 form-group'>
                                <label>Street Address</label> <input class='form-control' size='4' type='text'>
                            </div>
                        </div>

						<div class='form-row row'>
                            <div class='col-sm-12 col-md-4 form-group'>
                                <label>City</label> <input class='form-control' size='18' type='text'>
                            </div>

                            <div class='col-sm-12 col-md-4 form-group'>
                                <label>State</label> <input class='form-control' size='2' type='text'>
                            </div>

                            <div class='col-sm-12 col-md-4 form-group'>
                                <label>Zip</label> <input class='form-control' size='5' type='text'>
                            </div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-sm-12 form-group card required'>
                                <label>Card Number</label> <input autocomplete='off' class='form-control card-number' size='20' type='text'>
							</div>
                        </div>

                        <div class='form-row row'>
                            <div class='col-sm-12 col-md-4 form-group cvc required'>
                                <label>CVC</label> <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text'>
                            </div>

                            <div class='col-sm-12 col-md-4 form-group expiration required'>
                                <label>Expiration Month</label> <input class='form-control card-expiry-month' placeholder='MM' size='2' type='text'>
                            </div>
                            <div class='col-sm-12 col-md-4 form-group expiration required'>
                                <label>Expiration Year</label> <input class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text'>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                &nbsp;
                            </div>
                            <div class='col-sm-12 col-md-4 form-group'>
                                <?php
                                    $actionTxt = '';
                                    if($action == 'renewal') {
                                        $actionTxt = ' ' . $cur_year . ' Membership'; ?>
                                        <label><input type="checkbox" value="" checked disabled><?php echo $actionTxt ." $". number_format($charges['membership'], 2); ?></label> 
                                        <input type='hidden' name='memcharge' value= <?php echo strval($charges['membership']); ?>/>
                                        <input type='hidden' name='action' value=<?php echo $action; ?>/>
                                <?php    }  ?>
                            </div>
                            <?php if($charges['carrier'] > 0) { ?>
                                <div class='col-sm-12 col-md-4 form-group'>
                                            <label><input type="checkbox" id="carrier" name="carrier" checked disabled> Mail The Carrier $<?php echo strval(number_format($charges['carrier'], 2)); ?></label>
                                            <input type='hidden' name='carrier' value=<?php echo strval($charges['carrier']); ?>/>
                                </div>
                            <?php }?>
                            <?php if($charges['repeater'] > 0) { ?>
                                <div class='col-sm-12 col-md-4 form-group'>
                                            <label><input type="checkbox" id="repeater" name="repeater" checked disabled> Donation (repeater) $<?php echo strval(number_format($charges['repeater'], 2)); ?></label>
                                            <input type='hidden' name='repeater' value=<?php echo strval($charges['repeater']); ?>/>
                                </div>
                            <?php }?>
                            <div class='col-sm-12 col-md-12'>
                                <hr>
                            </div>
                            <div class="col-sm-12 col-md-12 text-center">
                                <label>Total charges: $<?php echo number_format($charges['membership'] + $charges['carrier'] + $charges['repeater'],2); ?></label>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                &nbsp;
                            </div>
                        </div>
                        <div class='form-row row'>
                            <div class='col-md-12 error form-group hide'>
                                <div class='alert-danger alert'>Please correct the errors and try again.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <button class="btn btn-primary btn-lg btn-block" type="submit">Submit Payment</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>
</div>
</body> 
<script type="text/javascript" src="https://js.stripe.com/v2/"></script>
<script type="text/javascript">

$(function() {
	var $form = $(".require-validation");
  $('form.require-validation').bind('submit', function(e) {
    var $form         = $(".require-validation"),
        inputSelector = ['input[type=email]', 'input[type=password]',
                         'input[type=text]', 'input[type=file]',
                         'textarea'].join(', '),
        $inputs       = $form.find('.required').find(inputSelector),
        $errorMessage = $form.find('div.error'),
        valid         = true;
        $errorMessage.addClass('hide');
        $('.has-error').removeClass('has-error');
    $inputs.each(function(i, el) {
      var $input = $(el);
      if ($input.val() === '') {
        $input.parent().addClass('has-error');
        $errorMessage.removeClass('hide');
        e.preventDefault();
      }
    });

    if (!$form.data('cc-on-file')) {
      e.preventDefault();
      Stripe.setPublishableKey($form.data('stripe-publishable-key'));
      Stripe.createToken({
        number: $('.card-number').val(),
        cvc: $('.card-cvc').val(),
        exp_month: $('.card-expiry-month').val(),
        exp_year: $('.card-expiry-year').val()
      }, stripeResponseHandler);
    }
  });

  function stripeResponseHandler(status, response) {
        if (response.error) {
            $('.error')
                .removeClass('hide')
                .find('.alert')
                .text(response.error.message);
        } else {
            var token = response['id'];
            $form.find('input[type=text]').empty();
            $form.append("<input type='hidden' name='stripeToken' value='" + token + "'/>");
            $form.get(0).submit();
        }
    }
});
</script>
</html>
