<?php
return [
	"sites"  => [
		"IHGC"   => "Iron Horse Golf Course",
		"410"    => "Hunt Valley Golf Club",
		"229"    => "Tatum Ranch Golf Club",
		"CCGC"   => "Clear Creek Golf Club",
		"PDGC"   => "Painted Desert Golf Club",
		"PHGC"   => "Park Hill Golf Club",
		"MGC"    => "Meadowlark Golf Course",
		"GCACR"  => "Golf Club at Cinco Ranch",
		"RFGC"   => "Ruffled Feathers Golf Course",
		"MCGC"   => "Mill Creek Golf Club",
		"411"    => "Montgomery Country Club",
		"401"    => "LakeRidge Country Club",
		"CGC"    => "Cowboys Golf Club",
		"406"    => "Weymouth Country Club",
		"416"    => "TPC Valencia",
		"702"    => "LaCentre",
		"228"    => "Superstition Springs Golf Club",
		"DPGC"   => "Desert Pines Golf Club",
		"673"    => "Stonecreek Golf Club",
		"DLBGC"  => "David L. Baker Golf Course",
		"412"    => "Broad Bay Golf Club (Traditions)",
		"423"    => "The Club at Snoqualmie Ridge",
		"428"    => "Arrowhead Country Club",
		"CAFP"   => "Courses at Forest Park",
		"LVGC"   => "Las Vegas Golf Club",
		"242"    => "Kokopelli Golf Course",
		"WTGC"   => "Whitetail Ridge Golf Club",
		"424"    => "Tartan Fields",
		"405"    => "Fox Meadow Country Club",
		"CSGC"   => "Canyon Springs Golf Club",
		"TCC"    => "TallGrass Country Club",
		"703"    => "LPGA International",
		"LRGC"   => "Los Robles Golf Course",
		"425"    => "Ruby Hill Golf Club",
		"PGC"    => "Plantation Golf Club",
		"429"    => "The Legend at Arrowhead",
		"427"    => "Ancala Country Club",
		"TGCAFC" => "The Golf Club Fossil Creek",
		"DCGC"   => "Deer Creek Golf Club",
		"TGC"    => "Tamarack Golf Club",
		"422"    => "Pinery Country Club",
		"SHGC"   => "Shandin Hills Golf Club",
		"418"    => "Valencia Country Club",
		"498"    => "Eagle Brook Country Club",
		"MOGC"   => "Majestic Oaks Golf Club",
		"AGC"    => "Arrowhead Golf Club",
		"487"    => "Meadowbrook Country Club",
		"407"    => "Signature of Solon Country Club",
		"421"    => "The Club at Pradera",
		"BCGC"   => "Bear Creek Golf Club",
		"TCGC"   => "The Golf Club at Twin Creeks",
		"227"    => "Raven Phoenix Golf Club",
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
					"unbounce" => [
						"campaign_attribution",
						"campaign",
					],
				],
				"clubLead.club"                => [
					"verbose"  => "club name",
					"unbounce" => [
						"club",
						"site",
					],
				],
				"clubLead.customData(0).tx02"  => [
					"verbose"  => "do not use - inactive custom field",
					"unbounce" => [],
				],
				"clubLead.decisionDate"        => [
					"verbose"  => "decision date",
					"unbounce" => [],
				],
				"clubLead.description"         => [
					"verbose"  => "description",
					"unbounce" => [],
				],
				"clubLead.division.name"       => [
					"verbose"  => "division",
					"unbounce" => [
						"division",
						"divison",
					],
				],
				"clubLead.name"                => [
					"verbose"  => "lead name",
					"unbounce" => [],
				],
				"clubLead.customData(0).o00"   => [
					"verbose"  => "lead source",
					"unbounce" => [],
				],
				"clubLead.customData(0).tx04"  => [
					"verbose"  => "lead source details",
					"unbounce" => [],
				],
				"clubLead.leadStatus"          => [
					"verbose"  => "lead status",
					"unbounce" => [],
					"values"   => [
						"new" => "1 New",
					],
				],
				"clubLead.leadType"            => [
					"verbose"  => "lead type",
					"unbounce" => [],
				],
				"clubLead.customData(0).tx05"  => [
					"verbose"  => "lead/membership description",
					"unbounce" => [],
				],
				"clubLead.clubLeadNotes.notes" => [
					"verbose"  => "member lead notes",
					"unbounce" => [],
				],
				"clubLead.membershipTypeList"  => [
					"verbose"  => "new membership type",
					"unbounce" => [],
				],
				"clubLead.customData(0).tx00"  => [
					"verbose"  => "notes",
					"unbounce" => [],
				],
				"clubLead.customData(0).o02"   => [
					"verbose"  => "objections",
					"unbounce" => [],
				],
				"clubLead.customData(0).i00"   => [
					"verbose"  => "probability (%)",
					"unbounce" => [],
				],
				"clubLead.referral"            => [
					"verbose"  => "referral type",
					"unbounce" => [],
				],
				"clubLead.site.name"           => [
					"verbose"  => "site",
					"unbounce" => [
						"club",
						"site",
					],
				],
				"clubLead.site.code"           => [
					"verbose"  => "site code",
					"unbounce" => [],
				],
				"clubLead.site.siteType.value" => [
					"verbose"  => "site type",
					"unbounce" => [],
				],
				"clubLead.customData(0).tx03"  => [
					"verbose"  => "spouse",
					"unbounce" => [],
				],
				"clubLead.uniqueId"            => [
					"verbose"  => "unique id",
					"unbounce" => [],
				],
			],
			"owner"           => [
				"clubLead.owner.emailAddress" => [
					"verbose"  => "Owner Email",
					"unbounce" => [
						"owner",
					],
				],
				"clubLead.owner.firstName"    => [
					"verbose"  => "Owner First Name",
					"unbounce" => [],
				],
				"clubLead.owner.initials"     => [
					"verbose"  => "Owner Initials",
					"unbounce" => [],
				],
				"clubLead.owner.lastName"     => [
					"verbose"  => "Owner Last Name",
					"unbounce" => [],
				],
				"clubLead.owner.username"     => [
					"verbose"  => "Owner Username",
					"unbounce" => [],
				],
			],
			"primary-contact" => [
				"clubLead.contact.uniqueId"                => [
					"verbose"  => "Contact Unique ID",
					"unbounce" => [],
				],
				"clubLead.contact.email"                   => [
					"verbose"  => "Primary Contact Email",
					"unbounce" => [
						"email",
					],
				],
				"clubLead.contact.firstName"               => [
					"verbose"  => "Primary Contact First Name",
					"unbounce" => [
						"first_name",
					],
				],
				"clubLead.contact.homePhone"               => [
					"verbose"  => "Primary Contact Home Phone",
					"unbounce" => [
						"phone",
						"telephone",
						"phone_number",
					],
				],
				"clubLead.contact.lastName"                => [
					"verbose"  => "Primary Contact Last Name",
					"unbounce" => [
						"last_name",
					],
				],
				"clubLead.contact.mailingAddress.city"     => [
					"verbose"  => "Primary Contact Mailing Address City",
					"unbounce" => [],
				],
				"clubLead.contact.mailingAddress.country"  => [
					"verbose"  => "Primary Contact Mailing Address Country",
					"unbounce" => [],
				],
				"clubLead.contact.mailingAddress.address1" => [
					"verbose"  => "Primary Contact Mailing Address Line 1",
					"unbounce" => [],
				],
				"clubLead.contact.mailingAddress.address2" => [
					"verbose"  => "Primary Contact Mailing Address Line 2",
					"unbounce" => [],
				],
				"clubLead.contact.mailingAddress.state"    => [
					"verbose"  => "Primary Contact Mailing Address State",
					"unbounce" => [],
				],
				"clubLead.contact.mailingAddress.zipCode"  => [
					"verbose"  => "Primary Contact Mailing Address Zip Code",
					"unbounce" => [],
				],
				"clubLead.contact.mobilePhone"             => [
					"verbose"  => "Primary Contact Mobile Phone",
					"unbounce" => [],
				],
				"clubLead.contact.workPhone"               => [
					"verbose"  => "Primary Contact Work Phone",
					"unbounce" => [],
				],
			],
			"salesperson"     => [
				"clubLead.salesperson.emailAddress" => [
					"verbose"  => "Salesperson Email",
					"unbounce" => [
						"salesperson",
					],
				],
				"clubLead.salesperson.firstName"    => [
					"verbose"  => "Salesperson First Name",
					"unbounce" => [],
				],
				"clubLead.salesperson.lastName"     => [
					"verbose"  => "Salesperson Last Name",
					"unbounce" => [],
				],
				"clubLead.salesperson.username"     => [
					"verbose"  => "Salesperson Username",
					"unbounce" => [],
				],
			]
		],
		"event"  => [
			"event-lead"      => [
				"lead.billingNotes"        => [
					"verbose"  => "Billing Notes",
					"unbounce" => [],
				],
				"lead.budget"              => [
					"verbose"  => "Budget",
					"unbounce" => [],
				],
				"lead.customData(0).tx02"  => [
					"verbose"  => "Client ID",
					"unbounce" => [],
				],
				"lead.customData(0).tx03"  => [
					"verbose"  => "Company / Title",
					"unbounce" => [],
				],
				"lead.customData(0).b00"   => [
					"verbose"  => "Dates Flexible",
					"unbounce" => [],
				],
				"lead.decisionDate"        => [
					"verbose"  => "Decision Date",
					"unbounce" => [],
				],
				"lead.description"         => [
					"verbose"  => "Description",
					"unbounce" => [],
				],
				"lead.division.name"       => [
					"verbose"  => "Division",
					"unbounce" => [
						"division",
						"divison",
					],
				],
				"lead.customData(0).tx01"  => [
					"verbose"  => "End Date",
					"unbounce" => [],
				],
				"lead.endTime"             => [
					"verbose"  => "End Time",
					"unbounce" => [],
				],
				"lead.estimatedAttendance" => [
					"verbose"  => "Estimated Attendance",
					"unbounce" => [],
				],
				"lead.eventDate"           => [
					"verbose"  => "Event Date",
					"unbounce" => [],
				],
				"lead.eventDate_verbose"   => [
					"verbose"  => "Event Day/Date",
					"unbounce" => [],
				],
				"lead.customData(0).tx00"  => [
					"verbose"  => "Event Information",
					"unbounce" => [],
				],
				"lead.leadNotes.notes"     => [
					"verbose"  => "Event Lead Notes",
					"unbounce" => [],
				],
				"lead.functionType"        => [
					"verbose"  => "Function Type",
					"unbounce" => [],
				],
				"lead.eventType"           => [
					"verbose"  => "Lead Event Type",
					"unbounce" => [],
				],
				"lead.name"                => [
					"verbose"  => "Lead Name",
					"unbounce" => [],
				],
				"lead.leadStatus"          => [
					"verbose"  => "Lead Status",
					"unbounce" => [],
					"values"   => [
						"new" => "New",
					],
				],
				"lead.leadType"            => [
					"verbose"  => "Lead Type",
					"unbounce" => [],
				],
				"lead.locations"           => [
					"verbose"  => "Location",
					"unbounce" => [],
				],
				"lead.customData(0).tx05"  => [
					"verbose"  => "Medium",
					"unbounce" => [],
				],
				"lead.probability"         => [
					"verbose"  => "Probability",
					"unbounce" => [],
				],
				"lead.referral"            => [
					"verbose"  => "Referral Type",
					"unbounce" => [],
				],
				"lead.site.name"           => [
					"verbose"  => "Site",
					"unbounce" => [
						"club",
					],
				],
				"lead.site.code"           => [
					"verbose"  => "Site Code",
					"unbounce" => [],
				],
				"lead.site.siteType.value" => [
					"verbose"  => "Site Type",
					"unbounce" => [],
				],
				"lead.customData(0).tx04"  => [
					"verbose"  => "Source",
					"unbounce" => [],
				],
				"lead.eventTime"           => [
					"verbose"  => "Start Time",
					"unbounce" => [],
				],
				"lead.uniqueId"            => [
					"verbose"  => "Unique ID",
					"unbounce" => [],
				],
				"lead.customData(0).tx06"  => [
					"verbose"  => "Preferred Event Date",
					"unbounce" => [
						"preferred_event_date"
					],
				],
				"lead.customData(0).tx07"  => [
					"verbose"  => "Campaign Attribution",
					"unbounce" => [
						"campaign_attribution",
						"campaign",
					],
				],
			],
			"owner"           => [
				"lead.owner.emailAddress" => [
					"verbose"  => "Owner Email",
					"unbounce" => [
						"owner"
					],
				],
				"lead.owner.firstName"    => [
					"verbose"  => "Owner First Name",
					"unbounce" => [],
				],
				"lead.owner.initials"     => [
					"verbose"  => "Owner Initials",
					"unbounce" => [],
				],
				"lead.owner.lastName"     => [
					"verbose"  => "Owner Last Name",
					"unbounce" => [],
				],
				"lead.owner.username"     => [
					"verbose"  => "Owner Username",
					"unbounce" => [],
				],
			],
			"primary-contact" => [
				"lead.contact.email"                   => [
					"verbose"  => "Primary Contact Email",
					"unbounce" => [
						"email",
					],
				],
				"lead.contact.firstName"               => [
					"verbose"  => "Primary Contact First Name",
					"unbounce" => [
						"first_name",
					],
				],
				"lead.contact.homePhone"               => [
					"verbose"  => "Primary Contact Home Phone",
					"unbounce" => [
						"phone",
						"telephone",
						"phone_number",
					],
				],
				"lead.contact.lastName"                => [
					"verbose"  => "Primary Contact Last Name",
					"unbounce" => [
						"last_name",
					],
				],
				"lead.contact.mailingAddress.city"     => [
					"verbose"  => "Primary Contact Mailing Address City",
					"unbounce" => [],
				],
				"lead.contact.mailingAddress.country"  => [
					"verbose"  => "Primary Contact Mailing Address Country",
					"unbounce" => [],
				],
				"lead.contact.mailingAddress.address1" => [
					"verbose"  => "Primary Contact Mailing Address Line 1",
					"unbounce" => [],
				],
				"lead.contact.mailingAddress.address2" => [
					"verbose"  => "Primary Contact Mailing Address Line 2",
					"unbounce" => [],
				],
				"lead.contact.mailingAddress.state"    => [
					"verbose"  => "Primary Contact Mailing Address State",
					"unbounce" => [],
				],
				"lead.contact.mailingAddress.zipCode"  => [
					"verbose"  => "Primary Contact Mailing Address Zip Code",
					"unbounce" => [],
				],
				"lead.contact.mobilePhone"             => [
					"verbose"  => "Primary Contact Mobile Phone",
					"unbounce" => [],
				],
				"lead.contact.workPhone"               => [
					"verbose"  => "Primary Contact Work Phone",
					"unbounce" => [],
				],
			],
			"salesperson"     => [
				"lead.salesperson.emailAddress" => [
					"verbose"  => "Salesperson Email",
					"unbounce" => [
						"salesperson",
					],
				],
				"lead.salesperson.firstName"    => [
					"verbose"  => "Salesperson First Name",
					"unbounce" => [],
				],
				"lead.salesperson.lastName"     => [
					"verbose"  => "Salesperson Last Name",
					"unbounce" => [],
				],
				"lead.salesperson.username"     => [
					"verbose"  => "Salesperson Username",
					"unbounce" => [],
				],
			]
		]
	]
];
?>