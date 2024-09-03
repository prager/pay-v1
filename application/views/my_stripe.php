<!DOCTYPE html>
<html>
<head>
    <title>Pay v1-0</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.7/css/bootstrap.min.css" />
    <!-- MDARC icon v1 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <link rel="shortcut icon" href="<?php echo base_url() ;?>assets/img/mdarc-icon.ico" type="image/x-icon" />
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
                    $payType = 'MDARC Payments';
                }

                if($payment_type == 'donation') {
                    $payType = 'Donation to MDARC';
                }
            ?>
			<h2><?php echo $payType; ?></h2>
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
                    <form role="form" action="<?php echo base_url(); ?>index.php/stripePost" method="post" class="require-validation" data-cc-on-file="false" data-stripe-publishable-key="<?php echo $this->config->item('mdarc_key') ?>" id="payment-form">
					
                        <div class='form-row row'>
                            <div class='col-sm-12 form-group required'>
                                <label>Name on Card</label> 
                                <input class="form-control" size="4" type="text" name="cc_name" value="<?php if($member->lname != 'NullMember') echo $member->fname . ' ' . $member->lname; ?>">
                            </div>
                        </div>

						<div class='form-row row'>
                            <div class='col-sm-12 form-group'>
                                <label>Street Address</label> <input class="form-control" size="4" type="text" value="<?php if($member->lname != 'NullMember') echo $member->address; ?>">
                            </div>
                        </div>

						<div class='form-row row'>
                            <div class='col-sm-12 col-md-4 form-group'>
                                <label>City</label> <input class="form-control" size="18" type="text" value="<?php if($member->lname != 'NullMember') echo $member->city; ?>">
                            </div>

                            <div class='col-sm-12 col-md-4 form-group'>
                                <label>State</label> <input class="form-control" size="2" type="text" value="<?php if($member->lname != 'NullMember') echo $member->state; ?>">
                            </div>

                            <div class='col-sm-12 col-md-4 form-group'>
                                <label>Zip</label> <input class="form-control" size="5" type="text" value="<?php if($member->lname != 'NullMember') echo $member->zip; ?>">
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
                            <?php   
                            $actionTxt = '';
                            if($action == 'renewal' || $action == 'new_mem' || $action == 'public_renew') { ?>
                                <div class="col-sm-12 col-md-2">
                                    &nbsp;
                                </div>
                                <div class="col-sm-12 col-md-4 form-group">
                                    <?php $actionTxt = ' ' . $cur_year . ' Membership'; ?>
                                        <label><input type="checkbox" value="" checked disabled><?php echo $actionTxt ." $". number_format($charges['membership'], 2); ?></label> 
                                        <input type='hidden' name='memcharge' value= <?php echo strval($charges['membership']); ?>/>
                                </div>
                                <div class="col-sm-12 col-md-4 form-group">
                                    <label><input type="checkbox" id="carrier" name="carrier" checked disabled> Mail The Carrier $<?php echo strval(number_format($charges['carrier'], 2)); ?></label>
                                    <input type='hidden' name='carrier' value=<?php echo strval($charges['carrier']); ?>/>                            
                                </div>
                            <?php    }  ?>
                                <div class="row">&nbsp;</div>
                                <div class="col-sm-12 col-md-2">
                                    &nbsp;
                                </div>
                                <div class="col-sm-12 col-md-12 form-group">                                    
                                    <?php $actionTxt = ' Total amount that will be charged for Donation to MDARC'; ?>
                                        <label><input type="checkbox" value="" checked disabled><?php echo $actionTxt ." $". number_format($charges['mdarc'], 2); ?></label>
                                        <input type='hidden' name='mdarc' value=<?php echo strval($charges['mdarc']); ?>/>
                                </div>
                            
                            <!-- <div class="col-sm-12 col-md-4 form-group">                                    
                                <?php $actionTxt = ' Donation (repeater)'; ?>
                                    <label><input type="checkbox" value="" checked disabled><?php echo $actionTxt ." $". number_format($charges['repeater'], 2); ?></label>
                                    <input type='hidden' name='repeater' value=<?php echo strval($charges['repeater']); ?>/>
                            </div>  -->
                            <div class='col-sm-12 col-md-12'>
                                <hr>
                            </div>
                            <div class="col-sm-12 col-md-12 text-center">
                                <label>Total charges: $<?php echo number_format($charges['membership'] + $charges['carrier'] + $charges['repeater'] + $charges['mdarc'],2); ?></label>
                            </div>
                            <div class="col-sm-12 col-md-12">
                                <input type='hidden' name='idstr' value=<?php echo $idStr; ?>/>
                                <input type='hidden' name='action' value=<?php echo $action; ?>/>
                                <input type='hidden' name='cur_year' value=<?php echo $cur_year; ?>/>
                                <input type='hidden' name='payment_type' value=<?php echo $payment_type; ?>/>
                                <input type='hidden' name='lname' value=<?php echo $member->lname; ?>/>
                                &nbsp;
                            </div>
                        </div>
                        <div class='form-row row'>
                            <div class='col-md-12 error form-group hide'>
                                <div class='alert-danger alert'>Please correct the errors and try again.</div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-2">&nbsp;</div>
                            <div class="col-sm-4">
                                <button class="btn btn-primary btn-lg btn-block" type="submit">Submit Payment</button>
                            </div>
                            <div class="col-sm-4">
                                <a href="https://mdarc-dev.jlkconsulting.info" type="button" class="btn btn-default btn-lg btn-block">Cancel Payment</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div> 
        </div>
    </div>
    <div class="row">
        <div class="col">
            &nbsp;
        </div>
    </div>
    <div class="row">
        <div class="col-ms-10 col-md-offset-1">
            <hr>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <?php //echo phpinfo(); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-4 col-md-offset-2">
            &copy; <a href="https://jlkconsulting.info" target="_blank">JLK Consulting</a>
        </div>
        <div class="col-sm-4 text-right">
            <a href="https://stripe.com/docs/testing#cards" target="_blank" class="text-decoration-none">Testing Mode</a> | <a href="<?php echo base_url();?>index.php/about" target="_blank">About</a>
        </div>
    </div>
    <div class="row" style="height: 60px; ">
        <div class="col">
            &nbsp;
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