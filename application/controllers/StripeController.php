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
	// retrieve values from uri
		$data['payment_type'] = $this->uri->segment(1);
		$data['action'] = $this->uri->segment(2);
		$id = intval($this->uri->segment(3));
		$data['member'] = $this->Manager_model->get_member($id);
		$data['cur_year'] = '2024';

	// temp test values
		$data['charges'] = array("membership" => 45.00, "carrier" => 0, "repeater" => 10, "mdarc" => 25);
	// retrieve values from db

	// pass values for membership year, sum charged	
        $this->load->view('my_stripe', $data);
    }

	public function about() {
		$this->load->view('readme');
	}

    /**
     * Get All Data from this method.
     * @return Response
    */
    public function stripePost() {
		try {
			$memVal = intval($this->input->post('memcharge'));
			$carrVal = intval($this->input->post('carrier'));
			$repVal = intval($this->input->post('repeater'));
			$mdarcVal = intval($this->input->post('mdarc'));
			$totCharge = $memVal + $carrVal + $repVal + $mdarcVal;
			require_once('application/libraries/stripe-php/init.php');
			\Stripe\Stripe::setApiKey($this->config->item('mdarc_secret'));
			\Stripe\Charge::create ([
					"amount" => $totCharge * 100,
					"currency" => "usd",
					"source" => $this->input->post('stripeToken'),
					"description" => "Testing Pay-v1 " . $this->input->post('action')
			]);
			//$this->load->view('payment_ok');
			header("Location: https://mdarc-dev.jlkconsulting.info/index.php/member");
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
			echo 'Too many requests!';
		} 
		catch (\Stripe\Exception\InvalidRequestException $e) {
			// Invalid parameters were supplied to Stripe's API
			echo 'Invalid parameters sent!';
		} 
		catch (\Stripe\Exception\AuthenticationException $e) {
			// Authentication with Stripe's API failed
			// (maybe you changed API keys recently)
			echo 'Authentication error!';
		} 
		catch (\Stripe\Exception\ApiConnectionException $e) {
			// Network communication with Stripe failed
			echo 'Network connection failed!';
		} 
		catch (\Stripe\Exception\ApiErrorException $e) {
			// Display a very generic error to the user, and maybe send
			// yourself an email
			echo 'Very generic error. Who knows what is this!';
		} 
		catch (Exception $e) {
			// Something else happened, completely unrelated to Stripe
			echo 'An error that is completely unrelated to Stripe.';
		}
	}
}
