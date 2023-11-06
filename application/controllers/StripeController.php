<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*  
* Inspired by:
* https://www.itsolutionstuff.com/post/stripe-payment-gateway-integration-in-codeigniter-exampleexample.html
*/

class StripeController extends CI_Controller {

    /**
     * Get All Data from this method.
	 * 
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->library("session");
       $this->load->helper('url');
    }

    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index() {
		$this->Manager_model->reset_flags();
	// retrieve values from uri
		$data['payment_type'] = $this->uri->segment(1);
		$data['action'] = $this->uri->segment(2);
		$idStr = $this->uri->segment(3);
		if($idStr != '')
			{

		// must retrieve payment data from mem_payments table
			$paydata = $this->Manager_model->get_paydata($idStr);
			if($paydata['id_member'] == 0) {
				echo 'There was a database error. Please, try again...';
			}
			else {
				$memData = $this->Manager_model->get_member($paydata['id_member']);
				$data['member'] = $memData['member'];

			// this is not correct. need a function to get the correct cur_year
				$data['cur_year'] = $memData['cur_year'];
				if($data['action'] == 'renewal') {
					$data['charges'] = array("membership" => $paydata['renewal'], "carrier" => $paydata['carrier'], "repeater" => $paydata['repeater_donation'], "mdarc" => $paydata['mdarc_donation']);
				}
				if($data['action'] == 'public_renew') {
					$data['charges'] = array("membership" => $paydata['public_renew'], "carrier" => $paydata['carrier'], "repeater" => $paydata['repeater_donation'], "mdarc" => $paydata['mdarc_donation']);
				}
				else if($data['action'] == 'new_mem') {
					$data['charges'] = array("membership" => $paydata['new_mem'], "carrier" => $paydata['carrier'], "repeater" => $paydata['repeater_donation'], "mdarc" => $paydata['mdarc_donation']);
				}
				else if($data['action'] == 'donation') {
					$data['charges'] = array("membership" => 0, "carrier" => 0, "repeater" => $paydata['repeater_donation'], "mdarc" => $paydata['mdarc_donation']);
				}

				$data['idStr'] = $idStr;

			// pass values for membership year, sum charged	
				$this->load->view('my_stripe', $data);
			}
		}
		else {
			echo 'Insufficient data!';
		}
    }

	public function about() {
		$this->load->view('readme');
	}

    /**
     * Get All Data from this method.
     * @return Response
    */
    public function stripePost() {
		
		$memVal = floatval($this->input->post('memcharge'));
		$carrVal = floatval($this->input->post('carrier'));
		$repVal = floatval($this->input->post('repeater'));
		$mdarcVal = floatval($this->input->post('mdarc'));
		$totCharge = $memVal + $carrVal + $repVal + $mdarcVal;
		$idStr = $this->input->post('idstr');
		$param['idStr'] = substr($idStr, 0, strlen($idStr) - 1);
		$param['cur_year'] = $this->input->post('cur_year');
		$actStr = $this->input->post('action');
		$param['action'] = substr($actStr, 0, strlen($actStr) - 1);
		$paymTypeStr =  $this->input->post('payment_type');
		$paymTypeStr = substr($paymTypeStr, 0, strlen($paymTypeStr) - 1);
		$param['carrVal'] = $carrVal;
		$description = $paymTypeStr . ' ' . $param['action'];
		require_once('application/libraries/stripe-php/init.php');
		try {
			\Stripe\Stripe::setApiKey($this->config->item('mdarc_secret'));
			\Stripe\Charge::create ([
					"amount" => $totCharge * 100,
					"currency" => "usd",
					"source" => $this->input->post('stripeToken'),
					"description" => $this->input->post('cc_name') . ' ' . $description
			]);
			$param['status'] = 'success';
			$this->Manager_model->save_paydata($param);
			if($param['action'] == 'renewal') {
				if($memVal > 0 && ($repVal > 0 || $mdarcVal > 0)) {
					header("Location: https://mdarc-dev.jlkconsulting.info/index.php/member/memdon_ok");
				}
				else if ($memVal > 0 && $repVal == 0 && $repVal == 0) {
					header("Location: https://mdarc-dev.jlkconsulting.info/index.php/member/mem_ok");
				}
			}
			else if($param['action'] == 'public_renew') {
				if($memVal > 0 && ($repVal > 0 || $mdarcVal > 0)) {
					header("Location: https://mdarc-dev.jlkconsulting.info/index.php/public-renewdon");
				}
				else if ($memVal > 0 && $repVal == 0 && $repVal == 0) {
					header("Location: https://mdarc-dev.jlkconsulting.info/index.php/public-renew");
				}
			}			
			else if($param['action'] == 'donation') {
				header("Location: https://mdarc-dev.jlkconsulting.info/index.php/member/donation_ok");
			}
			else if($param['action'] == 'new_mem') {
				header("Location: https://mdarc-dev.jlkconsulting.info/index.php/new-member");
			}
			exit;
		}
		catch (\Stripe\Exception\CardException $e) {
			  // Since it's a decline, \Stripe\Exception\CardException will be caught
			echo "Status is: " . $e->getHttpStatus() . "<br />";
			echo 'Type is: ' . $e->getError()->type . '<br />';
			echo 'Code is: ' . $e->getError()->code . '<br />';
			// param is '' in this case
			echo 'Param is: ' . $e->getError()->param . '<br />';
			echo 'Message is: ' . $e->getError()->message . '<br />';
		} 
		catch (\Stripe\Exception\RateLimitException $e) {
			// Too many requests made to the API too quickly
			$param['status'] = 'rate_failure';
			$this->Manager_model->save_paydata($param);
			echo 'Too many requests!';
		} 
		catch (\Stripe\Exception\InvalidRequestException $e) {
			// Invalid parameters were supplied to Stripe's API
			echo 'Invalid parameters sent!';
		} 
		catch (\Stripe\Exception\AuthenticationException $e) {
			// Authentication with Stripe's API failed
			// (maybe you changed API keys recently)
			$param['status'] = 'auth_failure';
			$this->Manager_model->save_paydata($param);
			echo 'Authentication error!';
		} 
		catch (\Stripe\Exception\ApiConnectionException $e) {
			// Network communication with Stripe failed
			$param['status'] = 'comm_failure';
			$this->Manager_model->save_paydata($param);
			echo 'Network connection failed!';
		} 
		catch (\Stripe\Exception\ApiErrorException $e) {
			// Display a very generic error to the user, and maybe send
			// yourself an email
			$param['status'] = 'gen_failure';
			$this->Manager_model->save_paydata($param);
			echo 'Very generic error. Who knows what is this!';
		} 
		catch (Exception $e) {
			// Something else happened, completely unrelated to Stripe
			$param['status'] = 'unk_failure';
			$this->Manager_model->save_paydata($param);
			echo 'An error that is completely unrelated to Stripe.';
		}
	}

	public function go_back() {
		if(ENVIRONMENT == 'development') {
			header("Location: https://mdarc-dev.jlkconsulting.info/");
		}
		else {
			header("Location: https://mdarc.jlkconsulting.info/");
		}
		exit;
	}
}
