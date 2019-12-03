<?php

namespace App\Leads;

class ClubEssential extends Base {

	public function __construct() {
		$this->setAttribute("type", self::$TYPE_CLUBESSENTIAL);
		parent::__construct();
	}

	/*
	Array
	(
		[0] => Array
		(
			[FormID] => 1
			[FormTitle] => Membership Inquiry
			[SubmissionID] => 2280
			[UserID] => 0
			[Cancelled] =>
			[URL] => https://www.twincreeksclub.com/default.aspx?p=.NETDynamicFormView&s=2280&f=79b34337-7327-4ea1-b986-a78fba723c7e
			[_1_1_Title] =>
			[_1_2_First_Name] => Bradley
			[_1_3_Last_Name] => McClinton
			[_1_4_Company] => General Motors
			[_1_5_Street_Address] => 1401 Little Elm Trl
			[_1_101_City_x003A_] => Cedar Park
			[_1_102_State] => TX
			[_1_103_Zip_Code] => 78613
			[_1_7_Home_Phone] => 2544246131
			[_1_8_Cell_Phone] =>
			[_1_9_Email_Address_x003A__] => Ryan_McClinton@baylor.edu
			[_1_10_How_would_you_like_to_be_contacted] => Email Address
			[_1_11_Best_time_to_contact_you] => Morning
			[_1_12_How_did_you_hear_about_Twin_Creeks_Country_Club_x003F_] => Research
			[_1_13_Would_you_like_to_be_added_to_our_email_distribution_list_x003F_] =>
			[_1_14_I_would_like_to_learn_more_about_this_type_of_membership] =>
			[_1_15_I_would_like_to_apply_for_membership_in] => Just inquiring
		)
	)
	*/

	public function normalize($data = []) {

		$out = [];

		\Log::info(print_r($data[0],1));

		$out['sub_type']   = strstr(strtolower($data[0]['FormTitle']), "member") ? "member" : "event";
		$out['source']     = preg_replace("/^www\./", "", strtolower(parse_url((preg_match("/^http/", $data[0]['URL']) ? "" : "http://") . $data[0]['URL'], PHP_URL_HOST)));
		$out['utm_source'] = "Website";

		foreach($data[0] AS $k => $v) {

			$k = strtolower($k);

			switch(true) {

				case strstr($k, "email_") && filter_var($v, FILTER_VALIDATE_EMAIL):
				case strstr($k, "email_address"):
					$out['email'] = $v;
					break;

				case strstr($k, "first_name"):
					$out['first_name'] = $v;
					break;

				case strstr($k, "last_name"):
					$out['last_name'] = $v;
					break;

				case strstr($k, "home_phone"):
					$out['phone'] = $v;
					break;

				case strstr($k, "company"):
					$out['company_title'] = $v;
					break;

				case strstr($k, "street_address"):
					$out['address_1'] = $v;
					break;

				case strstr($k, "_city_"):
					$out['address_city'] = $v;
					break;

				case strstr($k, "_state"):
					$out['address_state'] = $v;
					break;

				case strstr($k, "zip_code"):
					$out['address_zip'] = $v;
					break;

				default:
					//$field = ucwords(str_replace("_", " ", preg_replace("/^_\d{1,2}_(\d{1,2})_/", "", $k)));
					$field = preg_replace("/^_\d{1,2}_\d{1,2}_/", "", $k);
					$field = preg_replace("/x003f/", "", strtolower($field));
					$field = preg_replace("/_+$/", "", $field);
					$out[$field] = $v;
					break;

			}

		}

		switch(true) {
			case !$out['email']:
				throw new \Exception("Missing required email address, aborting...");
				break;
			case !filter_var($out['email'], FILTER_VALIDATE_EMAIL):
				throw new \Exception("Invalid email address ({$out['email']}), aborting...");
				break;
			case !$out['last_name']:
				throw new \Exception("Reserve Interactive requires a contact to have a last name. No last name provided, aborting...");
				break;
		}

		//\Log::info(print_r($out,1));

		return $out;

	}

}
