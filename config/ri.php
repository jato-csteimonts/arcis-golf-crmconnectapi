<?php
return [
	"sites"  => [
		"427"  => "Ancala Country Club",
		"428"  => "Arrowhead Country Club",
		"186"  => "Arrowhead Golf Club",
		"126"  => "Bear Creek Golf Club",
		"412"  => "Broad Bay Golf Club (Traditions)",
		"113"  => "Canyon Springs Golf Club",
		"117"  => "Clear Creek Golf Club",
		"171"  => "Courses at Forest Park",
		"118"  => "Cowboys Golf Club",
		"163"  => "David L. Baker Golf Course",
		"DCGC" => "Deer Creek Golf Club",
		"746"  => "Desert Pines Golf Club",
		"498"  => "Eagle Brook Country Club",
		"405"  => "Fox Meadow Country Club",
		"114"  => "Golf Club at Cinco Ranch",
		"410"  => "Hunt Valley Golf Club",
		"717"  => "Iron Horse Golf Course",
		"242"  => "Kokopelli Golf Course",
		"702"  => "LaCentre",
		"401"  => "LakeRidge Country Club",
		"792"  => "Las Vegas Golf Club",
		"750"  => "Los Robles Golf Course",
		"703"  => "LPGA International",
		"659"  => "Majestic Oaks Golf Club",
		"487"  => "Meadowbrook Country Club",
		"152"  => "Meadowlark Golf Course",
		"658"  => "Mill Creek Golf Club",
		"411"  => "Montgomery Country Club",
		"177"  => "Painted Desert Golf Club",
		"696"  => "Park Hill Golf Club",
		"422"  => "Pinery Country Club",
		"116"  => "Plantation Golf Club",
		"227"  => "Raven Phoenix Golf Club",
		"425"  => "Ruby Hill Golf Club",
		"272"  => "Ruffled Feathers Golf Course",
		"783"  => "Shandin Hills Golf Club",
		"407"  => "Signature of Solon Country Club",
		"673"  => "Stonecreek Golf Club",
		"228"  => "Superstition Springs Golf Club",
		"TCC"  => "TallGrass Country Club",
		"674"  => "Tamarack Golf Club",
		"424"  => "Tartan Fields",
		"229"  => "Tatum Ranch Golf Club",
		"421"  => "The Club at Pradera",
		"423"  => "The Club at Snoqualmie Ridge",
		"740"  => "The Golf Club at Twin Creeks",
		"115"  => "The Golf Club Fossil Creek",
		"429"  => "The Legend at Arrowhead",
		"416"  => "The Oaks Club at Valencia",
		"418"  => "Valencia Country Club",
		"406"  => "Weymouth Country Club",
		"755"  => "Whitetail Ridge Golf Club",
	],
	"fields" => [
		"misc"   => [
			"member" => "clubLead.customData(0).tx00",
			"event"  => "lead.leadNotes.notes",
		],
		"status" => [
			"member" => "clubLead.leadStatus",
			"event"  => "lead.leadStatus",
		],
		"member" => [
			"member-lead"     => [
				"clubLead.customData(0).tx01"  => [
					"verbose"  => "campaign attribution",
					"possible" => [
						"campaign_attribution",
						"campaign",
					],
				],
				"clubLead.club"                => [
					"verbose"  => "club name",
					"possible" => [
						"club",
						"site",
					],
				],
				"clubLead.customData(0).tx06"  => [
					"verbose"  => "Preferred Visit Date",
					"possible" => [
						"preferred_date",
					],
				],
				"clubLead.customData(0).tx02"  => [
					"verbose"  => "do not use - inactive custom field",
					"possible" => [],
				],
				"clubLead.decisionDate"        => [
					"verbose"  => "decision date",
					"possible" => [],
				],
				"clubLead.description"         => [
					"verbose"  => "description",
					"possible" => [],
				],
				"clubLead.division.name"       => [
					"verbose"  => "division",
					"possible" => [
						"division",
						"divison",
					],
				],
				"clubLead.name"                => [
					"verbose"  => "lead name",
					"possible" => [
						"lead_name"
					],
				],
				"clubLead.customData(0).o00"   => [
					"verbose"  => "lead source",
					"possible" => [],
				],
				"clubLead.customData(0).tx04"  => [
					"verbose"  => "lead source details",
					"possible" => [],
				],
				"clubLead.leadStatus"          => [
					"verbose"  => "lead status",
					"possible" => [],
					"values"   => [
						"new" => "1 New",
					],
				],
				"clubLead.leadType"            => [
					"verbose"  => "lead type",
					"possible" => [
						//"sub_type"
					],
				],
				"clubLead.customData(0).tx07"  => [
					"verbose"  => "Lead type",
					"possible" => [
						"sub_type",
					],
				],
				"clubLead.customData(0).tx05"  => [
					"verbose"  => "lead/membership description",
					"possible" => [],
				],
				"clubLead.clubLeadNotes.notes" => [
					"verbose"  => "member lead notes",
					"possible" => [],
				],
				"clubLead.membershipTypeList"  => [
					"verbose"  => "new membership type",
					"possible" => [],
				],
				"clubLead.customData(0).tx00"  => [
					"verbose"  => "notes",
					"possible" => [],
				],
				"clubLead.customData(0).o02"   => [
					"verbose"  => "objections",
					"possible" => [],
				],
				"clubLead.customData(0).i00"   => [
					"verbose"  => "probability (%)",
					"possible" => [],
				],
				"clubLead.referral"            => [
					"verbose"  => "referral type",
					"possible" => [],
				],
				"clubLead.site.name"           => [
					"verbose"  => "site",
					"possible" => [
						"club",
						"site",
					],
				],
				"clubLead.site.code"           => [
					"verbose"  => "site code",
					"possible" => [],
				],
				"clubLead.site.siteType.value" => [
					"verbose"  => "site type",
					"possible" => [],
				],
				"clubLead.customData(0).tx03"  => [
					"verbose"  => "spouse",
					"possible" => [],
				],
				"clubLead.uniqueId"            => [
					"verbose"  => "unique id",
					"possible" => [],
				],
			],
			"owner"           => [
				"clubLead.owner.emailAddress" => [
					"verbose"  => "Owner Email",
					"possible" => [
						"owner",
					],
				],
				"clubLead.owner.firstName"    => [
					"verbose"  => "Owner First Name",
					"possible" => [],
				],
				"clubLead.owner.initials"     => [
					"verbose"  => "Owner Initials",
					"possible" => [],
				],
				"clubLead.owner.lastName"     => [
					"verbose"  => "Owner Last Name",
					"possible" => [],
				],
				"clubLead.owner.username"     => [
					"verbose"  => "Owner Username",
					"possible" => [],
				],
			],
			"primary-contact" => [
				"clubLead.contact.uniqueId"                => [
					"verbose"  => "Contact Unique ID",
					"possible" => [],
				],
				"clubLead.contact.email"                   => [
					"verbose"  => "Primary Contact Email",
					"possible" => [
						"email",
					],
				],
				"clubLead.contact.firstName"               => [
					"verbose"  => "Primary Contact First Name",
					"possible" => [
						"first_name",
					],
				],
				"clubLead.contact.homePhone"               => [
					"verbose"  => "Primary Contact Home Phone",
					"possible" => [
						"phone",
						"telephone",
						"phone_number",
						"phone_no",
					],
				],
				"clubLead.contact.lastName"                => [
					"verbose"  => "Primary Contact Last Name",
					"possible" => [
						"last_name",
					],
				],
				"clubLead.contact.mailingAddress.city"     => [
					"verbose"  => "Primary Contact Mailing Address City",
					"possible" => [],
				],
				"clubLead.contact.mailingAddress.country"  => [
					"verbose"  => "Primary Contact Mailing Address Country",
					"possible" => [],
				],
				"clubLead.contact.mailingAddress.address1" => [
					"verbose"  => "Primary Contact Mailing Address Line 1",
					"possible" => [],
				],
				"clubLead.contact.mailingAddress.address2" => [
					"verbose"  => "Primary Contact Mailing Address Line 2",
					"possible" => [],
				],
				"clubLead.contact.mailingAddress.state"    => [
					"verbose"  => "Primary Contact Mailing Address State",
					"possible" => [],
				],
				"clubLead.contact.mailingAddress.zipCode"  => [
					"verbose"  => "Primary Contact Mailing Address Zip Code",
					"possible" => [
						"zip",
						"Zip",
					],
				],
				"clubLead.contact.mobilePhone"             => [
					"verbose"  => "Primary Contact Mobile Phone",
					"possible" => [],
				],
				"clubLead.contact.workPhone"               => [
					"verbose"  => "Primary Contact Work Phone",
					"possible" => [],
				],
			],
			"salesperson"     => [
				"clubLead.salesperson.emailAddress" => [
					"verbose"  => "Salesperson Email",
					"possible" => [
						"salesperson",
					],
				],
				"clubLead.salesperson.firstName"    => [
					"verbose"  => "Salesperson First Name",
					"possible" => [],
				],
				"clubLead.salesperson.lastName"     => [
					"verbose"  => "Salesperson Last Name",
					"possible" => [],
				],
				"clubLead.salesperson.username"     => [
					"verbose"  => "Salesperson Username",
					"possible" => [],
				],
			]
		],
		"event"  => [
			"event-lead"      => [
				"lead.billingNotes"        => [
					"verbose"  => "Billing Notes",
					"possible" => [],
				],
				"lead.budget"              => [
					"verbose"  => "Budget",
					"possible" => [],
				],
				"lead.customData(0).tx02"  => [
					"verbose"  => "Client ID",
					"possible" => [],
				],
				"lead.customData(0).tx03"  => [
					"verbose"  => "Company / Title",
					"possible" => [
						"company_title"
					],
				],
				"lead.customData(0).b00"   => [
					"verbose"  => "Dates Flexible",
					"possible" => [
						"yes_no"
					],
				],
				"lead.decisionDate"        => [
					"verbose"  => "Decision Date",
					"possible" => [],
				],
				"lead.description"         => [
					"verbose"  => "Description",
					"possible" => [],
				],
				"lead.division.name"       => [
					"verbose"  => "Division",
					"possible" => [
						"division",
						"divison",
					],
				],
				"lead.customData(0).tx01"  => [
					"verbose"  => "End Date",
					"possible" => [],
				],
				"lead.endTime"             => [
					"verbose"  => "End Time",
					"possible" => [],
				],
				"lead.estimatedAttendance" => [
					"verbose"  => "Estimated Attendance",
					"possible" => [
						//"number"
					],
				],
				"lead.eventDate"           => [
					"verbose"  => "Event Date",
					"possible" => [],
				],
				"lead.eventDate_verbose"   => [
					"verbose"  => "Event Day/Date",
					"possible" => [],
				],
				"lead.customData(0).tx00"  => [
					"verbose"  => "Event Information",
					"possible" => [],
				],
				"lead.leadNotes.notes"     => [
					"verbose"  => "Event Lead Notes",
					"possible" => [],
				],
				"lead.functionType"        => [
					"verbose"  => "Function Type",
					"possible" => [],
				],
				"lead.eventType"           => [
					"verbose"  => "Lead Event Type",
					"possible" => [],
				],
				"lead.name"                => [
					"verbose"  => "Lead Name",
					"possible" => [
						"lead_name"
					],
				],
				"lead.leadStatus"          => [
					"verbose"  => "Lead Status",
					"possible" => [],
					"values"   => [
						"new" => "New",
					],
				],
				"lead.leadType"            => [
					"verbose"  => "Lead Type",
					"possible" => [
						//"sub_type"
					],
				],
				"lead.locations"           => [
					"verbose"  => "Location",
					"possible" => [],
				],
				"lead.customData(0).tx05"  => [
					"verbose"  => "Medium",
					"possible" => [],
				],
				"lead.probability"         => [
					"verbose"  => "Probability",
					"possible" => [],
				],
				"lead.referral"            => [
					"verbose"  => "Referral Type",
					"possible" => [],
				],
				"lead.site.name"           => [
					"verbose"  => "Site",
					"possible" => [
						"club",
						"site",
					],
				],
				"lead.site.code"           => [
					"verbose"  => "Site Code",
					"possible" => [],
				],
				"lead.site.siteType.value" => [
					"verbose"  => "Site Type",
					"possible" => [],
				],
				"lead.customData(0).tx04"  => [
					"verbose"  => "Source",
					"possible" => [],
				],
				"lead.eventTime"           => [
					"verbose"  => "Start Time",
					"possible" => [],
				],
				"lead.uniqueId"            => [
					"verbose"  => "Unique ID",
					"possible" => [],
				],
				"lead.customData(0).tx06"  => [
					"verbose"  => "Preferred Event Date",
					"possible" => [
						"preferred_event_date"
					],
				],
				"lead.customData(1).tx00"  => [
					"verbose"  => "Lead type",
					"possible" => [
						"sub_type",
					],
				],
				"lead.customData(0).tx07"  => [
					"verbose"  => "Campaign Attribution",
					"possible" => [
						"campaign_attribution",
						"campaign",
					],
				],
				"lead.customData(0).tx08"   => [
					"verbose"  => "Expected Number of Guests",
					"possible" => [
						"expected_number_of_guests",
						"number",
					],
				],

			],
			"owner"           => [
				"lead.owner.emailAddress" => [
					"verbose"  => "Owner Email",
					"possible" => [
						"owner"
					],
				],
				"lead.owner.firstName"    => [
					"verbose"  => "Owner First Name",
					"possible" => [],
				],
				"lead.owner.initials"     => [
					"verbose"  => "Owner Initials",
					"possible" => [],
				],
				"lead.owner.lastName"     => [
					"verbose"  => "Owner Last Name",
					"possible" => [],
				],
				"lead.owner.username"     => [
					"verbose"  => "Owner Username",
					"possible" => [],
				],
			],
			"primary-contact" => [
				"lead.contact.email"                   => [
					"verbose"  => "Primary Contact Email",
					"possible" => [
						"email",
					],
				],
				"lead.contact.firstName"               => [
					"verbose"  => "Primary Contact First Name",
					"possible" => [
						"first_name",
					],
				],
				"lead.contact.homePhone"               => [
					"verbose"  => "Primary Contact Home Phone",
					"possible" => [
						"phone",
						"telephone",
						"phone_number",
						"phone_no",

					],
				],
				"lead.contact.lastName"                => [
					"verbose"  => "Primary Contact Last Name",
					"possible" => [
						"last_name",
					],
				],
				"lead.contact.mailingAddress.city"     => [
					"verbose"  => "Primary Contact Mailing Address City",
					"possible" => [],
				],
				"lead.contact.mailingAddress.country"  => [
					"verbose"  => "Primary Contact Mailing Address Country",
					"possible" => [],
				],
				"lead.contact.mailingAddress.address1" => [
					"verbose"  => "Primary Contact Mailing Address Line 1",
					"possible" => [],
				],
				"lead.contact.mailingAddress.address2" => [
					"verbose"  => "Primary Contact Mailing Address Line 2",
					"possible" => [],
				],
				"lead.contact.mailingAddress.state"    => [
					"verbose"  => "Primary Contact Mailing Address State",
					"possible" => [],
				],
				"lead.contact.mailingAddress.zipCode"  => [
					"verbose"  => "Primary Contact Mailing Address Zip Code",
					"possible" => [
						"zip",
						"Zip",
					],
				],
				"lead.contact.mobilePhone"             => [
					"verbose"  => "Primary Contact Mobile Phone",
					"possible" => [],
				],
				"lead.contact.workPhone"               => [
					"verbose"  => "Primary Contact Work Phone",
					"possible" => [],
				],
			],
			"salesperson"     => [
				"lead.salesperson.emailAddress" => [
					"verbose"  => "Salesperson Email",
					"possible" => [
						"salesperson",
					],
				],
				"lead.salesperson.firstName"    => [
					"verbose"  => "Salesperson First Name",
					"possible" => [],
				],
				"lead.salesperson.lastName"     => [
					"verbose"  => "Salesperson Last Name",
					"possible" => [],
				],
				"lead.salesperson.username"     => [
					"verbose"  => "Salesperson Username",
					"possible" => [],
				],
			]
		]
	]
];
?>